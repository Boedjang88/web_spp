<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GuruController extends Controller
{
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

        return view('guru.index', compact('gurus'));
    }

    public function create(): View
    {
        return view('guru.create');
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
            'nama_guru.required' => 'Nama guru wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar untuk guru lain.',
        ]);

        $guru = Guru::create($validated);
        ActivityLog::record('GURU_CREATE', "Menambahkan data tenaga pendidik {$guru->nama_guru}.");

        return redirect()->route('web.guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(string|int $id): View
    {
        $guru = Guru::findOrFail($id);
        return view('guru.edit', compact('guru'));
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
        ActivityLog::record('GURU_UPDATE', "Memperbarui data guru {$guru->nama_guru}.");

        return redirect()->route('web.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $guru = Guru::findOrFail($id);
        $nama = $guru->nama_guru;
        $guru->delete();

        ActivityLog::record('GURU_DELETE', "Menghapus data guru {$nama}.");

        return redirect()->route('web.guru.index')->with('success', 'Data guru berhasil dihapus.');
    }
}
