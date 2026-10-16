<?php

namespace App\Domain\Finance\Services;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Services\Audit\AuditTrailService;

class SppBillingService
{
    public function __construct(
        protected AuditTrailService $auditTrailService
    ) {}

    /**
     * Get real-time arrears and payment status for a student.
     */
    public function calculateStudentArrears(Siswa $siswa): array
    {
        return $siswa->info_tunggakan;
    }

    /**
     * Check whether student has active payment transactions prohibiting deletion.
     */
    public function hasPaymentHistory(Siswa $siswa): bool
    {
        return $siswa->pembayarans()->count() > 0;
    }

    /**
     * Check if a student is financially cleared for exams / KRS enrollment.
     */
    public function isFinanciallyCleared(Siswa $siswa): bool
    {
        $tunggakan = $this->calculateStudentArrears($siswa);
        return ($tunggakan['total_bulan'] ?? 0) === 0;
    }

    /**
     * Generate structured billing summary for official statement letter.
     */
    public function getBillingStatementData(Siswa $siswa): array
    {
        $siswa->loadMissing(['kelas', 'spp', 'pembayarans.petugas']);
        $tunggakan = $this->calculateStudentArrears($siswa);

        $totalRupiah = max(0, (int) ($tunggakan['total_rupiah'] ?? $tunggakan['total_tunggakan'] ?? 0));
        $listBulan = $tunggakan['list_bulan'] ?? $tunggakan['rincian'] ?? $tunggakan['bulan_menunggak'] ?? [];

        return [
            'siswa' => $siswa,
            'tunggakan' => $tunggakan,
            'is_lunas' => ($tunggakan['total_bulan'] ?? 0) === 0,
            'nominal_per_bulan' => max(0, (int) ($siswa->spp?->nominal ?? 0)),
            'total_tunggakan' => $totalRupiah,
            'total_rupiah' => $totalRupiah,
            'bulan_menunggak' => $listBulan,
            'list_bulan' => $listBulan,
            'rincian' => $listBulan,
        ];
    }
}
