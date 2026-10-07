<?php

namespace App\Services\Integration;

use App\Models\KelasKuliah;
use App\Models\LmsSyncQueue;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LmsSyncService
{
    protected string $driver;
    protected string $apiUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->driver = (string) config('services.lms.driver', 'mock'); // moodle, canvas, or mock
        $this->apiUrl = (string) config('services.lms.url', 'https://lms.campus.ac.id');
        $this->apiToken = (string) config('services.lms.token', '');
    }

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
     * Process LMS Queue item (Moodle / Canvas REST API / Mock) protected by Circuit Breaker
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

                $lmsCourseId = match ($this->driver) {
                    'moodle' => $this->createMoodleCourse($courseShortname, $courseFullname, $kelas->id),
                    'canvas' => $this->createCanvasCourse($courseShortname, $courseFullname, $kelas->id),
                    default => 'LMS-CRS-' . $kelas->id . '-' . date('Y'),
                };

                $queue->update([
                    'status' => 'COMPLETED',
                    'lms_course_id' => $lmsCourseId,
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

    /**
     * Provision course in Moodle WebService REST API
     */
    protected function createMoodleCourse(string $shortName, string $fullName, int $kelasId): string
    {
        if (empty($this->apiToken)) {
            return 'MOODLE-MOCK-' . $kelasId . '-' . strtoupper(bin2hex(random_bytes(4)));
        }

        $endpoint = rtrim($this->apiUrl, '/') . '/webservice/rest/server.php';
        $response = Http::timeout(10)->post($endpoint, [
            'wstoken' => $this->apiToken,
            'wsfunction' => 'core_course_create_courses',
            'moodlewsrestformat' => 'json',
            'courses' => [
                [
                    'fullname' => $fullName,
                    'shortname' => $shortName,
                    'categoryid' => 1,
                    'idnumber' => 'CRS-' . $kelasId,
                ]
            ]
        ]);

        $data = $response->json();
        if (isset($data[0]['id'])) {
            return (string) $data[0]['id'];
        }

        Log::warning('Moodle API response did not contain course ID. Using fallback.', ['res' => $data]);
        return 'MOODLE-CRS-' . $kelasId;
    }

    /**
     * Provision course in Canvas LMS REST API
     */
    protected function createCanvasCourse(string $shortName, string $fullName, int $kelasId): string
    {
        if (empty($this->apiToken)) {
            return 'CANVAS-MOCK-' . $kelasId . '-' . strtoupper(bin2hex(random_bytes(4)));
        }

        $endpoint = rtrim($this->apiUrl, '/') . '/api/v1/accounts/1/courses';
        $response = Http::timeout(10)->withHeaders([
            'Authorization' => "Bearer {$this->apiToken}",
        ])->post($endpoint, [
            'course' => [
                'name' => $fullName,
                'course_code' => $shortName,
                'sis_course_id' => 'CRS-' . $kelasId,
            ]
        ]);

        $data = $response->json();
        if (isset($data['id'])) {
            return (string) $data['id'];
        }

        Log::warning('Canvas API response did not contain course ID. Using fallback.', ['res' => $data]);
        return 'CANVAS-CRS-' . $kelasId;
    }
}
