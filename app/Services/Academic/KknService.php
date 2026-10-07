<?php

namespace App\Services\Academic;

use App\Models\KknRegistrasi;
use App\Models\Siswa;
use InvalidArgumentException;

class KknService
{
    /**
     * Enroll Student into KKN (Kuliah Kerja Nyata)
     */
    public function registerKkn(
        Siswa $siswa,
        int $idTahunAkademik,
        string $namaKelompok,
        string $desaLokasi,
        string $kecamatan,
        string $kabupaten
    ): KknRegistrasi {
        return KknRegistrasi::updateOrCreate(
            [
                'id_siswa' => $siswa->id,
                'id_tahun_akademik' => $idTahunAkademik,
            ],
            [
                'nama_kelompok' => $namaKelompok,
                'desa_lokasi' => $desaLokasi,
                'kecamatan' => $kecamatan,
                'kabupaten' => $kabupaten,
                'status_pendaftaran' => 'SUBMITTED',
            ]
        );
    }

    /**
     * Assign Dosen Pembimbing Lapangan (DPL) to KKN Group
     */
    public function assignDpl(int $idKknRegistrasi, int $idDosenDpl): KknRegistrasi
    {
        $reg = KknRegistrasi::findOrFail($idKknRegistrasi);
        $reg->update([
            'id_dosen_dpl' => $idDosenDpl,
            'status_pendaftaran' => 'APPROVED',
        ]);
        return $reg;
    }

    /**
     * Grade KKN Performance
     */
    public function gradeKkn(int $idKknRegistrasi, float $nilaiAngka): KknRegistrasi
    {
        $reg = KknRegistrasi::findOrFail($idKknRegistrasi);

        $nilaiHuruf = match (true) {
            $nilaiAngka >= 85.0 => 'A',
            $nilaiAngka >= 75.0 => 'B',
            $nilaiAngka >= 65.0 => 'C',
            $nilaiAngka >= 55.0 => 'D',
            default => 'E',
        };

        $reg->update([
            'nilai_angka' => $nilaiAngka,
            'nilai_huruf' => $nilaiHuruf,
            'status_pendaftaran' => 'COMPLETED',
        ]);

        return $reg;
    }
}
