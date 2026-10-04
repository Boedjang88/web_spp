<?php

namespace App\Policies;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MahasiswaPolicy
{
    use HandlesAuthorization;

    /**
     * Superadmin and BAAK can perform any action.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin() || $user->isBaak()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBaak() || $user->isDosen();
    }

    /**
     * Determine whether the user can view the model (Anti-IDOR).
     */
    public function view(User $user, Mahasiswa $mahasiswa): bool
    {
        if ($user->isSuperAdmin() || $user->isBaak() || $user->isDosen()) {
            return true;
        }

        if ($user->isMahasiswa()) {
            $studentId = $user->id_mahasiswa ?? $user->id_siswa;
            return (int) $studentId === (int) $mahasiswa->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isBaak();
    }

    /**
     * Determine whether the user can update the model (Anti-IDOR).
     */
    public function update(User $user, Mahasiswa $mahasiswa): bool
    {
        if ($user->isSuperAdmin() || $user->isBaak()) {
            return true;
        }

        if ($user->isMahasiswa()) {
            $studentId = $user->id_mahasiswa ?? $user->id_siswa;
            return (int) $studentId === (int) $mahasiswa->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mahasiswa $mahasiswa): bool
    {
        return $user->isSuperAdmin();
    }
}
