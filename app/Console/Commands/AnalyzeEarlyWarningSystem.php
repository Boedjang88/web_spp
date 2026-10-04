<?php

namespace App\Console\Commands;

use App\Services\Academic\EarlyWarningService;
use Illuminate\Console\Command;

class AnalyzeEarlyWarningSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ews:analyze';

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
        $this->info('Memulai pemindaian Early Warning System (EWS) untuk seluruh mahasiswa aktif...');

        $result = $service->analyzeAllStudents();

        $this->table(
            ['Metrik EWS', 'Nilai'],
            [
                ['Total Mahasiswa Dievaluasi', $result['total_evaluated']],
                ['Jumlah Mahasiswa Terindikasi Risiko', $result['flagged_students_count']],
                ['Total Peringatan/Log Dibuat', $result['total_violations']],
            ]
        );

        if ($result['flagged_students_count'] > 0) {
            $this->warn("Ditemukan {$result['flagged_students_count']} mahasiswa dengan risiko akademik. Notifikasi diteruskan ke Dosen PA terkait.");
        } else {
            $this->info('Seluruh mahasiswa berada dalam batas aman performa akademik.');
        }

        return self::SUCCESS;
    }
}
