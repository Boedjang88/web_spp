<?php

namespace App\Services\Document;

use App\Models\Siswa;
use App\Models\SuratAkademik;
use Illuminate\Support\Str;

class AcademicLetterService
{
    protected DigitalSignatureService $signatureService;

    public function __construct(?DigitalSignatureService $signatureService = null)
    {
        $this->signatureService = $signatureService ?? new DigitalSignatureService();
    }

    /**
     * Generate an Active Student Certificate (Surat Keterangan Mahasiswa Aktif)
     * with automated BSrE digital signature.
     */
    public function generateSuratMahasiswaAktif(
        Siswa $siswa,
        string $keperluan = 'Persyaratan Beasiswa / BPJS',
        ?array $signerInfo = null
    ): SuratAkademik {
        $nomorSurat = 'SKMA/' . date('Y/m') . '/' . sprintf('%04d', rand(1, 9999));
        $filePath = 'documents/surat_aktif/' . Str::slug($nomorSurat) . '.pdf';
        $qrToken = hash('sha256', $siswa->id . $nomorSurat . config('app.key'));

        $signer = $signerInfo ?? [
            'name' => 'Dr. H. Ahmad Fauzi, M.T.',
            'identifier' => 'NIP. 197508122003121002',
            'role' => 'Wakil Rektor I Bidang Akademik',
        ];

        // Apply BSrE digital signature
        $signature = $this->signatureService->signDocument($filePath, $signer, DigitalSignatureService::PROVIDER_BSRE);

        return SuratAkademik::create([
            'id_siswa' => $siswa->id,
            'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif',
            'nomor_surat' => $nomorSurat,
            'perihal' => 'Surat Keterangan Mahasiswa Aktif',
            'keperluan' => $keperluan,
            'qr_verification_token' => $qrToken,
            'digital_signature_hash' => $signature['signature_hash'],
            'signature_provider' => $signature['provider'],
            'signer_name' => $signature['signer_name'],
            'signed_at' => now(),
            'file_pdf_path' => $filePath,
            'status' => 'DISETUJUI',
            'tgl_terbit' => now(),
        ]);
    }

    /**
     * Verify authenticity of an academic letter by its QR verification token or signature hash.
     */
    public function verifyLetterToken(string $token): ?array
    {
        $surat = SuratAkademik::with('siswa')
            ->where('qr_verification_token', $token)
            ->orWhere('digital_signature_hash', $token)
            ->first();

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
            'is_digitally_signed' => $surat->isDigitallySigned(),
            'signature_provider' => $surat->signature_provider,
            'digital_signature_hash' => $surat->digital_signature_hash,
            'signer_name' => $surat->signer_name,
            'signed_at' => $surat->signed_at?->toIso8601String(),
        ];
    }
}
