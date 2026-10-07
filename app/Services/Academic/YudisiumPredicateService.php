<?php

namespace App\Services\Academic;

class YudisiumPredicateService
{
    /**
     * Determine honorific graduation predicate based on IPK, study duration (semesters), and retake history.
     *
     * @param float $ipk
     * @param int $lamaStudiSemester
     * @param bool $adaNilaiMengulang
     * @return array
     */
    public function calculatePredicate(float $ipk, int $lamaStudiSemester = 8, bool $adaNilaiMengulang = false): array
    {
        if ($ipk < 2.00) {
            return [
                'predikat' => 'Tidak Lulus',
                'description' => 'IPK di bawah ambang minimum 2.00',
                'eligible_cum_laude' => false,
            ];
        }

        if ($ipk >= 3.51) {
            if ($lamaStudiSemester <= 8 && !$adaNilaiMengulang) {
                return [
                    'predikat' => 'Dengan Pujian (Cum Laude)',
                    'description' => 'IPK ≥ 3.51, lulus tepat waktu (≤ 8 semester), dan tanpa mengulang mata kuliah',
                    'eligible_cum_laude' => true,
                ];
            }

            return [
                'predikat' => 'Sangat Memuaskan',
                'description' => 'IPK ≥ 3.51 (Masa studi > 8 semester atau terdapat nilai mengulang)',
                'eligible_cum_laude' => false,
            ];
        }

        if ($ipk >= 3.01) {
            return [
                'predikat' => 'Sangat Memuaskan',
                'description' => 'IPK 3.01 - 3.50',
                'eligible_cum_laude' => false,
            ];
        }

        if ($ipk >= 2.76) {
            return [
                'predikat' => 'Memuaskan',
                'description' => 'IPK 2.76 - 3.00',
                'eligible_cum_laude' => false,
            ];
        }

        return [
            'predikat' => 'Cukup',
            'description' => 'IPK 2.00 - 2.75',
            'eligible_cum_laude' => false,
        ];
    }
}
