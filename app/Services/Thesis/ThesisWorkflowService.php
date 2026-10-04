<?php

namespace App\Services\Thesis;

use App\Models\PembimbingSkripsi;
use App\Models\PenilaianSidang;
use App\Models\SidangSkripsi;
use App\Models\TugasAkhir;
use Exception;
use Illuminate\Support\Facades\DB;

class ThesisWorkflowService
{
    /**
     * Maximum active thesis advisees allowed per lecturer
     */
    public const MAX_ADVISOR_QUOTA = 10;

    /**
     * Assign Advisor to Student Thesis with Quota Enforcement
     *
     * @throws Exception
     */
    public function assignAdvisor(int $idTugasAkhir, int $idGuru, string $peran = 'Pembimbing Utama', int $urutan = 1): PembimbingSkripsi
    {
        return DB::transaction(function () use ($idTugasAkhir, $idGuru, $peran, $urutan) {
            $activeCount = PembimbingSkripsi::where('id_guru', $idGuru)
                ->whereHas('tugasAkhir', function ($q) {
                    $q->where('status_skripsi', '!=', 'Lulus Yudisium');
                })
                ->count();

            if ($activeCount >= self::MAX_ADVISOR_QUOTA) {
                throw new Exception("Kuota bimbingan dosen sudah penuh ({$activeCount}/" . self::MAX_ADVISOR_QUOTA . ' mahasiswa aktif).');
            }

            return PembimbingSkripsi::updateOrCreate(
                [
                    'id_tugas_akhir' => $idTugasAkhir,
                    'id_guru' => $idGuru,
                ],
                [
                    'peran' => $peran,
                    'urutan' => $urutan,
                ]
            );
        });
    }

    /**
     * Aggregate Examination Board Grades and Determine Graduation Outcome
     */
    public function finalizeExaminationBoard(int $idSidang): SidangSkripsi
    {
        return DB::transaction(function () use ($idSidang) {
            $sidang = SidangSkripsi::with('penilaians')->findOrFail($idSidang);
            $penilaians = $sidang->penilaians;

            if ($penilaians->isEmpty()) {
                throw new Exception('Belum ada nilai dari dewan penguji yang diinputkan.');
            }

            $totalScoreSum = 0;
            foreach ($penilaians as $penilaian) {
                // Calculate weighted score per examiner: 20% Presentation, 40% Mastery, 40% Methodology
                $score = ($penilaian->skor_presentasi * 0.20) +
                         ($penilaian->skor_penguasaan_materi * 0.40) +
                         ($penilaian->skor_metodologi_karya * 0.40);

                $penilaian->skor_total_penguji = round($score, 2);
                $penilaian->save();

                $totalScoreSum += $score;
            }

            $average = round($totalScoreSum / $penilaians->count(), 2);
            $sidang->nilai_rata_rata = $average;

            if ($average >= 80.00) {
                $sidang->nilai_huruf = 'A';
                $sidang->hasil_keputusan = 'Lulus Tanpa Revisi';
            } elseif ($average >= 70.00) {
                $sidang->nilai_huruf = 'B';
                $sidang->hasil_keputusan = 'Lulus Dengan Revisi';
            } elseif ($average >= 60.00) {
                $sidang->nilai_huruf = 'C';
                $sidang->hasil_keputusan = 'Lulus Dengan Revisi';
            } else {
                $sidang->nilai_huruf = 'E';
                $sidang->hasil_keputusan = 'Mengulang Sidang';
            }

            $sidang->save();

            // If Final Defense Passed, update Thesis state to Yudisium
            if (in_array($sidang->hasil_keputusan, ['Lulus Tanpa Revisi', 'Lulus Dengan Revisi'])) {
                $tugasAkhir = $sidang->tugasAkhir;
                $tugasAkhir->status_skripsi = 'Lulus Yudisium';
                $tugasAkhir->tgl_lulus_sidang = now()->toDateString();
                $tugasAkhir->save();
            }

            return $sidang;
        });
    }
}
