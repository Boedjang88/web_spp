<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['guru', 'dosen', 'siswa.kelas', 'mahasiswa.prodi']);

        if ($request->filled('role')) {
            $role = $request->role;
            if (in_array($role, ['dosen', 'guru'])) {
                $query->whereIn('role', ['dosen', 'guru', 'lecturer']);
            } elseif (in_array($role, ['mahasiswa', 'siswa'])) {
                $query->whereIn('role', ['mahasiswa', 'siswa', 'student']);
            } elseif ($role === 'admin') {
                $query->whereIn('role', ['admin', 'petugas', 'baak']);
            } else {
                $query->where('role', $role);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'superadmin' => User::whereIn('role', ['superadmin', 'super_admin'])->count(),
            'admin' => User::whereIn('role', ['admin', 'petugas', 'baak'])->count(),
            'dosen' => User::whereIn('role', ['dosen', 'guru', 'lecturer'])->count(),
            'mahasiswa' => User::whereIn('role', ['mahasiswa', 'siswa', 'student'])->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function create(): View
    {
        $gurus = Guru::orderBy('nama_guru')->get();
        $siswas = Siswa::with('kelas')->orderBy('nama')->get();

        return view('users.create', compact('gurus', 'siswas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['superadmin', 'admin', 'dosen', 'guru', 'mahasiswa', 'siswa', 'petugas', 'baak'])],
            'id_guru' => 'nullable|exists:gurus,id',
            'id_siswa' => 'nullable|exists:siswas,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['consent_pdp_at'] = now();

        if (!in_array($validated['role'], ['dosen', 'guru'])) {
            $validated['id_guru'] = null;
        }
        if (!in_array($validated['role'], ['mahasiswa', 'siswa'])) {
            $validated['id_siswa'] = null;
        }

        $user = User::create($validated);

        ActivityLog::record('CREATE_USER', "Membuat akun pengguna baru {$user->name} ({$user->role}) [ID: {$user->id}].");

        return redirect()->route('web.users.index')->with('success', "Akun pengguna {$user->name} ({$user->role}) berhasil dibuat.");
    }

    public function edit(User $user): View
    {
        $gurus = Guru::orderBy('nama_guru')->get();
        $siswas = Siswa::with('kelas')->orderBy('nama')->get();

        return view('users.edit', compact('user', 'gurus', 'siswas'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role' => ['required', Rule::in(['superadmin', 'admin', 'dosen', 'guru', 'mahasiswa', 'siswa', 'petugas', 'baak'])],
            'id_guru' => 'nullable|exists:gurus,id',
            'id_siswa' => 'nullable|exists:siswas,id',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        if (!in_array($validated['role'], ['dosen', 'guru'])) {
            $validated['id_guru'] = null;
        }
        if (!in_array($validated['role'], ['mahasiswa', 'siswa'])) {
            $validated['id_siswa'] = null;
        }

        $user->update($validated);

        ActivityLog::record('UPDATE_USER', "Perbarui akun pengguna {$user->name} ({$user->role}) [ID: {$user->id}].");

        return redirect()->route('web.users.index')->with('success', "Akun pengguna {$user->name} ({$user->role}) berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::record('DELETE_USER', "Menghapus akun pengguna {$name}.");

        return redirect()->route('web.users.index')->with('success', "Akun pengguna {$name} berhasil dihapus.");
    }
}
