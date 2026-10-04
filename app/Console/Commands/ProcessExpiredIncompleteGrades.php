<?php

namespace App\Console\Commands;

use App\Models\Krs;
use App\Models\KrsDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessExpiredIncompleteGrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grades:expire-incomplete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-expire incomplete grades (BL/T) past 30 days and convert to grade E with GPA recalculation';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memproses nilai tertunda / belum lengkap (BL/T) yang telah melampaui batas waktu 30 hari...');

        $now = now();
        $expiredRecords = KrsDetail::with(['krs.mahasiswa', 'kelasKuliah.mataKuliah'])
            ->whereIn('nilai_akhir_huruf', ['BL', 'T'])
            ->where('incomplete_expires_at', '<=', $now)
            ->where('is_incomplete_expired', false)
            ->get();

        if ($expiredRecords->isEmpty()) {
            $this->info('Tidak ditemukan nilai BL/T yang kadaluarsa.');
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

        $this->info("Berhasil mengonversi {$processedCount} nilai BL/T kadaluarsa menjadi nilai E dan memperbarui IPS terkait.");

        return self::SUCCESS;
    }
}
