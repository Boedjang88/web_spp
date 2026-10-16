<?php

namespace App\Domain\Academic\Services;

use App\Models\ActivityLog;
use App\Models\KknRegistrasi;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use App\Services\Audit\AuditTrailService;
use Illuminate\Support\Facades\DB;

class KknRegistrationService
{
    public const MIN_KKN_SKS = 100;

    public function __construct(
        protected AuditTrailService $auditTrailService
    ) {}

    /**
     * Get verified passed SKS for the student.
     */
    public function getPassedSks(Siswa $siswa): int
    {
        return app(\App\Services\Academic\GraduationClearanceService::class)->getTotalPassedSks($siswa);
    }

    public function getMinSks(): int
    {
        return (int) config('academic.kkn_min_sks', self::MIN_KKN_SKS);
    }

    /**
     * Check if student meets KKN credit threshold.
     */
    public function isEligible(Siswa $siswa): bool
    {
        if (in_array($siswa->status_kelulusan, ['Lulus', 'Lulus Wisuda', 'LULUS', 'Dikeluarkan', 'Drop Out'])) {
            return false;
        }

        return $this->getPassedSks($siswa) >= $this->getMinSks();
    }

    /**
     * Get latest KKN registration for student.
     */
    public function getLatestRegistrationForStudent(int $idSiswa): ?KknRegistrasi
    {
        return KknRegistrasi::where('id_siswa', $idSiswa)
            ->latest()
            ->first();
    }

    /**
     * Register student for KKN in an active academic year.
     */
    public function registerStudent(
        Siswa $siswa,
        array $data,
        bool $enforcePrerequisites = true
    ): KknRegistrasi {
        if (in_array($siswa->status_kelulusan, ['Lulus', 'Lulus Wisuda', 'LULUS', 'Dikeluarkan', 'Drop Out'])) {
            throw new \DomainException("Mahasiswa dengan status akademik '{$siswa->status_kelulusan}' tidak diizinkan mendaftar KKN.");
        }

        if ($enforcePrerequisites) {
            $minSks = $this->getMinSks();
            $passedSks = $this->getPassedSks($siswa);
            if ($passedSks < $minSks) {
                throw new \DomainException("Mahasiswa belum memenuhi syarat SKS untuk mendaftar KKN. SKS lulus saat ini: {$passedSks} SKS (Syarat minimum: {$minSks} SKS).");
            }
        }

        return DB::transaction(function () use ($siswa, $data) {
            $tahun = TahunAkademik::where('is_active', true)->first()
                ?? TahunAkademik::first()
                ?? TahunAkademik::create([
                    'kode_tahun' => '20261',
                    'nama_tahun' => '2026/2027 Ganjil',
                    'semester' => 'Ganjil',
                    'tgl_mulai' => now()->startOfYear(),
                    'tgl_selesai' => now()->endOfYear(),
                    'is_active' => true,
                ]);

            $registration = KknRegistrasi::updateOrCreate(
                [
                    'id_siswa' => $siswa->id,
                    'id_tahun_akademik' => $tahun->id,
                ],
                [
                    'lokasi_kkn' => $data['lokasi_kkn'],
                    'kelompok' => $data['kelompok'],
                    'kecamatan' => $data['kecamatan'] ?? 'Kecamatan Ciawi',
                    'kabupaten' => $data['kabupaten'] ?? 'Kabupaten Bogor',
                    'status_pendaftaran' => 'SUBMITTED',
                ]
            );

            ActivityLog::record(
                'KKN_REGISTER',
                "Mahasiswa {$siswa->nama} mendaftar KKN di lokasi: {$data['lokasi_kkn']}"
            );

            $this->auditTrailService->record(
                userId: auth()->id(),
                actionEvent: 'ACADEMIC_KKN_REGISTER',
                payload: [
                    'id_siswa' => $siswa->id,
                    'id_kkn_registrasi' => $registration->id,
                    'lokasi' => $data['lokasi_kkn'],
                    'kelompok' => $data['kelompok'],
                ]
            );

            return $registration;
        });
    }
}
