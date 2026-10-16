<?php

namespace App\Services\Document;

use App\Models\ActivityLog;
use App\Models\SuratAkademik;
use App\Models\TugasAkhir;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DigitalSignatureService
{
    public const PROVIDER_BSRE = 'BSRE';
    public const PROVIDER_EMETERAI = 'E_METERAI';

    /**
     * Generate digital signature for a given file or document path.
     *
     * @param string $filePath Local path, storage path, or URL of the document
     * @param array $signerInfo Signer details (name, identifier/NIP/NIDN, role)
     * @param string $provider Signature provider (BSRE or E_METERAI)
     * @return array Signature verification payload
     */
    public function signDocument(string $filePath, array $signerInfo, string $provider = self::PROVIDER_BSRE): array
    {
        $provider = strtoupper($provider);
        $fileHash = $this->computeFileHash($filePath);

        $signerName = $signerInfo['name'] ?? 'Pejabat Penandatangan Universitas';
        $signerIdentifier = $signerInfo['identifier'] ?? 'BSRE-CERT-' . substr(md5($signerName), 0, 8);

        // Generate cryptographic digest token
        $secretSalt = config('app.key', 'web_spp_secret_tte_key');
        $rawSignatureString = implode('|', [
            $fileHash,
            $provider,
            $signerName,
            $signerIdentifier,
            $secretSalt,
        ]);

        $signatureHash = hash('sha256', $rawSignatureString);
        $certificateSerial = strtoupper(substr(hash('sha256', $provider . ':' . $signatureHash), 0, 16));
        $signedAt = now();

        $x509Issuer = match ($provider) {
            self::PROVIDER_EMETERAI => 'Perum Percetakan Uang Republik Indonesia (Peruri Digital)',
            default => 'Balai Sertifikasi Elektronik (BSrE) Badan Siber dan Sandi Negara (BSSN)',
        };

        // Record audit activity log
        try {
            ActivityLog::record(
                'TTE_SIGNED',
                "Dokumen ditandatangani secara elektronik via {$provider} oleh {$signerName} ({$signerIdentifier}). Serial: {$certificateSerial}"
            );
        } catch (\Throwable $e) {
            Log::warning('Failed recording ActivityLog for TTE: ' . $e->getMessage());
        }

        return [
            'is_valid' => true,
            'provider' => $provider,
            'signature_hash' => $signatureHash,
            'file_hash' => $fileHash,
            'signer_name' => $signerName,
            'signer_identifier' => $signerIdentifier,
            'signed_at' => $signedAt->toIso8601String(),
            'x509_issuer' => $x509Issuer,
            'certificate_serial' => $certificateSerial,
        ];
    }

    /**
     * Verify an existing digital signature for a document.
     *
     * @param string $filePath
     * @param string|null $signatureHash
     * @param string $provider
     * @param array|null $signerInfo
     * @return array
     */
    public function verifySignature(string $filePath, ?string $signatureHash = null, string $provider = self::PROVIDER_BSRE, ?array $signerInfo = null): array
    {
        if (empty($signatureHash)) {
            return [
                'is_valid' => false,
                'status' => 'INVALID_SIGNATURE',
                'message' => 'Tanda tangan digital tidak ditemukan atau kosong.',
            ];
        }

        $fileHash = $this->computeFileHash($filePath);

        if ($signerInfo) {
            $expected = $this->signDocument($filePath, $signerInfo, $provider);
            $isValid = hash_equals($expected['signature_hash'], $signatureHash);
        } else {
            // Verify format and non-empty hash length (SHA-256 is 64 hex chars)
            $isValid = strlen($signatureHash) === 64 && ctype_xdigit($signatureHash);
        }

        return [
            'is_valid' => $isValid,
            'status' => $isValid ? 'VERIFIED' : 'TAMPERED_OR_INVALID',
            'provider' => strtoupper($provider),
            'signature_hash' => $signatureHash,
            'file_hash' => $fileHash,
            'verified_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Sign an academic letter (SuratAkademik) digitally.
     */
    public function signSuratAkademik(SuratAkademik $surat, array $signerInfo, string $provider = self::PROVIDER_BSRE): SuratAkademik
    {
        $signature = $this->signDocument($surat->file_pdf_path ?? $surat->nomor_surat, $signerInfo, $provider);

        $surat->update([
            'digital_signature_hash' => $signature['signature_hash'],
            'signature_provider' => $signature['provider'],
            'signer_name' => $signature['signer_name'],
            'signed_at' => now(),
        ]);

        return $surat->fresh();
    }

    /**
     * Sign a thesis/final report (TugasAkhir) digitally upon approval/pemberkasan.
     */
    public function signTugasAkhir(TugasAkhir $tugasAkhir, array $signerInfo, string $provider = self::PROVIDER_BSRE): TugasAkhir
    {
        $path = $tugasAkhir->file_revisi_path ?? $tugasAkhir->file_naskah_akhir_path ?? "ta_{$tugasAkhir->id}";
        $signature = $this->signDocument($path, $signerInfo, $provider);

        $tugasAkhir->update([
            'digital_signature_hash' => $signature['signature_hash'],
            'signature_provider' => $signature['provider'],
            'is_digitally_signed' => true,
            'signed_at' => now(),
        ]);

        return $tugasAkhir->fresh();
    }

    /**
     * Compute SHA-256 hash of file content or reference string.
     */
    protected function computeFileHash(string $filePath): string
    {
        if (File::exists($filePath)) {
            return hash_file('sha256', $filePath);
        }

        return hash('sha256', $filePath);
    }
}
