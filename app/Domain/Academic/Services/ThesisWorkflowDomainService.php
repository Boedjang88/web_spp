<?php

namespace App\Domain\Academic\Services;

use App\Models\PembimbingSkripsi;
use App\Models\SidangSkripsi;
use App\Services\Audit\AuditTrailService;
use App\Services\Thesis\ThesisWorkflowService;

class ThesisWorkflowDomainService
{
    public function __construct(
        protected ThesisWorkflowService $workflowService,
        protected AuditTrailService $auditTrailService
    ) {}

    /**
     * Assign advisor with quota enforcement and domain audit trail.
     */
    public function assignAdvisor(int $idTugasAkhir, int $idGuru, string $peran = 'Pembimbing Utama', int $urutan = 1): PembimbingSkripsi
    {
        $advisor = $this->workflowService->assignAdvisor($idTugasAkhir, $idGuru, $peran, $urutan);

        $this->auditTrailService->record(
            userId: auth()->id(),
            actionEvent: 'ACADEMIC_THESIS_ADVISOR_ASSIGNED',
            payload: [
                'id_tugas_akhir' => $idTugasAkhir,
                'id_guru' => $idGuru,
                'peran' => $peran,
                'urutan' => $urutan,
            ]
        );

        return $advisor;
    }

    /**
     * Finalize examination board and record domain audit.
     */
    public function finalizeExaminationBoard(int $idSidang): SidangSkripsi
    {
        $sidang = $this->workflowService->finalizeExaminationBoard($idSidang);

        $this->auditTrailService->record(
            userId: auth()->id(),
            actionEvent: 'ACADEMIC_THESIS_DEFENSE_FINALIZED',
            payload: [
                'id_sidang' => $idSidang,
                'nilai_rata_rata' => $sidang->nilai_rata_rata,
                'nilai_huruf' => $sidang->nilai_huruf,
                'hasil_keputusan' => $sidang->hasil_keputusan,
            ]
        );

        return $sidang;
    }
}
