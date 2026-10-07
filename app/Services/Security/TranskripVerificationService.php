<?php

namespace App\Services\Security;

use App\Models\Siswa;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class TranskripVerificationService
{
    /**
     * Generate HMAC digital signature and signed verification URL for student transcripts
     */
    public function generateDocumentVerificationPayload(Siswa $siswa, string $documentType = 'TRANSKRIP'): array
    {
        $appKey = config('app.key', 'secret_app_key');
        $timestamp = now()->timestamp;
        
        $signatureData = "{$siswa->id}|{$siswa->nisn}|{$documentType}|{$timestamp}";
        $hmacSignature = hash_hmac('sha256', $signatureData, $appKey);

        $verificationUrl = Route::has('transcript.verify')
            ? URL::temporarySignedRoute(
                'transcript.verify',
                now()->addDays(365),
                [
                    'siswa_id' => $siswa->id,
                    'doc_type' => $documentType,
                    'sig' => $hmacSignature,
                ]
            )
            : url("/verify/transcript?siswa_id={$siswa->id}&doc_type={$documentType}&sig={$hmacSignature}");

        $qrDataString = "SIAKAD-OFFICIAL-DOC|{$documentType}|NIM:{$siswa->nisn}|SIG:{$hmacSignature}|URL:{$verificationUrl}";

        return [
            'document_type' => $documentType,
            'student_name' => $siswa->nama,
            'nim' => $siswa->nisn,
            'hmac_signature' => $hmacSignature,
            'qr_data' => $qrDataString,
            'verification_url' => $verificationUrl,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Verify authenticity of a transcript signature
     */
    public function verifyDocumentSignature(int $siswaId, string $documentType, string $providedSignature, int $timestamp): bool
    {
        $appKey = config('app.key', 'secret_app_key');
        $siswa = Siswa::find($siswaId);
        if (!$siswa) return false;

        $signatureData = "{$siswa->id}|{$siswa->nisn}|{$documentType}|{$timestamp}";
        $expectedSignature = hash_hmac('sha256', $signatureData, $appKey);

        return hash_equals($expectedSignature, $providedSignature);
    }
}
