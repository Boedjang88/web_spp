<?php

namespace App\Services\Finance;

use App\Models\Beasiswa;
use App\Models\PendaftaranBeasiswa;
use App\Models\Siswa;
use DomainException;

class ScholarshipService
{
    /**
     * Create a new scholarship program (e.g. KIP-Kuliah, Beasiswa Prestasi)
     */
    public function createScholarship(array $data): Beasiswa
    {
        return Beasiswa::create([
            'nama_beasiswa' => $data['nama_beasiswa'],
            'penyelenggara' => $data['penyelenggara'] ?? 'Kemendikbudristek',
            'jenis_cakupan' => $data['jenis_cakupan'] ?? 'FULL',
            'persentase_potongan' => $data['persentase_potongan'] ?? 100.00,
            'nominal_potongan' => $data['nominal_potongan'] ?? 0.00,
            'kuota' => $data['kuota'] ?? 100,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Apply for a scholarship for a student.
     */
    public function applyScholarship(Siswa $siswa, int $idBeasiswa, float $ipk = 3.50, ?string $catatan = null): PendaftaranBeasiswa
    {
        $beasiswa = Beasiswa::findOrFail($idBeasiswa);

        if (!$beasiswa->is_active) {
            throw new DomainException("Program beasiswa {$beasiswa->nama_beasiswa} sedang tidak aktif.");
        }

        return PendaftaranBeasiswa::updateOrCreate(
            [
                'id_beasiswa' => $idBeasiswa,
                'id_siswa' => $siswa->id,
            ],
            [
                'status_pengajuan' => 'DISETUJUI',
                'ipk_terakhir' => $ipk,
                'tgl_pengajuan' => now(),
                'catatan' => $catatan,
            ]
        );
    }

    /**
     * Calculate adjusted UKT / SPP billing amount after applying active approved scholarships.
     */
    public function calculateNetBillingAmount(Siswa $siswa, float $originalBillingAmount): float
    {
        $activeScholarships = PendaftaranBeasiswa::with('beasiswa')
            ->where('id_siswa', $siswa->id)
            ->where('status_pengajuan', 'DISETUJUI')
            ->whereHas('beasiswa', function ($query) {
                $query->where('is_active', true);
            })->get();

        if ($activeScholarships->isEmpty()) {
            return $originalBillingAmount;
        }

        $netAmount = $originalBillingAmount;

        foreach ($activeScholarships as $app) {
            $b = $app->beasiswa;
            if (!$b) {
                continue;
            }

            if ($b->jenis_cakupan === 'FULL') {
                return 0.00; // 100% free / waived
            } elseif ($b->jenis_cakupan === 'PARSIAL') {
                $discountPercent = (float) $b->persentase_potongan;
                $netAmount -= ($originalBillingAmount * ($discountPercent / 100));
            } elseif ($b->jenis_cakupan === 'NOMINAL') {
                $netAmount -= (float) $b->nominal_potongan;
            }
        }

        return max(0.00, round($netAmount, 2));
    }
}
