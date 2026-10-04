<?php

namespace App\Services\Lms;

use App\Models\Assignment;
use App\Models\Guru;
use App\Models\KrsDetail;
use App\Models\Submission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LmsGradingService
{
    /**
     * Get submissions for an assignment, applying anonymous identity masking if enabled.
     */
    public function getSubmissions(Assignment $assignment, bool $forceAnonymous = false): Collection
    {
        $isAnonymous = $assignment->is_anonymous_grading || $forceAnonymous;

        $submissions = Submission::with(['mahasiswa'])
            ->where('id_assignment', $assignment->id)
            ->get();

        if ($isAnonymous) {
            return $submissions->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'masked_identifier' => $submission->masked_identity,
                    'file_path' => $submission->file_path,
                    'original_filename' => $submission->original_filename,
                    'file_size' => $submission->file_size,
                    'submitted_at' => $submission->submitted_at,
                    'is_late' => $submission->is_late,
                    'nilai' => $submission->nilai,
                    'feedback' => $submission->feedback,
                    'graded_at' => $submission->graded_at,
                    'is_anonymous' => true,
                ];
            });
        }

        return $submissions;
    }

    /**
     * Grade a student's submission and sync into the student's KrsDetail gradebook.
     */
    public function gradeSubmission(Submission $submission, float $nilai, ?string $feedback = null, ?Guru $dosen = null): Submission
    {
        return DB::transaction(function () use ($submission, $nilai, $feedback, $dosen) {
            $submission->nilai = $nilai;
            $submission->feedback = $feedback;
            $submission->graded_at = now();
            if ($dosen) {
                $submission->graded_by_dosen = $dosen->id;
            }
            $submission->save();

            // Sync with student's KrsDetail for this class
            $assignment = $submission->assignment;
            if ($assignment && $assignment->id_kelas_kuliah) {
                $this->syncKrsGrade($assignment->id_kelas_kuliah, $submission->id_siswa);
            }

            return $submission;
        });
    }

    /**
     * Sync and recalculate KrsDetail composite grade based on LMS submissions & weights.
     */
    public function syncKrsGrade(int $kelasKuliahId, int $siswaId): ?KrsDetail
    {
        $krsDetail = KrsDetail::where('id_kelas_kuliah', $kelasKuliahId)
            ->whereHas('krs', function ($q) use ($siswaId) {
                $q->where('id_siswa', $siswaId);
            })
            ->first();

        if (!$krsDetail) {
            return null;
        }

        // Calculate average score for TUGAS assignments
        $tugasAvg = Submission::whereHas('assignment', function ($q) use ($kelasKuliahId) {
            $q->where('id_kelas_kuliah', $kelasKuliahId)
              ->where('komponen_penilaian', 'TUGAS');
        })
        ->where('id_siswa', $siswaId)
        ->whereNotNull('nilai')
        ->avg('nilai');

        // Calculate average score for QUIZ assignments
        $quizAvg = Submission::whereHas('assignment', function ($q) use ($kelasKuliahId) {
            $q->where('id_kelas_kuliah', $kelasKuliahId)
              ->where('komponen_penilaian', 'QUIZ');
        })
        ->where('id_siswa', $siswaId)
        ->whereNotNull('nilai')
        ->avg('nilai');

        // Calculate average score for PRAKTIKUM assignments
        $prakAvg = Submission::whereHas('assignment', function ($q) use ($kelasKuliahId) {
            $q->where('id_kelas_kuliah', $kelasKuliahId)
              ->where('komponen_penilaian', 'PRAKTIKUM');
        })
        ->where('id_siswa', $siswaId)
        ->whereNotNull('nilai')
        ->avg('nilai');

        if ($tugasAvg !== null) {
            $krsDetail->nilai_tugas = round($tugasAvg, 2);
        }
        if ($quizAvg !== null) {
            $krsDetail->nilai_quiz = round($quizAvg, 2);
        }
        if ($prakAvg !== null) {
            $krsDetail->nilai_praktikum = round($prakAvg, 2);
        }

        // Standard Grade Calculation:
        // Kehadiran (10%), Tugas (20%), Quiz (15%), Praktikum (15%), UTS (20%), UAS (20%)
        $hadir = (float) ($krsDetail->nilai_kehadiran ?? 100);
        $tugas = (float) ($krsDetail->nilai_tugas ?? 0);
        $quiz = (float) ($krsDetail->nilai_quiz ?? 0);
        $prak = (float) ($krsDetail->nilai_praktikum ?? 0);
        $uts = (float) ($krsDetail->nilai_uts ?? 0);
        $uas = (float) ($krsDetail->nilai_uas ?? 0);

        $finalScore = ($hadir * 0.10) + ($tugas * 0.20) + ($quiz * 0.15) + ($prak * 0.15) + ($uts * 0.20) + ($uas * 0.20);
        $krsDetail->nilai_akhir_angka = round($finalScore, 2);

        // Map to Letter Grade & Mutu
        if ($finalScore >= 85) {
            $krsDetail->nilai_akhir_huruf = 'A';
            $krsDetail->bobot_mutu = 4.00;
            $krsDetail->is_lulus = true;
        } elseif ($finalScore >= 75) {
            $krsDetail->nilai_akhir_huruf = 'B';
            $krsDetail->bobot_mutu = 3.00;
            $krsDetail->is_lulus = true;
        } elseif ($finalScore >= 60) {
            $krsDetail->nilai_akhir_huruf = 'C';
            $krsDetail->bobot_mutu = 2.00;
            $krsDetail->is_lulus = true;
        } elseif ($finalScore >= 50) {
            $krsDetail->nilai_akhir_huruf = 'D';
            $krsDetail->bobot_mutu = 1.00;
            $krsDetail->is_lulus = false;
        } else {
            $krsDetail->nilai_akhir_huruf = 'E';
            $krsDetail->bobot_mutu = 0.00;
            $krsDetail->is_lulus = false;
        }

        $krsDetail->save();

        return $krsDetail;
    }
}
