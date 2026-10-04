<?php

namespace App\Services\Lms;

use App\Models\Assignment;
use App\Models\CourseMaterial;
use App\Models\Siswa;
use App\Models\Submission;
use App\Services\Security\UploadSecurityGateway;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LmsService
{
    public function __construct(
        protected UploadSecurityGateway $securityGateway
    ) {}

    /**
     * Get drip-fed published course materials for a class.
     */
    public function getAvailableMaterials(int $kelasKuliahId)
    {
        return CourseMaterial::where('id_kelas_kuliah', $kelasKuliahId)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('publish_at')
                  ->orWhere('publish_at', '<=', now());
            })
            ->orderBy('minggu_ke', 'asc')
            ->get();
    }

    /**
     * Create or publish a new course material.
     */
    public function createMaterial(array $data, ?UploadedFile $file = null): CourseMaterial
    {
        $filePath = $data['file_path'] ?? '';
        $originalFilename = $data['original_filename'] ?? 'material.pdf';
        $fileSize = $data['file_size'] ?? 0;
        $fileType = $data['file_type'] ?? 'pdf';

        if ($file) {
            $this->securityGateway->assertSafeFile($file, ['pdf', 'docx', 'zip', 'png', 'jpg', 'jpeg']);
            $originalFilename = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getClientOriginalExtension();
            $storedPath = $file->store('lms/materials', 'local');
            $filePath = $storedPath;
        }

        return CourseMaterial::create([
            'id_kelas_kuliah' => $data['id_kelas_kuliah'],
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'minggu_ke' => $data['minggu_ke'] ?? 1,
            'publish_at' => $data['publish_at'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Create a new class assignment.
     */
    public function createAssignment(array $data): Assignment
    {
        return Assignment::create([
            'id_kelas_kuliah' => $data['id_kelas_kuliah'],
            'target_kelas_ids' => $data['target_kelas_ids'] ?? null,
            'judul' => $data['judul'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'attachment_path' => $data['attachment_path'] ?? null,
            'komponen_penilaian' => $data['komponen_penilaian'] ?? 'TUGAS',
            'bobot_persen' => $data['bobot_persen'] ?? 10.00,
            'deadline_at' => $data['deadline_at'],
            'allow_late_submission' => $data['allow_late_submission'] ?? false,
            'late_grace_minutes' => $data['late_grace_minutes'] ?? 0,
            'is_published' => $data['is_published'] ?? true,
            'is_anonymous_grading' => $data['is_anonymous_grading'] ?? false,
        ]);
    }

    /**
     * Submit an assignment solution with millisecond timestamping and device fingerprinting.
     *
     * @param Assignment $assignment
     * @param \App\Models\Mahasiswa|\App\Models\Siswa $siswa
     * @param UploadedFile|string $file
     * @param array $clientContext ['ip' => string, 'user_agent' => string]
     * @return Submission
     */
    public function submitAssignment(Assignment $assignment, object $siswa, $file, array $clientContext = []): Submission
    {
        $now = now();

        if (!$assignment->canAcceptSubmission($now)) {
            throw new DomainException('Batas waktu pengumpulan tugas telah berakhir dan pengumpulan terlambat tidak diizinkan.');
        }

        // 1. Inspect Binary Header and Virus/Shell signatures
        $this->securityGateway->assertSafeFile($file, ['pdf', 'docx', 'zip']);

        $microtimeNow = microtime(true);
        $clientIp = $clientContext['ip'] ?? request()->ip() ?? '127.0.0.1';
        $userAgent = $clientContext['user_agent'] ?? request()->userAgent() ?? 'CLI/System';
        $deviceFingerprint = hash('sha256', $clientIp . '|' . $userAgent);
        $submissionToken = hash('sha256', $assignment->id . '-' . $siswa->id . '-' . $microtimeNow . '-' . Str::random(16));

        $originalFilename = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);
        $fileMime = $file instanceof UploadedFile ? ($file->getMimeType() ?? 'application/octet-stream') : mime_content_type($file);
        $fileSize = $file instanceof UploadedFile ? $file->getSize() : filesize($file);

        $storedPath = $file instanceof UploadedFile
            ? $file->store("lms/submissions/{$assignment->id}", 'local')
            : $file;

        $isLate = $assignment->isTimestampLate($now);

        return DB::transaction(function () use (
            $assignment,
            $siswa,
            $storedPath,
            $originalFilename,
            $fileMime,
            $fileSize,
            $now,
            $microtimeNow,
            $submissionToken,
            $deviceFingerprint,
            $clientIp,
            $isLate
        ) {
            return Submission::updateOrCreate(
                [
                    'id_assignment' => $assignment->id,
                    'id_siswa' => $siswa->id,
                ],
                [
                    'file_path' => $storedPath,
                    'original_filename' => $originalFilename,
                    'file_mime' => $fileMime,
                    'file_size' => $fileSize,
                    'submitted_at' => $now,
                    'submission_microtime' => $microtimeNow,
                    'submission_token' => $submissionToken,
                    'device_fingerprint' => $deviceFingerprint,
                    'client_ip' => $clientIp,
                    'is_late' => $isLate,
                ]
            );
        });
    }
}
