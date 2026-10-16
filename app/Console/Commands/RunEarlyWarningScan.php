<?php

namespace App\Console\Commands;

use App\Jobs\AnalyzeStudentDropoutRisk;
use App\Models\Siswa;
use App\Services\Academic\EarlyWarningService;
use Illuminate\Console\Command;

class RunEarlyWarningScan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ews:scan 
                            {--sync : Jalankan pemindaian secara sinkron alih-alih background queue}
                            {--threshold=45 : Ambang batas skor risiko prediktif minimum}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch asynchronous chunked queue jobs for university EWS dropout risk scan';

    /**
     * Execute the console command.
     */
    public function handle(EarlyWarningService $service): int
    {
        $this->info('Memulai pemindaian Early Warning System (EWS)...');

        if ($this->option('sync')) {
            $this->info('Mode sinkron dipilih.');
            return $this->call('ews:analyze', [
                '--threshold' => $this->option('threshold'),
            ]);
        }

        $startTime = microtime(true);
        $dispatchedCount = 0;

        Siswa::where('status_kelulusan', 'Aktif')
            ->chunkById(100, function ($students) use (&$dispatchedCount) {
                foreach ($students as $student) {
                    AnalyzeStudentDropoutRisk::dispatch($student->id);
                    $dispatchedCount++;
                }
            });

        $elapsedSeconds = round(microtime(true) - $startTime, 3);
        $this->info("Berhasil mendispatch {$dispatchedCount} pekerjaan EWS ke antrean dalam {$elapsedSeconds} detik.");

        return self::SUCCESS;
    }
}
