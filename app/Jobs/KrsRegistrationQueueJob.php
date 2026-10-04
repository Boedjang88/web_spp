<?php

namespace App\Jobs;

use App\Services\Academic\SmartKrsService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class KrsRegistrationQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $idSiswa,
        public int $idKelasKuliah,
        public int $idTahunAkademik
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SmartKrsService $krsService): void
    {
        try {
            $krsService->enrollClassWithPessimisticLock(
                $this->idSiswa,
                $this->idKelasKuliah,
                $this->idTahunAkademik
            );

            Log::info("KRS Queue Job Success: Siswa {$this->idSiswa} -> Kelas {$this->idKelasKuliah}");
        } catch (Exception $e) {
            Log::warning("KRS Queue Job Failed: Siswa {$this->idSiswa} -> Kelas {$this->idKelasKuliah}. Error: " . $e->getMessage());
            throw $e;
        }
    }
}
