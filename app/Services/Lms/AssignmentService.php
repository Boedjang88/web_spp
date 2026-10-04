<?php

namespace App\Services\Lms;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    /**
     * Distribute an assignment across multiple parallel classes atomically.
     *
     * @param array $classIds
     * @param array $assignmentData
     * @return EloquentCollection
     */
    public function distributeToParallelClasses(array $classIds, array $assignmentData): EloquentCollection
    {
        return DB::transaction(function () use ($classIds, $assignmentData) {
            $createdAssignments = new EloquentCollection();

            foreach ($classIds as $classId) {
                $payload = array_merge($assignmentData, [
                    'id_kelas_kuliah' => $classId,
                    'target_kelas_ids' => $classIds,
                ]);

                $assignment = Assignment::create($payload);
                $createdAssignments->push($assignment);
            }

            return $createdAssignments;
        });
    }

    /**
     * Get submissions for an assignment with optional Anonymous Grading Mode.
     *
     * @param Assignment $assignment
     * @return Collection
     */
    public function getMaskedSubmissions(Assignment $assignment): Collection
    {
        $submissions = Submission::with(['mahasiswa', 'siswa'])
            ->where('id_assignment', $assignment->id)
            ->get();

        if (!$assignment->is_anonymous_grading) {
            return $submissions->map(function ($sub) {
                $student = $sub->mahasiswa ?? $sub->siswa;
                return [
                    'id' => $sub->id,
                    'id_mahasiswa' => $sub->id_mahasiswa ?? $sub->id_siswa,
                    'nama_mahasiswa' => $student?->nama ?? 'Mahasiswa',
                    'nim' => $student?->nim ?? $student?->nisn ?? '-',
                    'file_path' => $sub->file_path,
                    'original_filename' => $sub->original_filename,
                    'submitted_at' => $sub->submitted_at,
                    'is_late' => $sub->is_late,
                    'nilai' => $sub->nilai,
                    'feedback' => $sub->feedback,
                    'catatan_dosen' => $sub->catatan_dosen,
                    'is_anonymous' => false,
                ];
            });
        }

        return $submissions->map(function ($sub) {
            $studentId = $sub->id_mahasiswa ?? $sub->id_siswa ?? $sub->id;
            $maskedTag = 'ANON-STUDENT-' . strtoupper(substr(hash('sha256', (string) $studentId), 0, 8));

            return [
                'id' => $sub->id,
                'masked_student_id' => $maskedTag,
                'nama_mahasiswa' => '[Disembunyikan / Anonymous Mode]',
                'nim' => '[Disembunyikan]',
                'file_path' => $sub->file_path,
                'original_filename' => $sub->original_filename,
                'submitted_at' => $sub->submitted_at,
                'is_late' => $sub->is_late,
                'nilai' => $sub->nilai,
                'feedback' => $sub->feedback,
                'catatan_dosen' => $sub->catatan_dosen,
                'is_anonymous' => true,
            ];
        });
    }
}
