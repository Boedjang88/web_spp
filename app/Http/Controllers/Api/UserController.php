<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['guru', 'siswa.kelas']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate((int) $request->get('per_page', 15));

        return $this->sendResponse($users, 'Daftar pengguna sistem berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['superadmin', 'admin', 'guru', 'siswa'])],
            'id_guru' => 'nullable|exists:gurus,id',
            'id_siswa' => 'nullable|exists:siswas,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['role'] !== 'guru') {
            $validated['id_guru'] = null;
        }
        if ($validated['role'] !== 'siswa') {
            $validated['id_siswa'] = null;
        }

        $user = User::create($validated);

        ActivityLog::record('CREATE_USER_API', "Membuat akun {$user->name} ({$user->role}) via REST API.", auth()->id());

        return $this->sendResponse($user->load(['guru', 'siswa']), 'Akun pengguna berhasil dibuat.', 201);
    }

    public function show(User $user): JsonResponse
    {
        return $this->sendResponse($user->load(['guru', 'siswa.kelas']), 'Detail pengguna berhasil diambil.');
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['sometimes', 'required', Rule::in(['superadmin', 'admin', 'guru', 'siswa'])],
            'id_guru' => 'nullable|exists:gurus,id',
            'id_siswa' => 'nullable|exists:siswas,id',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }
        }

        if (isset($validated['role'])) {
            if ($validated['role'] !== 'guru') {
                $validated['id_guru'] = null;
            }
            if ($validated['role'] !== 'siswa') {
                $validated['id_siswa'] = null;
            }
        }

        $user->update($validated);

        ActivityLog::record('UPDATE_USER_API', "Memperbarui akun {$user->name} ({$user->role}) via REST API.", auth()->id());

        return $this->sendResponse($user->load(['guru', 'siswa']), 'Data akun pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return $this->sendError('Tidak dapat menghapus akun Anda sendiri.', [], 400);
        }

        if ($user->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1) {
            return $this->sendError('Super Admin terakhir tidak dapat dihapus.', [], 400);
        }

        $nama = $user->name;
        $user->delete();

        ActivityLog::record('DELETE_USER_API', "Menghapus akun pengguna {$nama} via REST API.", auth()->id());

        return $this->sendResponse(null, "Akun {$nama} berhasil dihapus.");
    }
}
