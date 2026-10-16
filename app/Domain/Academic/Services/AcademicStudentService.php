<?php

namespace App\Domain\Academic\Services;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AcademicStudentService
{
    /**
     * Get paginated students with filters.
     */
    public function getPaginatedStudents(?string $search, ?string $idKelas, int $perPage = 10): LengthAwarePaginator
    {
        $query = Siswa::with(['kelas', 'spp']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if (!empty($idKelas)) {
            $query->where('id_kelas', $idKelas);
        }

        return $query->orderBy('nama')->paginate($perPage)->withQueryString();
    }

    /**
     * Get class options for dropdowns.
     */
    public function getKelasList(): Collection
    {
        return Kelas::orderBy('nama_kelas')->get();
    }

    /**
     * Get SPP rates list.
     */
    public function getSppList(): Collection
    {
        return Spp::orderBy('tahun', 'desc')->get();
    }
}
