<?php

namespace App\Console\Commands;

use App\Models\Siswa;
use App\Services\Academic\EarlyWarningService;
use Illuminate\Console\Command;

class AnalyzeEarlyWarningSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ews:analyze 
                            {--threshold=45 : Ambang batas skor risiko prediktif minimum untuk ditampilkan}
                            {--student= : Filter berdasarkan ID atau NISN mahasiswa tertentu}
                            {--queue : Dispatch proses analisis ke asynchronous background queue worker}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan student academic records and flag drop-out risks using Early Warning System';

    /**
     * Execute the console command.
     */
    public function handle(EarlyWarningService $service): int
    {
        $threshold = (int) $this->option('threshold');
        $studentIdentifier = $this->option('student');

        if ($studentIdentifier) {
            $student = Siswa::where('id', $studentIdentifier)
                ->orWhere('nisn', $studentIdentifier)
                ->orWhere('nis', $studentIdentifier)
                ->first();

            if (!$student) {
                $this->error("Mahasiswa dengan identifier '{$studentIdentifier}' tidak ditemukan.");
                return self::FAILURE;
            }

            $this->info("Memeriksa status EWS untuk: {$student->nama} ({$student->nisn})...");
            $risk = $service->calculatePredictiveDropoutRiskScore($student);
            $service->analyzeStudent($student);

            $this->table(
                ['Indikator', 'Detail', 'Skor Risiko'],
                [
                    ['Presensi (Bobot 35%)', "Kehadiran: {$risk['breakdown']['presensi']['rate_percent']}% ({$risk['breakdown']['presensi']['attended']}/{$risk['breakdown']['presensi']['total_meetings']})", "{$risk['breakdown']['presensi']['risk_score']} / 35"],
                    ['Nilai (Bobot 35%)', "IPK/IPS Rata-rata: {$risk['breakdown']['nilai']['gpa']}", "{$risk['breakdown']['nilai']['risk_score']} / 35"],
                    ['Tagihan (Bobot 30%)', "Tunggakan: Rp " . number_format($risk['breakdown']['tagihan']['unpaid_amount'], 0, ',', '.'), "{$risk['breakdown']['tagihan']['risk_score']} / 30"],
                    ['TOTAL SKOR', "Kategori: {$risk['risk_category']}", "{$risk['total_risk_score']} / 100"],
                ]
            );

            return self::SUCCESS;
        }

        if ($this->option('queue')) {
            $this->info('Dispatching EWS analysis jobs ke background queue worker...');
            $dispatchedCount = 0;
            Siswa::where('status_kelulusan', 'Aktif')
                ->chunkById(100, function ($students) use (&$dispatchedCount) {
                    foreach ($students as $student) {
                        \App\Jobs\AnalyzeStudentDropoutRisk::dispatch($student->id);
                        $dispatchedCount++;
                    }
                });

            $this->info("Berhasil mendispatch {$dispatchedCount} pekerjaan EWS ke background queue.");
            return self::SUCCESS;
        }

        $this->info('Memulai pemindaian Early Warning System (EWS) untuk seluruh mahasiswa aktif...');
        $result = $service->analyzeAllStudents();

        // Calculate and collect predictive risk scores
        $students = Siswa::where('status_kelulusan', 'Aktif')->get();
        $breakdownRows = [];

        foreach ($students as $student) {
            $risk = $service->calculatePredictiveDropoutRiskScore($student);
            if ($risk['total_risk_score'] >= $threshold) {
                $breakdownRows[] = [
                    $student->nisn ?? $student->nis ?? (string) $student->id,
                    $student->nama,
                    "{$risk['breakdown']['presensi']['rate_percent']}% ({$risk['breakdown']['presensi']['risk_score']} pt)",
                    "{$risk['breakdown']['nilai']['gpa']} ({$risk['breakdown']['nilai']['risk_score']} pt)",
                    ($risk['breakdown']['tagihan']['has_arrears'] ? 'Ada Tunggakan' : 'Lunas') . " ({$risk['breakdown']['tagihan']['risk_score']} pt)",
                    $risk['total_risk_score'],
                    $risk['risk_category'],
                ];
            }
        }

        $this->table(
            ['Metrik EWS', 'Nilai'],
            [
                ['Total Mahasiswa Dievaluasi', $result['total_evaluated']],
                ['Jumlah Mahasiswa Terindikasi Risiko (EWS Logs)', $result['flagged_students_count']],
                ['Total Peringatan/Log Dibuat', $result['total_violations']],
                ['Mahasiswa di atas Threshold (' . $threshold . ')', count($breakdownRows)],
            ]
        );

        if (!empty($breakdownRows)) {
            $this->newLine();
            $this->warn("Daftar Mahasiswa Terindikasi Risiko Prediktif (Skor >= {$threshold}):");
            $this->table(
                ['NIM/NISN', 'Nama', 'Presensi', 'Nilai', 'Tagihan', 'Total Skor', 'Kategori'],
                $breakdownRows
            );
        } else {
            $this->info("Tidak ada mahasiswa yang melampaui ambang batas risiko {$threshold}.");
        }

        return self::SUCCESS;
    }
}
