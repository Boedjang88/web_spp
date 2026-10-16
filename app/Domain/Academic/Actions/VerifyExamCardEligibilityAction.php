<?php

namespace App\Domain\Academic\Actions;

use App\Domain\Finance\Actions\CheckFinancialClearanceAction;
use App\Models\ActivityLog;
use App\Models\Siswa;
use App\Services\Audit\AuditTrailService;

class VerifyExamCardEligibilityAction
{
    public function __construct(
        protected CheckFinancialClearanceAction $financialClearanceAction,
        protected AuditTrailService $auditTrailService
    ) {}

    /**
     * Check if student can print and sit for exam card based on academic & financial clearance.
     */
    public function execute(Siswa $siswa): array
    {
        $isLunas = $this->financialClearanceAction->execute($siswa);
        $tunggakan = $siswa->info_tunggakan;

        // Verify academic attendance eligibility (SN-Dikti min 75% rule)
        $rekap = $siswa->rekap_kehadiran;
        $totalMeetings = (int) ($rekap['hadir'] + $rekap['izin'] + $rekap['sakit'] + $rekap['alpa']);
        $attendanceRate = $totalMeetings > 0 ? round(($rekap['hadir'] / $totalMeetings) * 100, 1) : 100.0;
        $minAttendance = (float) config('academic.min_attendance_exam_percent', 75);
        $isAttendanceEligible = ($totalMeetings === 0) || ($attendanceRate >= $minAttendance);

        $canPrint = $isLunas && $isAttendanceEligible;

        $this->auditTrailService->record(
            userId: auth()->id(),
            actionEvent: 'ACADEMIC_EXAM_CARD_CLEARANCE_CHECK',
            payload: [
                'id_siswa' => $siswa->id,
                'nisn' => $siswa->nisn,
                'is_eligible' => $canPrint,
                'is_financial_cleared' => $isLunas,
                'attendance_rate' => $attendanceRate,
                'is_attendance_eligible' => $isAttendanceEligible,
                'total_arrears' => $tunggakan['total_tunggakan'] ?? 0,
            ]
        );

        ActivityLog::record(
            'KARTU_UJIAN_CHECK',
            "Pengecekan kelayakan kartu ujian siswa {$siswa->nama} (Status Lunas: " . ($isLunas ? 'YES' : 'NO') . ", Presensi: {$attendanceRate}% - " . ($isAttendanceEligible ? 'ELIGIBLE' : 'BLOCKED') . ')'
        );

        return [
            'siswa' => $siswa,
            'tunggakan' => $tunggakan,
            'is_lunas' => $isLunas,
            'attendance_rate' => $attendanceRate,
            'min_attendance' => $minAttendance,
            'is_attendance_eligible' => $isAttendanceEligible,
            'can_print' => $canPrint,
        ];
    }
}
