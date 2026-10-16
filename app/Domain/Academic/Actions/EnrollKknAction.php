<?php

namespace App\Domain\Academic\Actions;

use App\Domain\Academic\Services\KknRegistrationService;
use App\Models\KknRegistrasi;
use App\Models\Siswa;

class EnrollKknAction
{
    public function __construct(
        protected KknRegistrationService $registrationService
    ) {}

    /**
     * Execute student KKN registration.
     */
    public function execute(Siswa $siswa, array $payload, bool $enforcePrerequisites = true): KknRegistrasi
    {
        return $this->registrationService->registerStudent($siswa, $payload, $enforcePrerequisites);
    }
}
