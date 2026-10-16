<?php

namespace App\Jobs;

use App\Models\Siswa;
use App\Services\Academic\EarlyWarningService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeStudentDropoutRisk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $studentId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(EarlyWarningService $service): void
    {
        try {
            $student = Siswa::find($this->studentId);
            if (!$student) {
                Log::warning("AnalyzeStudentDropoutRisk Job: Siswa ID {$this->studentId} not found.");
                return;
            }

            $service->analyzeStudent($student);
        } catch (Exception $e) {
            Log::error("AnalyzeStudentDropoutRisk Job failed for Siswa ID {$this->studentId}: " . $e->getMessage());
            throw $e;
        }
    }
}
