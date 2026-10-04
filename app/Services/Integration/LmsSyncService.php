<?php

namespace App\Services\Integration;

use App\Models\KelasKuliah;
use App\Models\LmsSyncQueue;
use Exception;
use Illuminate\Support\Facades\Http;

class LmsSyncService
{
    /**
     * Queue LMS course provisioning when an Academic Class is activated
     */
    public function queueCourseCreation(KelasKuliah $kelas): LmsSyncQueue
    {
        return LmsSyncQueue::create([
            'id_kelas_kuliah' => $kelas->id,
            'event_type' => 'CREATE_COURSE',
            'status' => 'QUEUED',
        ]);
    }

    /**
     * Process LMS Queue item (Moodle / Canvas REST API Mock) protected by Circuit Breaker
     */
    public function processQueueItem(LmsSyncQueue $queue): bool
    {
        $queue->update(['status' => 'PROCESSING']);

        return CircuitBreaker::call(
            'lms_moodle_canvas',
            function () use ($queue) {
                $kelas = $queue->kelasKuliah;
                $courseShortname = ($kelas->mataKuliah?->kode_mk ?? 'MK') . '-' . $kelas->nama_kelas;
                $courseFullname = ($kelas->mataKuliah?->nama_mk ?? 'Mata Kuliah') . ' (Kelas ' . $kelas->nama_kelas . ')';

                // Simulated LMS REST API response
                $mockLmsCourseId = 'LMS-CRS-' . $kelas->id . '-' . date('Y');

                $queue->update([
                    'status' => 'COMPLETED',
                    'lms_course_id' => $mockLmsCourseId,
                    'retry_count' => $queue->retry_count + 1,
                ]);

                return true;
            },
            function (\Throwable $e) use ($queue) {
                $queue->update([
                    'status' => 'FAILED',
                    'retry_count' => $queue->retry_count + 1,
                    'error_log' => $e->getMessage(),
                ]);

                return false;
            }
        );
    }
}
