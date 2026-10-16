<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Services\Audit\AuditTrailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessExpiredIncompleteGrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grades:expire-incomplete
                            {--days= : Custom grace period in days before incomplete grades expire (default from config)}
                            {--dry-run : Preview affected incomplete grades without modifying database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-expire incomplete grades (BL/T) past statutory grace days and convert to grade E with GPA recalculation';

    public function __construct(
        protected AuditTrailService $auditTrailService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) ($this->option('days') ?? config('academic.incomplete_grade_grace_days', 14));
        $isDryRun = (bool) $this->option('dry-run');
        $now = now();
        $cutoffDate = now()->subDays($days);

        $this->info("Memproses nilai tertunda / belum lengkap (BL/T) dengan batas toleransi {$days} hari (cutoff: {$cutoffDate->toDateTimeString()})...");

        $expiredRecords = KrsDetail::with(['krs.mahasiswa', 'kelasKuliah.mataKuliah'])
            ->whereIn('nilai_akhir_huruf', ['BL', 'T'])
            ->where(function ($q) use ($now, $cutoffDate) {
                $q->where('incomplete_expires_at', '<=', $now)
                  ->orWhere(function ($sub) use ($cutoffDate) {
                      $sub->whereNull('incomplete_expires_at')
                          ->where('updated_at', '<=', $cutoffDate);
                  });
            })
            ->where('is_incomplete_expired', false)
            ->get();

        if ($expiredRecords->isEmpty()) {
            $this->info("Tidak ditemukan nilai BL/T yang kadaluarsa.");
            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->warn("[DRY-RUN] Ditemukan {$expiredRecords->count()} nilai BL/T kadaluarsa yang memenuhi syarat konversi (toleransi: {$days} hari). Tidak ada perubahan data yang disimpan.");
            return self::SUCCESS;
        }

        $processedCount = 0;
        $affectedKrsIds = [];

        DB::transaction(function () use ($expiredRecords, &$processedCount, &$affectedKrsIds) {
            foreach ($expiredRecords as $detail) {
                $detail->nilai_akhir_huruf = 'E';
                $detail->bobot_mutu = 0.00;
                $detail->is_lulus = false;
                $detail->is_incomplete_expired = true;
                $detail->save();

                $affectedKrsIds[$detail->id_krs] = $detail->id_krs;
                $processedCount++;
            }

            // Recalculate IPS for affected KRS records
            foreach ($affectedKrsIds as $krsId) {
                $krs = Krs::with('details.kelasKuliah.mataKuliah')->find($krsId);
                if ($krs) {
                    $totalSks = 0;
                    $totalMutu = 0;
                    foreach ($krs->details as $d) {
                        $sks = (int) ($d->kelasKuliah?->mataKuliah?->sks_total ?? 0);
                        if ($sks > 0) {
                            $totalSks += $sks;
                            $totalMutu += ((float) $d->bobot_mutu * $sks);
                        }
                    }
                    if ($totalSks > 0) {
                        $krs->ips_lalu = round($totalMutu / $totalSks, 2);
                        $krs->save();
                    }
                }
            }
        });

        try {
            ActivityLog::record(
                'GRADES_EXPIRED_INCOMPLETE',
                "Mengonversi {$processedCount} nilai BL/T kadaluarsa menjadi E dan memperbarui IPS terkait."
            );

            $this->auditTrailService->record(
                userId: null,
                actionEvent: 'GRADES_EXPIRED_INCOMPLETE_PROCESSED',
                payload: [
                    'records_converted' => $processedCount,
                    'affected_krs_count' => count($affectedKrsIds),
                    'grace_days' => $days,
                ]
            );
        } catch (\Throwable $e) {
            $this->warn("Audit log warning: " . $e->getMessage());
        }

        $this->info("Berhasil mengonversi {$processedCount} nilai BL/T kadaluarsa menjadi nilai E dan memperbarui IPS terkait.");

        return self::SUCCESS;
    }
}
