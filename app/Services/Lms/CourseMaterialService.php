<?php

namespace App\Services\Lms;

use App\Models\CourseMaterial;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\URL;

class CourseMaterialService
{
    /**
     * Get published course materials available for student in a class.
     *
     * @param int $idKelasKuliah
     * @return Collection
     */
    public function getAvailableMaterialsForStudent(int $idKelasKuliah): Collection
    {
        return CourseMaterial::where('id_kelas_kuliah', $idKelasKuliah)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('publish_at')
                      ->orWhere('publish_at', '<=', now());
            })
            ->orderBy('minggu_ke', 'asc')
            ->get();
    }

    /**
     * Generate 5-minute temporary signed download URL for course material.
     *
     * @param CourseMaterial $material
     * @param int $minutes
     * @return string
     */
    public function generateSecureDownloadUrl(CourseMaterial $material, int $minutes = 5): string
    {
        return URL::temporarySignedRoute(
            'lms.material.download.signed',
            now()->addMinutes($minutes),
            ['id' => $material->id]
        );
    }
}
