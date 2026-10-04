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

        $users = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => User::count(),
            'superadmin' => User::where('role', 'superadmin')->count(),
            'admin' => User::where('role', 'admin')->orWhere('role', 'petugas')->count(),
            'guru' => User::where('role', 'guru')->count(),
            'siswa' => User::where('role', 'siswa')->count(),
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
            'role' => ['required', Rule::in(['superadmin', 'admin', 'guru', 'siswa'])],
            'id_guru' => 'nullable|exists:gurus,id',
            'id_siswa' => 'nullable|exists:siswas,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Clear relation if role doesn't match
        if ($validated['role'] !== 'guru') {
            $validated['id_guru'] = null;
        }
        if ($validated['role'] !== 'siswa') {
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
            'role' => ['required', Rule::in(['superadmin', 'admin', 'guru', 'siswa'])],
            'id_guru' => 'nullable|exists:gurus,id',
            'id_siswa' => 'nullable|exists:siswas,id',
            'is_active' => 'boolean',
        ]);

        // Protect primary superadmin
        if ($user->id === 1 && $validated['role'] !== 'superadmin') {
            return back()->with('error', 'Role Super Admin utama tidak dapat diubah.');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['role'] !== 'guru') {
            $validated['id_guru'] = null;
        }
        if ($validated['role'] !== 'siswa') {
            $validated['id_siswa'] = null;
        }

        $user->update($validated);

        ActivityLog::record('UPDATE_USER', "Memperbarui akun pengguna {$user->name} ({$user->role}) [ID: {$user->id}].");

        return redirect()->route('web.users.index')->with('success', "Akun {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1) {
            return back()->with('error', 'Super Admin terakhir tidak dapat dihapus.');
        }

        $nama = $user->name;
        $role = $user->role;
        $user->delete();

        ActivityLog::record('DELETE_USER', "Menghapus akun pengguna {$nama} ({$role}).");

        return redirect()->route('web.users.index')->with('success', "Akun {$nama} berhasil dihapus.");
    }
}
