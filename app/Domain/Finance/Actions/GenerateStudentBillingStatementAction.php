<?php

namespace App\Domain\Finance\Actions;

use App\Domain\Finance\Services\SppBillingService;
use App\Models\ActivityLog;
use App\Models\Siswa;
use App\Services\Audit\AuditTrailService;

class GenerateStudentBillingStatementAction
{
    public function __construct(
        protected SppBillingService $billingService,
        protected AuditTrailService $auditTrailService
    ) {}

    /**
     * Execute billing statement generation and immutable audit log.
     */
    public function execute(Siswa $siswa): array
    {
        $statement = $this->billingService->getBillingStatementData($siswa);

        ActivityLog::record(
            'SURAT_TAGIHAN_PRINT',
            "Mencetak/melihat surat tagihan resmi siswa {$siswa->nama} (NISN: {$siswa->nisn})."
        );

        $this->auditTrailService->record(
            userId: auth()->id(),
            actionEvent: 'FINANCIAL_BILLING_STATEMENT_GENERATED',
            payload: [
                'id_siswa' => $siswa->id,
                'nisn' => $siswa->nisn,
                'total_tunggakan' => $statement['total_tunggakan'],
                'is_lunas' => $statement['is_lunas'],
            ]
        );

        return $statement;
    }
}
