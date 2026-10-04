<?php

namespace App\Services\Academic;

use App\Models\EarlyWarningLog;
use App\Models\Krs;
use App\Models\PresensiKuliah;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EarlyWarningService
{
    /**
     * Run full EWS analysis for a single student.
     */
    public function analyzeStudent(Siswa $siswa): array
    {
        $createdLogs = [];

        // 1. Check Consecutive Inactive KRS (2 consecutive semesters)
        $inactiveKrsViolation = $this->checkInactiveKrs($siswa);
        if ($inactiveKrsViolation) {
            $createdLogs[] = $this->recordWarning(
                $siswa,
                'CRITICAL',
                'INACTIVE_KRS_CONSECUTIVE',
                $inactiveKrsViolation['reason'],
                $inactiveKrsViolation['payload']
            );
        }

        // 2. Check Consecutive Low GPA (IPS < 2.00 for 3 consecutive semesters)
        $lowGpaViolation = $this->checkLowGpa($siswa);
        if ($lowGpaViolation) {
            $createdLogs[] = $this->recordWarning(
                $siswa,
                'HIGH',
                'LOW_GPA_CONSECUTIVE',
                $lowGpaViolation['reason'],
                $lowGpaViolation['payload']
            );
        }

        // 3. Check Low Attendance (< 50% attendance)
        $attendanceViolation = $this->checkAttendance($siswa);
        if ($attendanceViolation) {
            $createdLogs[] = $this->recordWarning(
                $siswa,
                'MEDIUM',
                'LOW_ATTENDANCE',
                $attendanceViolation['reason'],
                $attendanceViolation['payload']
            );
        }

        return $createdLogs;
    }

    /**
     * Analyze all active students for early dropout risks.
     */
    public function analyzeAllStudents(): array
    {
        $students = Siswa::where('status_kelulusan', 'Aktif')->get();
        $totalEvaluated = $students->count();
        $totalViolations = 0;
        $logs = [];

        foreach ($students as $student) {
            $result = $this->analyzeStudent($student);
            if (!empty($result)) {
                $totalViolations += count($result);
                $logs[$student->id] = $result;
            }
        }

        return [
            'total_evaluated' => $totalEvaluated,
            'total_violations' => $totalViolations,
            'flagged_students_count' => count($logs),
            'logs' => $logs,
        ];
    }

    /**
     * Check if student has 2 consecutive inactive/empty KRS semesters.
     */
    public function checkInactiveKrs(Siswa $siswa): ?array
    {
        $allSemesters = TahunAkademik::orderBy('id', 'desc')->take(4)->get();
        if ($allSemesters->count() < 2) {
            return null;
        }

        $krsHistory = Krs::where('id_siswa', $siswa->id)
            ->whereIn('id_tahun_akademik', $allSemesters->pluck('id'))
            ->get()
            ->keyBy('id_tahun_akademik');

        $consecutiveInactive = 0;
        $inactiveSemesters = [];

        foreach ($allSemesters as $sem) {
            $krs = $krsHistory->get($sem->id);
            if (!$krs || $krs->total_sks_diambil == 0 || $krs->status_krs === 'DRAFT') {
                $consecutiveInactive++;
                $inactiveSemesters[] = $sem->nama_tahun;
                if ($consecutiveInactive >= 2) {
                    return [
                        'reason' => 'Mahasiswa tidak mengisi/mengambil KRS selama 2 semester berturut-turut (' . implode(', ', array_slice($inactiveSemesters, 0, 2)) . ').',
                        'payload' => [
                            'consecutive_count' => $consecutiveInactive,
                            'semesters' => $inactiveSemesters,
                        ],
                    ];
                }
            } else {
                $consecutiveInactive = 0;
                $inactiveSemesters = [];
            }
        }

        return null;
    }

    /**
     * Check if student has IPS < 2.00 for 3 consecutive semesters.
     */
    public function checkLowGpa(Siswa $siswa): ?array
    {
        $krsHistory = Krs::where('id_siswa', $siswa->id)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        if ($krsHistory->count() < 3) {
            return null;
        }

        $consecutiveLow = 0;
        $lowIpsRecords = [];

        foreach ($krsHistory as $krs) {
            $ips = (float) $krs->ips_lalu;
            if ($ips > 0 && $ips < 2.00) {
                $consecutiveLow++;
                $lowIpsRecords[] = [
                    'krs_id' => $krs->id,
                    'ips' => $ips,
                    'tahun_akademik_id' => $krs->id_tahun_akademik,
                ];

                if ($consecutiveLow >= 3) {
                    return [
                        'reason' => 'Indeks Prestasi Semester (IPS) berada di bawah 2.00 selama 3 semester berturut-turut.',
                        'payload' => [
                            'consecutive_count' => $consecutiveLow,
                            'records' => $lowIpsRecords,
                        ],
                    ];
                }
            } else {
                $consecutiveLow = 0;
                $lowIpsRecords = [];
            }
        }

        return null;
    }

    /**
     * Check attendance rate in active classes (< 50%).
     */
    public function checkAttendance(Siswa $siswa): ?array
    {
        // Check PresensiKuliah for student's enrolled classes
        $presensiQuery = PresensiKuliah::where('id_siswa', $siswa->id);
        $totalMeetings = $presensiQuery->count();

        if ($totalMeetings < 4) {
            // Need at least 4 meetings to judge mid-semester attendance trend
            return null;
        }

        $attendedMeetings = (clone $presensiQuery)->where('status_hadir', 'HADIR')->count();
        $attendanceRate = ($attendedMeetings / $totalMeetings) * 100;

        if ($attendanceRate < 50.0) {
            return [
                'reason' => "Tingkat kehadiran perkuliahan ({$attendanceRate}%) berada di bawah batas minimum 50%.",
                'payload' => [
                    'total_meetings' => $totalMeetings,
                    'attended' => $attendedMeetings,
                    'rate_percent' => round($attendanceRate, 2),
                ],
            ];
        }

        return null;
    }

    /**
     * Record early warning log if not duplicate open log.
     */
    protected function recordWarning(Siswa $siswa, string $severity, string $triggerType, string $reason, array $payload): EarlyWarningLog
    {
        // Find Dosen PA / Wali from recent KRS or Siswa profile
        $dosenPaId = Krs::where('id_siswa', $siswa->id)->latest()->value('id_dosen_wali');

        // Check if an unresolved log already exists for this student and trigger
        $existingLog = EarlyWarningLog::where('id_siswa', $siswa->id)
            ->where('trigger_type', $triggerType)
            ->where('is_resolved', false)
            ->first();

        if ($existingLog) {
            $existingLog->severity = $severity;
            $existingLog->trigger_reason = $reason;
            $existingLog->metrics_payload = $payload;
            $existingLog->id_dosen_pa = $dosenPaId ?? $existingLog->id_dosen_pa;
            $existingLog->save();
            return $existingLog;
        }

        return EarlyWarningLog::create([
            'id_siswa' => $siswa->id,
            'id_dosen_pa' => $dosenPaId,
            'severity' => $severity,
            'trigger_type' => $triggerType,
            'trigger_reason' => $reason,
            'metrics_payload' => $payload,
            'is_resolved' => false,
        ]);
    }
}
