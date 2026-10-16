<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\PresensiMahasiswa;
use App\Services\Audit\AuditTrailService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ArchiveAttendanceTelemetryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:archive-telemetry
                            {--days= : Number of retention days before coordinates are scrubbed (default: 180)}
                            {--dry-run : Preview affected attendance records without modifying database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrub and minimize high-frequency GPS telemetry (lat, long, device_fingerprint) on old attendance records while preserving academic attendance history (UU PDP No. 27/2022 compliance)';

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
        $days = (int) ($this->option('days') ?? config('academic.gps_attendance_retention_days', 180));
        $isDryRun = (bool) $this->option('dry-run');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Scanning attendance records older than {$days} days (cutoff: {$cutoffDate->toDateTimeString()})...");

        $query = PresensiMahasiswa::where(function ($q) use ($cutoffDate) {
            $q->where('waktu_hadir', '<', $cutoffDate)
              ->orWhere(function ($sub) use ($cutoffDate) {
                  $sub->whereNull('waktu_hadir')
                      ->where('created_at', '<', $cutoffDate);
              });
        })->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('latitude', '!=', 0)
                    ->orWhere('longitude', '!=', 0);
            })->orWhereNotNull('device_fingerprint');
        });

        $count = $query->count();

        if ($count === 0) {
            $this->info("No stale attendance telemetry records found requiring minimization.");
            return Command::SUCCESS;
        }

        if ($isDryRun) {
            $this->warn("[DRY-RUN] Found {$count} attendance records with active GPS coordinates older than {$days} days. No changes applied.");
            return Command::SUCCESS;
        }

        $this->info("Scrubbing GPS coordinates & device fingerprints for {$count} attendance records...");

        // Chunk update to avoid lock contention and memory exhaustion
        $updatedTotal = 0;
        PresensiMahasiswa::where(function ($q) use ($cutoffDate) {
            $q->where('waktu_hadir', '<', $cutoffDate)
              ->orWhere(function ($sub) use ($cutoffDate) {
                  $sub->whereNull('waktu_hadir')
                      ->where('created_at', '<', $cutoffDate);
              });
        })->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('latitude', '!=', 0)
                    ->orWhere('longitude', '!=', 0);
            })->orWhereNotNull('device_fingerprint');
        })->chunkById(200, function ($records) use (&$updatedTotal) {
            foreach ($records as $record) {
                $record->update([
                    'latitude' => 0.0,
                    'longitude' => 0.0,
                    'device_fingerprint' => null,
                ]);
                $updatedTotal++;
            }
        });

        // Record audit activity
        try {
            ActivityLog::record(
                'ATTENDANCE_TELEMETRY_ARCHIVED',
                "Scrubbed GPS telemetry for {$updatedTotal} attendance records older than {$days} days (UU PDP minimization)."
            );

            $this->auditTrailService->record(
                userId: null,
                actionEvent: 'ATTENDANCE_TELEMETRY_ARCHIVED',
                payload: [
                    'retention_days' => $days,
                    'cutoff_date' => $cutoffDate->toDateTimeString(),
                    'records_scrubbed' => $updatedTotal,
                ]
            );
        } catch (\Throwable $e) {
            $this->warn("Audit log warning: " . $e->getMessage());
        }

        $this->info("Successfully minimized telemetry for {$updatedTotal} records. Attendance history and grades remain 100% intact.");

        return Command::SUCCESS;
    }
}
