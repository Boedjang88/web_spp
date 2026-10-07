<?php

namespace App\Services\Document;

use App\Models\Siswa;
use App\Models\SuratAkademik;
use Illuminate\Support\Str;

class AcademicLetterService
{
    /**
     * Generate an Active Student Certificate (Surat Keterangan Mahasiswa Aktif)
     */
    public function generateSuratMahasiswaAktif(Siswa $siswa, string $keperluan = 'Persyaratan Beasiswa / BPJS'): SuratAkademik
    {
        $nomorSurat = 'SKMA/' . date('Y/m') . '/' . sprintf('%04d', rand(1, 9999));
        $qrToken = hash('sha256', $siswa->id . $nomorSurat . config('app.key'));

        return SuratAkademik::create([
            'id_siswa' => $siswa->id,
            'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif',
            'nomor_surat' => $nomorSurat,
            'perihal' => 'Surat Keterangan Mahasiswa Aktif',
            'keperluan' => $keperluan,
            'qr_verification_token' => $qrToken,
            'file_pdf_path' => 'documents/surat_aktif/' . Str::slug($nomorSurat) . '.pdf',
            'status' => 'DISETUJUI',
            'tgl_terbit' => now(),
        ]);
    }

    /**
     * Verify authenticity of an academic letter by its QR verification token.
     */
    public function verifyLetterToken(string $token): ?array
    {
        $surat = SuratAkademik::with('siswa')->where('qr_verification_token', $token)->first();

        if (!$surat) {
            return null;
        }

        return [
            'is_valid' => true,
            'nomor_surat' => $surat->nomor_surat,
            'jenis_surat' => $surat->jenis_surat,
            'nama_mahasiswa' => $surat->siswa?->nama,
            'nim_nisn' => $surat->siswa?->nisn ?? $surat->siswa?->nis,
            'tgl_terbit' => $surat->tgl_terbit?->toIso8601String(),
            'status' => $surat->status,
        ];
    }
}
