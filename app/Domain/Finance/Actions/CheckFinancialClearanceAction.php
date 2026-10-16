<?php

namespace App\Domain\Finance\Actions;

use App\Domain\Finance\Services\SppBillingService;
use App\Models\Siswa;

class CheckFinancialClearanceAction
{
    public function __construct(
        protected SppBillingService $billingService
    ) {}

    /**
     * Execute financial clearance verification.
     */
    public function execute(Siswa $siswa): bool
    {
        return $this->billingService->isFinanciallyCleared($siswa);
    }
}
