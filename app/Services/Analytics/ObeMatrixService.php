<?php

namespace App\Services\Analytics;

use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\KrsDetail;
use App\Models\NilaiObe;
use App\Models\Siswa;

class ObeMatrixService
{
    /**
     * Compute student CPL Competency Radar scores based on completed courses
     */
    public function computeStudentCplRadar(int $idSiswa): array
    {
        $siswa = Siswa::findOrFail($idSiswa);
        $cpls = Cpl::all();

        $radarData = [];

        foreach ($cpls as $cpl) {
            // Find all CPMKs mapped to this CPL
            $cpmkIds = Cpmk::where('id_cpl', $cpl->id)->pluck('id');

            // Find all student evaluations for these CPMKs
            $obeScores = NilaiObe::whereIn('id_cpmk', $cpmkIds)
                ->whereHas('krsDetail.krs', function ($q) use ($idSiswa) {
                    $q->where('id_siswa', $idSiswa);
                })
                ->get();

            $averageScore = $obeScores->isNotEmpty() ? round($obeScores->avg('skor_pencapaian'), 2) : 0.00;
            $targetMinimum = 70.00;
            $isFitted = $averageScore >= $targetMinimum;

            $radarData[] = [
                'id_cpl' => $cpl->id,
                'kode_cpl' => $cpl->kode_cpl,
                'aspek' => $cpl->aspek,
                'deskripsi' => $cpl->deskripsi_cpl_id,
                'skor_capaian' => $averageScore,
                'target_standar' => $targetMinimum,
                'status_kelulusan_cpl' => $isFitted ? 'TERPENUHI' : 'BELUM TUNTAS',
                'persentase_ketercapaian' => min(100.0, round(($averageScore / $targetMinimum) * 100, 1)),
            ];
        }

        $overallAverage = count($radarData) > 0 ? round(collect($radarData)->avg('skor_capaian'), 2) : 0.00;

        return [
            'mahasiswa' => [
                'nama' => $siswa->nama,
                'nim' => $siswa->nisn ?: $siswa->nis,
            ],
            'overall_cpl_index' => $overallAverage,
            'cpl_matrix' => $radarData,
        ];
    }
}
