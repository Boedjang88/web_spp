<?php

namespace App\Services\Skpi;

use App\Models\Siswa;
use App\Models\SkpiAktivitas;

class SkpiService
{
    /**
     * Minimum SACS point requirement for graduation
     */
    public const MIN_SACS_GRADUATION = 50;

    /**
     * Compute total verified SACS points for a student
     */
    public function getStudentSacsSummary(int $idSiswa): array
    {
        $activities = SkpiAktivitas::where('id_siswa', $idSiswa)
            ->whereIn('status_verifikasi', ['Disetujui Kaprodi', 'Disahkan Dekan'])
            ->get();

        $totalPoin = $activities->sum('poin_sacs');
        $isEligible = $totalPoin >= self::MIN_SACS_GRADUATION;

        $groupedByCategory = $activities->groupBy('kategori')->map(function ($items) {
            return [
                'total_poin' => $items->sum('poin_sacs'),
                'items' => $items,
            ];
        });

        return [
            'total_poin' => $totalPoin,
            'min_syarat' => self::MIN_SACS_GRADUATION,
            'is_eligible' => $isEligible,
            'kategori_breakdown' => $groupedByCategory,
        ];
    }

    /**
     * Generate bilingual (ID/EN) SKPI Diploma Supplement document data
     */
    public function generateBilingualSupplement(int $idSiswa): array
    {
        $siswa = Siswa::with(['kelas'])->findOrFail($idSiswa);
        $sacsSummary = $this->getStudentSacsSummary($idSiswa);

        return [
            'nomor_skpi' => 'SKPI/' . date('Y') . '/' . $siswa->nisn,
            'mahasiswa' => [
                'nama' => $siswa->nama,
                'nim' => $siswa->nisn ?: $siswa->nis,
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2004-05-15',
                'program_studi_id' => 'Rekayasa Perangkat Lunak',
                'program_studi_en' => 'Software Engineering',
                'gelar_id' => 'Sarjana Terapan Komputer (S.Tr.Kom)',
                'gelar_en' => 'Bachelor of Applied Computer (B.App.Comp)',
            ],
            'kualifikasi_id' => 'Level 6 Kerangka Kualifikasi Nasional Indonesia (KKNI)',
            'kualifikasi_en' => 'Level 6 Indonesian National Qualifications Framework (IQF)',
            'sacs_summary' => $sacsSummary,
            'qr_verification' => 'SKPI-VERIFIED-' . md5($siswa->nisn . '-' . date('Y')),
        ];
    }
}
