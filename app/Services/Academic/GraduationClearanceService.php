<?php

namespace App\Services\Academic;

use App\Models\KrsDetail;
use App\Models\MataKuliah;
use App\Models\Siswa;
use App\Models\SkpiAktivitas;
use DomainException;
use Illuminate\Support\Facades\DB;

class GraduationClearanceService
{
    public const DEFAULT_TARGET_SKS = 144;
    public const DEFAULT_MIN_SKPI_POINTS = 100;
    public const DISQUALIFYING_GRADES = ['D', 'E', 'BL', 'T'];

    /**
     * Audit a student's graduation clearance criteria.
     *
     * @param Siswa $siswa
     * @param int|null $targetSks
     * @param int|null $targetSkpi
     * @return array
     */
    public function auditGraduation(Siswa $siswa, ?int $targetSks = null, ?int $targetSkpi = null): array
    {
        // 1. Determine Curriculum Target SKS
        $targetSks = $targetSks ?? self::DEFAULT_TARGET_SKS;
        $targetSkpi = $targetSkpi ?? self::DEFAULT_MIN_SKPI_POINTS;

        // 2. Fetch all student KRS details
        $krsDetails = KrsDetail::whereHas('krs', function ($query) use ($siswa) {
            $query->where('id_siswa', $siswa->id);
        })->with(['kelasKuliah.mataKuliah.kurikulum'])->get();

        // Group by course ID to get the latest/highest grade for each course
        $courseGrades = [];
        $passedSksTotal = 0;
        $allPassedCourseIds = [];

        foreach ($krsDetails as $detail) {
            $mk = $detail->kelasKuliah?->mataKuliah;
            if (!$mk) {
                continue;
            }

            $courseId = $mk->id;
            $bobot = (float) ($detail->bobot_mutu ?? 0.0);
            $grade = strtoupper(trim((string) $detail->nilai_akhir_huruf));
            $isLulus = (bool) $detail->is_lulus;

            // Update if not exists or if newer/higher bobot
            if (!isset($courseGrades[$courseId]) || $bobot > $courseGrades[$courseId]['bobot']) {
                $courseGrades[$courseId] = [
                    'mk' => $mk,
                    'grade' => $grade,
                    'bobot' => $bobot,
                    'is_lulus' => $isLulus,
                    'sks' => (int) ($mk->sks_total ?? 0),
                ];
            }
        }

        foreach ($courseGrades as $courseId => $data) {
            if ($data['is_lulus'] && !in_array($data['grade'], ['E', 'BL', 'T'])) {
                $passedSksTotal += $data['sks'];
                $allPassedCourseIds[] = $courseId;
            }
        }

        // 3. Validate Mandatory Courses (Mata Kuliah Wajib)
        $failedMandatoryCourses = [];
        // Look up mandatory courses from the student's curriculum or all courses taken marked WAJIB
        $mandatoryCourses = MataKuliah::whereIn('jenis_mk', ['Wajib Program Studi', 'Wajib Nasional', 'WAJIB', 'Wajib Prodi', 'Wajib Universitas'])
            ->get();

        foreach ($mandatoryCourses as $mk) {
            $courseId = $mk->id;
            if (isset($courseGrades[$courseId])) {
                $taken = $courseGrades[$courseId];
                if (in_array($taken['grade'], self::DISQUALIFYING_GRADES) || !$taken['is_lulus']) {
                    $failedMandatoryCourses[] = [
                        'kode_mk' => $mk->kode_mk,
                        'nama_mk' => $mk->nama_mk,
                        'grade' => $taken['grade'],
                        'reason' => "Nilai {$taken['grade']} tidak memenuhi standar kelulusan mata kuliah wajib (harus minimal C).",
                    ];
                }
            }
        }

        // Also check any course taken by the student marked WAJIB with D/E
        foreach ($courseGrades as $courseId => $data) {
            $mk = $data['mk'];
            $isWajib = in_array(strtoupper((string) $mk->jenis_mk), ['WAJIB', 'WAJIB PRODI', 'WAJIB NASIONAL']);
            if ($isWajib && in_array($data['grade'], self::DISQUALIFYING_GRADES)) {
                $alreadyListed = false;
                foreach ($failedMandatoryCourses as $f) {
                    if ($f['kode_mk'] === $mk->kode_mk) {
                        $alreadyListed = true;
                        break;
                    }
                }
                if (!$alreadyListed) {
                    $failedMandatoryCourses[] = [
                        'kode_mk' => $mk->kode_mk,
                        'nama_mk' => $mk->nama_mk,
                        'grade' => $data['grade'],
                        'reason' => "Nilai {$data['grade']} pada mata kuliah wajib tidak diizinkan untuk yudisium.",
                    ];
                }
            }
        }

        // 4. Validate SKPI / SACS Non-Academic Points
        $verifiedSkpiPoints = SkpiAktivitas::where('id_siswa', $siswa->id)
            ->whereIn('status_verifikasi', ['Disetujui Kaprodi', 'Disahkan Dekan', 'DISETUJUI', 'Disetujui'])
            ->sum('poin_sacs');
        
        $totalSkpi = max((int) $siswa->total_skpi_points, (int) $verifiedSkpiPoints);

        // 5. Build Reasons for Ineligibility
        $reasons = [];
        if ($passedSksTotal < $targetSks) {
            $reasons[] = "Total SKS lulus ({$passedSksTotal} SKS) belum memenuhi syarat minimum kelulusan ({$targetSks} SKS).";
        }

        if (count($failedMandatoryCourses) > 0) {
            $reasons[] = 'Terdapat ' . count($failedMandatoryCourses) . ' mata kuliah wajib dengan nilai di bawah batas kelulusan (D/E/BL/T).';
        }

        if ($totalSkpi < $targetSkpi) {
            $reasons[] = "Poin SKPI/SACS ({$totalSkpi} poin) belum memenuhi ambang batas kelulusan ({$targetSkpi} poin).";
        }

        $isEligible = empty($reasons);

        return [
            'is_eligible' => $isEligible,
            'sks_accumulated' => $passedSksTotal,
            'sks_target' => $targetSks,
            'skpi_points' => $totalSkpi,
            'skpi_target' => $targetSkpi,
            'failed_mandatory_courses' => $failedMandatoryCourses,
            'reasons' => $reasons,
        ];
    }

    /**
     * Check if a student is eligible to graduate.
     */
    public function canGraduate(Siswa $siswa, ?int $targetSks = null, ?int $targetSkpi = null): bool
    {
        $audit = $this->auditGraduation($siswa, $targetSks, $targetSkpi);
        return $audit['is_eligible'];
    }

    /**
     * Approve graduation and mutate status to 'Lulus'.
     */
    public function approveGraduation(Siswa $siswa, ?string $nomorIjazah = null, ?int $targetSks = null, ?int $targetSkpi = null): Siswa
    {
        $audit = $this->auditGraduation($siswa, $targetSks, $targetSkpi);

        if (!$audit['is_eligible']) {
            $reasonsText = implode('; ', $audit['reasons']);
            throw new DomainException("Mahasiswa belum memenuhi syarat yudisium/kelulusan: {$reasonsText}");
        }

        return DB::transaction(function () use ($siswa, $nomorIjazah) {
            $siswa->status_kelulusan = 'Lulus';
            $siswa->tgl_kelulusan = now();
            if ($nomorIjazah) {
                $siswa->nomor_ijazah = $nomorIjazah;
            }
            $siswa->save();

            return $siswa;
        });
    }
}
