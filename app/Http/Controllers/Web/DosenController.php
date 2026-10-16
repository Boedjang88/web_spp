<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Dosen;
use App\Models\Guru;
use App\Models\ProgramStudi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DosenController extends Controller
{
    protected function getIndexRoute(): string
    {
        if (request()->routeIs('web.guru.*') || request()->is('web/guru*')) {
            return 'web.guru.index';
        }
        return Route::has('web.dosen.index') ? 'web.dosen.index' : 'web.guru.index';
    }

    public function index(Request $request): View
    {
        $query = Guru::withCount('jadwals');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_guru', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $gurus = $query->orderBy('nama_guru')->paginate(10)->withQueryString();

        $viewName = view()->exists('dosen.index') ? 'dosen.index' : 'guru.index';
        return view($viewName, compact('gurus'));
    }

    public function create(): View
    {
        $viewName = view()->exists('dosen.create') ? 'dosen.create' : 'guru.create';
        return view($viewName);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:20|unique:gurus,nip',
            'nama_guru' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ], [
            'nama_guru.required' => 'Nama dosen wajib diisi.',
            'nip.unique' => 'NIDN / NIP sudah terdaftar untuk dosen lain.',
        ]);

        $guru = Guru::create($validated);

        // Auto-sync into Dosen SIAKAD
        try {
            $nip = $guru->nip ?? ('DOS-' . str_pad($guru->id, 4, '0', STR_PAD_LEFT));
            Dosen::updateOrCreate(
                ['nip' => $nip],
                [
                    'nama_dosen' => $guru->nama_guru,
                    'jenis_kelamin' => $guru->jenis_kelamin,
                    'no_telp' => $guru->no_telp,
                    'email' => $guru->email,
                    'alamat' => $guru->alamat,
                    'id_prodi' => ProgramStudi::first()?->id ?? 1,
                    'is_active' => true,
                ]
            );
        } catch (\Throwable $e) {}

        ActivityLog::record('DOSEN_CREATE', "Menambahkan data dosen & tenaga pendidik {$guru->nama_guru}.");

        return redirect()->route($this->getIndexRoute())->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(string|int $id): View
    {
        $guru = Guru::findOrFail($id);
        $viewName = view()->exists('dosen.edit') ? 'dosen.edit' : 'guru.edit';
        return view($viewName, compact('guru'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:20', Rule::unique('gurus', 'nip')->ignore($guru->id)],
            'nama_guru' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ]);

        $guru->update($validated);

        // Auto-sync into Dosen SIAKAD
        try {
            $nip = $guru->nip ?? ('DOS-' . str_pad($guru->id, 4, '0', STR_PAD_LEFT));
            Dosen::updateOrCreate(
                ['nip' => $nip],
                [
                    'nama_dosen' => $guru->nama_guru,
                    'jenis_kelamin' => $guru->jenis_kelamin,
                    'no_telp' => $guru->no_telp,
                    'email' => $guru->email,
                    'alamat' => $guru->alamat,
                ]
            );
        } catch (\Throwable $e) {}

        ActivityLog::record('DOSEN_UPDATE', "Memperbarui data dosen {$guru->nama_guru}.");

        return redirect()->route($this->getIndexRoute())->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $guru = Guru::findOrFail($id);

        if ($guru->jadwals()->count() > 0 || $guru->nilais()->count() > 0) {
            return back()->with('error', "Dosen {$guru->nama_guru} tidak dapat dihapus karena masih terhubung dengan data jadwal perkuliahan atau rekap nilai mahasiswa.");
        }

        $nama = $guru->nama_guru;
        $guru->delete();

        ActivityLog::record('DOSEN_DELETE', "Menghapus data dosen {$nama}.");

        return redirect()->route($this->getIndexRoute())->with('success', 'Data dosen berhasil dihapus.');
    }
}
