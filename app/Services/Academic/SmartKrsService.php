<?php

namespace App\Services\Academic;

use App\Models\FinancialClearance;
use App\Models\JadwalKuliah;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\MataKuliah;
use App\Models\MataKuliahPrasyarat;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use App\Services\Audit\AuditTrailService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SmartKrsService
{
    public function __construct(
        protected AuditTrailService $auditService
    ) {}

    /**
     * Determine maximum SKS cap based on Previous Semester GPA (IPS)
     */
    public function calculateMaxSksCap(float $ips): int
    {
        if ($ips >= 3.00) {
            return 24;
        } elseif ($ips >= 2.50) {
            return 21;
        } elseif ($ips >= 2.00) {
            return 18;
        } else {
            return 15;
        }
    }

    /**
     * Check if Student is Financially Cleared to register KRS
     */
    public function isFinanciallyCleared(int $idSiswa, int $idTahunAkademik): bool
    {
        $clearance = FinancialClearance::where('id_siswa', $idSiswa)
            ->where('id_tahun_akademik', $idTahunAkademik)
            ->first();

        return $clearance ? $clearance->is_krs_unlocked : false;
    }

    /**
     * Check Prerequisite Course Requirements
     *
     * @return array [bool $passed, ?string $reason]
     */
    public function checkPrerequisites(int $idSiswa, int $idMataKuliah): array
    {
        $prasyarats = MataKuliahPrasyarat::where('id_mk', $idMataKuliah)->get();

        if ($prasyarats->isEmpty()) {
            return [true, null];
        }

        foreach ($prasyarats as $prasyarat) {
            // Find past student records for this prerequisite course
            $hasPassed = KrsDetail::whereHas('krs', function ($q) use ($idSiswa) {
                $q->where('id_siswa', $idSiswa);
            })
            ->whereHas('kelasKuliah', function ($q) use ($prasyarat) {
                $q->where('id_mk', $prasyarat->id_mk_prasyarat);
            })
            ->where('is_lulus', true)
            ->exists();

            if (!$hasPassed) {
                $mkPrasyarat = MataKuliah::find($prasyarat->id_mk_prasyarat);
                return [
                    false,
                    "Anda belum memenuhi mata kuliah prasyarat: {$mkPrasyarat?->nama_mk} ({$mkPrasyarat?->kode_mk})."
                ];
            }
        }

        return [true, null];
    }

    /**
     * High-Concurrency KRS Course Enrollment with Pessimistic Locking
     *
     * @throws Exception
     */
    public function enrollClassWithPessimisticLock(int $idSiswa, int $idKelasKuliah, int $idTahunAkademik): KrsDetail
    {
        return DB::transaction(function () use ($idSiswa, $idKelasKuliah, $idTahunAkademik) {
            // 1. Financial Clearance Check
            if (!$this->isFinanciallyCleared($idSiswa, $idTahunAkademik)) {
                throw new Exception('KRS Terkunci! Anda belum menyelesaikan administrasi pembayaran UKT/SPP semester ini.');
            }

            // 2. Fetch or Create KRS Header
            $krs = Krs::firstOrCreate(
                [
                    'id_siswa' => $idSiswa,
                    'id_tahun_akademik' => $idTahunAkademik,
                ],
                [
                    'max_sks_diizinkan' => 24,
                    'total_sks_diambil' => 0,
                    'status_krs' => 'Draft',
                ]
            );

            // 3. Pessimistically Lock the Target Class Row (FOR UPDATE)
            $kelas = KelasKuliah::where('id', $idKelasKuliah)
                ->lockForUpdate()
                ->with(['mataKuliah', 'jadwalKuliahs'])
                ->firstOrFail();

            // 4. Check if Seat is Available
            if ($kelas->total_terisi >= $kelas->kuota_maksimal) {
                throw new Exception("Kuota kelas {$kelas->nama_kelas} untuk mata kuliah {$kelas->mataKuliah?->nama_mk} sudah penuh ({$kelas->total_terisi}/{$kelas->kuota_maksimal}).");
            }

            // 5. Check if already enrolled in this class or same course in another class
            $alreadyEnrolled = KrsDetail::where('id_krs', $krs->id)
                ->whereHas('kelasKuliah', function ($q) use ($kelas) {
                    $q->where('id_mk', $kelas->id_mk);
                })
                ->exists();

            if ($alreadyEnrolled) {
                throw new Exception("Anda sudah mengambil mata kuliah {$kelas->mataKuliah?->nama_mk} pada semester ini.");
            }

            // 6. Check Prerequisite Requirements
            [$prereqPassed, $prereqReason] = $this->checkPrerequisites($idSiswa, $kelas->id_mk);
            if (!$prereqPassed) {
                throw new Exception($prereqReason);
            }

            // 7. Check SKS Cap Overage
            $courseSks = $kelas->mataKuliah?->sks_total ?? 2;
            if (($krs->total_sks_diambil + $courseSks) > $krs->max_sks_diizinkan) {
                throw new Exception("Melebihi batas SKS! Maksimal SKS Anda adalah {$krs->max_sks_diizinkan} SKS (Total diambil saat ini: {$krs->total_sks_diambil} SKS + {$courseSks} SKS).");
            }

            // 8. Check Timetable Clashes with currently enrolled courses in this KRS
            $this->validateNoStudentScheduleClash($krs->id, $kelas);

            // 9. Increment Class Enrolled Count
            $stateBefore = $kelas->toArray();
            $kelas->increment('total_terisi');
            $stateAfter = $kelas->fresh()->toArray();

            // 10. Create KRS Detail Entry
            $krsDetail = KrsDetail::create([
                'id_krs' => $krs->id,
                'id_kelas_kuliah' => $kelas->id,
                'status_ambil' => 'Baru',
            ]);

            // 11. Update Total SKS on KRS Header
            $krs->increment('total_sks_diambil', $courseSks);

            // 12. Record Immutable Audit Trail Log
            $this->auditService->record(
                auth()->id(),
                'KRS_ENROLL_PESSIMISTIC_LOCK',
                [
                    'id_siswa' => $idSiswa,
                    'id_kelas' => $idKelasKuliah,
                    'kode_mk' => $kelas->mataKuliah?->kode_mk,
                    'sks' => $courseSks,
                ],
                $stateBefore,
                $stateAfter
            );

            return $krsDetail;
        });
    }

    /**
     * Rate-limited & Deadlock-Preventing KRS Enrollment wrapper
     * Uses atomic locks on student and class resource to serialize concurrent spikes
     *
     * @throws Exception
     */
    public function attemptEnrollmentWithThrottle(
        int $idSiswa,
        int $idKelasKuliah,
        int $idTahunAkademik,
        int $lockTimeoutSeconds = 5
    ): KrsDetail {
        $studentLockKey = "lock_krs_student_{$idSiswa}";
        $classLockKey = "lock_krs_class_{$idKelasKuliah}";

        $studentLock = Cache::lock($studentLockKey, $lockTimeoutSeconds);
        $classLock = Cache::lock($classLockKey, $lockTimeoutSeconds);

        if (!$studentLock->get()) {
            throw new Exception("Permintaan KRS Anda sedang diproses. Mohon tunggu beberapa detik sebelum mencoba lagi.");
        }

        try {
            return $classLock->block($lockTimeoutSeconds, function () use ($idSiswa, $idKelasKuliah, $idTahunAkademik) {
                return $this->enrollClassWithPessimisticLock($idSiswa, $idKelasKuliah, $idTahunAkademik);
            });
        } finally {
            $studentLock->release();
        }
    }

    /**
     * Drop Class Enrollment with Pessimistic Locking
     *
     * @throws Exception
     */
    public function dropClass(int $idSiswa, int $idKrsDetail): bool
    {
        return DB::transaction(function () use ($idSiswa, $idKrsDetail) {
            $detail = KrsDetail::where('id', $idKrsDetail)
                ->whereHas('krs', function ($q) use ($idSiswa) {
                    $q->where('id_siswa', $idSiswa);
                })
                ->with(['krs', 'kelasKuliah.mataKuliah'])
                ->firstOrFail();

            if ($detail->krs->status_krs === 'Disetujui') {
                throw new Exception('KRS sudah disetujui dosen wali. Pembatalan mata kuliah harus melalui persetujuan BAAK/Dosen Wali.');
            }

            $kelas = KelasKuliah::where('id', $detail->id_kelas_kuliah)->lockForUpdate()->firstOrFail();
            $courseSks = $kelas->mataKuliah?->sks_total ?? 2;

            $kelas->decrement('total_terisi');
            $detail->krs->decrement('total_sks_diambil', $courseSks);
            $detail->delete();

            return true;
        });
    }

    /**
     * Validate that new class doesn't clash with student's active schedule
     *
     * @throws Exception
     */
    protected function validateNoStudentScheduleClash(int $idKrs, KelasKuliah $newClass): void
    {
        $newSchedules = $newClass->jadwalKuliahs;

        $existingSchedules = JadwalKuliah::whereHas('kelasKuliah.krsDetails', function ($q) use ($idKrs) {
            $q->where('id_krs', $idKrs);
        })->with('kelasKuliah.mataKuliah')->get();

        foreach ($newSchedules as $newJadwal) {
            foreach ($existingSchedules as $existJadwal) {
                if ($newJadwal->hari === $existJadwal->hari) {
                    if ($newJadwal->jam_mulai < $existJadwal->jam_selesai && $newJadwal->jam_selesai > $existJadwal->jam_mulai) {
                        throw new Exception("Bentrok Jadwal Pribadi! Mata kuliah {$newClass->mataKuliah?->nama_mk} bentrok pada hari {$newJadwal->hari} ({$newJadwal->jam_mulai}-{$newJadwal->jam_selesai}) dengan {$existJadwal->kelasKuliah?->mataKuliah?->nama_mk} ({$existJadwal->jam_mulai}-{$existJadwal->jam_selesai}).");
                    }
                }
            }
        }
    }
}
