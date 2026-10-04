<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\URL;

class SecureDocumentService
{
    public const DEFAULT_EXPIRY_MINUTES = 5;

    /**
     * Generate a temporary signed route URL with anti-tampering protection.
     */
    public function generateSignedUrl(string $routeName, array $parameters = [], int $minutes = self::DEFAULT_EXPIRY_MINUTES): string
    {
        return URL::temporarySignedRoute(
            $routeName,
            now()->addMinutes($minutes),
            $parameters
        );
    }

    /**
     * Generate 5-minute signed URL for Payment Receipt.
     */
    public function generateReceiptUrl(int $pembayaranId, int $siswaId, int $minutes = self::DEFAULT_EXPIRY_MINUTES): string
    {
        return $this->generateSignedUrl('pembayaran.cetak.signed', [
            'id' => $pembayaranId,
            'id_siswa' => $siswaId,
        ], $minutes);
    }

    /**
     * Generate 5-minute signed URL for Exam Pass (Kartu Ujian).
     */
    public function generateExamPassUrl(int $krsId, int $siswaId, int $minutes = self::DEFAULT_EXPIRY_MINUTES): string
    {
        return $this->generateSignedUrl('krs.kartu-ujian.signed', [
            'id_krs' => $krsId,
            'id_siswa' => $siswaId,
        ], $minutes);
    }

    /**
     * Generate 5-minute signed URL for E-Rapor / KHS.
     */
    public function generateRaporUrl(int $siswaId, int $tahunAkademikId, int $minutes = self::DEFAULT_EXPIRY_MINUTES): string
    {
        return $this->generateSignedUrl('khs.cetak.signed', [
            'id_siswa' => $siswaId,
            'id_tahun_akademik' => $tahunAkademikId,
        ], $minutes);
    }

    /**
     * Generate 5-minute signed URL for SKPI Certificate.
     */
    public function generateSkpiUrl(int $siswaId, int $minutes = self::DEFAULT_EXPIRY_MINUTES): string
    {
        return $this->generateSignedUrl('skpi.cetak.signed', [
            'id_siswa' => $siswaId,
        ], $minutes);
    }

    /**
     * Generate 5-minute signed URL for LMS Timed Material Download.
     */
    public function generateMaterialDownloadUrl(int $materialId, int $minutes = self::DEFAULT_EXPIRY_MINUTES): string
    {
        return $this->generateSignedUrl('lms.material.download.signed', [
            'id' => $materialId,
        ], $minutes);
    }
}
