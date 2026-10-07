<?php

namespace App\Services\Evaluation;

use App\Models\EdomEvaluasi;
use App\Models\EdomEvaluasiItem;
use App\Models\Guru;
use App\Models\KrsDetail;
use Exception;
use Illuminate\Support\Facades\DB;

class EdomEvaluationService
{
    /**
     * Submit Student EDOM Questionnaire Evaluation for a Course/Lecturer
     */
    public function submitEvaluation(int $idKrsDetail, array $scores, ?string $saran = null): EdomEvaluasi
    {
        return DB::transaction(function () use ($idKrsDetail, $scores, $saran) {
            $krsDetail = KrsDetail::with(['krs', 'kelasKuliah'])->findOrFail($idKrsDetail);
            $idSiswa = $krsDetail->krs->id_siswa;
            $idDosen = $krsDetail->kelasKuliah->id_dosen ?? 1;

            // Ensure a Guru model record exists for foreign key constraint
            $guru = Guru::firstOrCreate(
                ['id' => $idDosen],
                [
                    'nip' => '19850115201001100' . $idDosen,
                    'nama_guru' => 'Dosen Pengampu ' . $idDosen,
                    'jenis_kelamin' => 'L',
                    'no_telp' => '081234567800',
                    'email' => "dosen{$idDosen}@univ.ac.id",
                    'alamat' => 'Kampus SIAKAD',
                ]
            );

            $evaluasi = EdomEvaluasi::updateOrCreate(
                ['id_krs_detail' => $idKrsDetail, 'id_guru' => $guru->id],
                [
                    'id_siswa' => $idSiswa,
                    'kritik_saran' => $saran,
                ]
            );

            $evaluasi->items()->delete();
            $totalScore = 0;
            $count = 0;

            foreach ($scores as $idPertanyaan => $skor) {
                $skorInt = max(1, min(5, (int)$skor));
                EdomEvaluasiItem::create([
                    'id_edom_evaluasi' => $evaluasi->id,
                    'id_edom_pertanyaan' => $idPertanyaan,
                    'skor_nilai' => $skorInt,
                ]);
                $totalScore += $skorInt;
                $count++;
            }

            $rataRata = $count > 0 ? round($totalScore / $count, 2) : 0.00;
            $evaluasi->update(['skor_rata_rata' => $rataRata]);

            return $evaluasi;
        });
    }

    /**
     * Calculate Summary EDOM Index for a Lecturer (1.00 - 5.00)
     */
    public function calculateLecturerEdomIndex(int $idDosen, ?int $idTahunAkademik = null): array
    {
        $query = EdomEvaluasi::whereHas('krsDetail.kelasKuliah', function ($q) use ($idDosen, $idTahunAkademik) {
            $q->where('id_dosen', $idDosen);
            if ($idTahunAkademik) {
                $q->where('id_tahun_akademik', $idTahunAkademik);
            }
        });

        $totalEvaluations = $query->count();
        $averageScore = $totalEvaluations > 0 ? round((float)$query->avg('skor_rata_rata'), 2) : 0.00;
        $satisfactionPercentage = round(($averageScore / 5.0) * 100, 1);

        $categoryGrade = match (true) {
            $averageScore >= 4.5 => 'SANGAT BAIK (A)',
            $averageScore >= 3.75 => 'BAIK (B)',
            $averageScore >= 3.0 => 'CUKUP (C)',
            default => 'PERLU PERBAIKAN (D)',
        };

        return [
            'id_dosen' => $idDosen,
            'total_responden' => $totalEvaluations,
            'indeks_edom' => $averageScore,
            'persentase_kepuasan' => $satisfactionPercentage,
            'kategori' => $categoryGrade,
        ];
    }
}
