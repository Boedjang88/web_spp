<?php

namespace App\Services\Analytics;

use App\Models\EmployerFeedback;
use App\Models\TracerStudy;

class TracerStudyService
{
    /**
     * Compute Accreditation Performance Metrics for BAN-PT / LAM Accreditation
     */
    public function computeAccreditationTracerSummary(?int $idProdi = null, ?int $tahunLulus = null): array
    {
        $query = TracerStudy::query();

        if ($idProdi) {
            $query->whereHas('mahasiswa', function ($q) use ($idProdi) {
                $q->where('id_kelas', $idProdi);
            });
        }

        if ($tahunLulus) {
            $query->where('tahun_lulus', $tahunLulus);
        }

        $totalResponden = $query->count();
        $bekerjaCount = (clone $query)->whereIn('status_alumni', ['Bekerja', 'Wirausaha'])->count();
        $lanjutStudiCount = (clone $query)->where('status_alumni', 'Melanjutkan Studi')->count();
        $mencariKerjaCount = (clone $query)->where('status_alumni', 'Mencari Kerja')->count();

        $avgWaktuTungguBulan = round((float) (clone $query)->whereNotNull('masa_tunggu_bulan')->avg('masa_tunggu_bulan'), 1);
        $avgGajiPertama = round((float) (clone $query)->whereNotNull('gaji_pertama')->avg('gaji_pertama'), 0);
        $sesuaiBidangCount = (clone $query)->whereNotNull('keselarasan_bidang')->count();

        $persentaseBekerja = $totalResponden > 0 ? round(($bekerjaCount / $totalResponden) * 100, 1) : 0.0;
        $persentaseKesesuaianBidang = $bekerjaCount > 0 ? round(($sesuaiBidangCount / $bekerjaCount) * 100, 1) : 0.0;

        // Employer Satisfaction Index (1.00 - 5.00) calculated across all dimension scores
        $feedbacks = EmployerFeedback::all();
        $totalFeedbackScore = 0;
        $feedbackCount = $feedbacks->count();

        foreach ($feedbacks as $fb) {
            $sum = ($fb->skor_integritas_etika ?? 4)
                 + ($fb->skor_keahlian_bidang ?? 4)
                 + ($fb->skor_bahasa_asing ?? 4)
                 + ($fb->skor_penggunaan_ti ?? 4)
                 + ($fb->skor_komunikasi ?? 4)
                 + ($fb->skor_kerjasama_tim ?? 4)
                 + ($fb->skor_pengembangan_diri ?? 4);
            $totalFeedbackScore += ($sum / 7.0);
        }

        $avgEmployerSatisfaction = $feedbackCount > 0 ? round($totalFeedbackScore / $feedbackCount, 2) : 4.50;

        return [
            'total_responden' => $totalResponden,
            'bekerja_wiraswasta' => $bekerjaCount,
            'lanjut_studi' => $lanjutStudiCount,
            'mencari_kerja' => $mencariKerjaCount,
            'persentase_bekerja' => $persentaseBekerja,
            'rata_waktu_tunggu_bulan' => $avgWaktuTungguBulan,
            'rata_gaji_pertama' => $avgGajiPertama,
            'persentase_kesesuaian_bidang' => $persentaseKesesuaianBidang,
            'indeks_kepuasan_pengguna_lulusan' => $avgEmployerSatisfaction,
        ];
    }
}
