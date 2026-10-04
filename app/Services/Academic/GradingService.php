<?php

namespace App\Services\Academic;

use App\Models\BobotPenilaian;
use App\Models\Krs;
use App\Models\KrsDetail;

class GradingService
{
    /**
     * Compute final aggregate numeric score based on class multi-component weights
     */
    public function calculateFinalScore(KrsDetail $detail): float
    {
        $bobot = BobotPenilaian::where('id_kelas_kuliah', $detail->id_kelas_kuliah)->first();

        $wHadir = $bobot ? $bobot->bobot_kehadiran : 10;
        $wTugas = $bobot ? $bobot->bobot_tugas : 20;
        $wQuiz  = $bobot ? $bobot->bobot_quiz : 10;
        $wUts   = $bobot ? $bobot->bobot_uts : 30;
        $wUas   = $bobot ? $bobot->bobot_uas : 30;
        $wPrak  = $bobot ? $bobot->bobot_praktikum : 0;

        $totalWeight = $wHadir + $wTugas + $wQuiz + $wUts + $wUas + $wPrak;
        if ($totalWeight === 0) {
            $totalWeight = 100;
        }

        $nHadir = (float) ($detail->nilai_kehadiran ?? 0);
        $nTugas = (float) ($detail->nilai_tugas ?? 0);
        $nQuiz  = (float) ($detail->nilai_quiz ?? 0);
        $nUts   = (float) ($detail->nilai_uts ?? 0);
        $nUas   = (float) ($detail->nilai_uas ?? 0);
        $nPrak  = (float) ($detail->nilai_praktikum ?? 0);

        $weightedSum = ($nHadir * $wHadir) +
                       ($nTugas * $wTugas) +
                       ($nQuiz * $wQuiz) +
                       ($nUts * $wUts) +
                       ($nUas * $wUas) +
                       ($nPrak * $wPrak);

        return round($weightedSum / $totalWeight, 2);
    }

    /**
     * Convert numeric score (0 - 100) to standard Letter Grade & Quality Mutu Points
     *
     * @return array [string $huruf, float $bobotMutu, bool $isLulus]
     */
    public function convertScoreToGrade(float $numericScore): array
    {
        if ($numericScore >= 85.00) {
            return ['A', 4.00, true];
        } elseif ($numericScore >= 77.50) {
            return ['AB', 3.50, true];
        } elseif ($numericScore >= 70.00) {
            return ['B', 3.00, true];
        } elseif ($numericScore >= 62.50) {
            return ['BC', 2.50, true];
        } elseif ($numericScore >= 55.00) {
            return ['C', 2.00, true];
        } elseif ($numericScore >= 45.00) {
            return ['D', 1.00, false];
        } else {
            return ['E', 0.00, false];
        }
    }

    /**
     * Update and publish student grade record for a specific KRS Detail
     */
    public function processAndPublishGrade(int $idKrsDetail, array $scores): KrsDetail
    {
        $detail = KrsDetail::with('kelasKuliah')->findOrFail($idKrsDetail);

        $detail->fill($scores);

        $finalNumeric = $this->calculateFinalScore($detail);
        [$huruf, $bobotMutu, $isLulus] = $this->convertScoreToGrade($finalNumeric);

        $detail->nilai_akhir_angka = $finalNumeric;
        $detail->nilai_akhir_huruf = $huruf;
        $detail->bobot_mutu = $bobotMutu;
        $detail->is_lulus = $isLulus;
        $detail->is_published = true;
        $detail->save();

        return $detail;
    }

    /**
     * Calculate Semester GPA (IPS) for a completed KRS
     */
    public function calculateIps(Krs $krs): float
    {
        $details = $krs->details()->with('kelasKuliah.mataKuliah')->where('is_published', true)->get();

        if ($details->isEmpty()) {
            return 0.00;
        }

        $totalSks = 0;
        $totalMutuXskS = 0;

        foreach ($details as $detail) {
            $sks = $detail->kelasKuliah?->mataKuliah?->sks_total ?? 2;
            $mutu = (float) ($detail->bobot_mutu ?? 0.00);

            $totalSks += $sks;
            $totalMutuXskS += ($mutu * $sks);
        }

        return $totalSks > 0 ? round($totalMutuXskS / $totalSks, 2) : 0.00;
    }
}
