<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $query = Kelas::withCount('siswas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('kompetensi_keahlian', 'like', "%{$search}%");
            });
        }

        $kelas = $query->orderBy('nama_kelas')->paginate(10)->withQueryString();

        return view('kelas.index', compact('kelas'));
    }

    public function create(): View
    {
        return view('kelas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas',
            'kompetensi_keahlian' => 'required|string|max:100',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah ada.',
            'kompetensi_keahlian.required' => 'Kompetensi keahlian wajib diisi.',
        ]);

        $kelas = Kelas::create($validated);
        \App\Models\ActivityLog::record('KELAS_CREATE', "Menambahkan kelas baru {$kelas->nama_kelas} ({$kelas->kompetensi_keahlian}).");

        return redirect()->route('web.kelas.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(string|int $id): View
    {
        $kelas = Kelas::findOrFail($id);
        return view('kelas.edit', compact('kelas'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:50', Rule::unique('kelas', 'nama_kelas')->ignore($kelas->id)],
            'kompetensi_keahlian' => 'required|string|max:100',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah digunakan.',
            'kompetensi_keahlian.required' => 'Kompetensi keahlian wajib diisi.',
        ]);

        $kelas->update($validated);
        \App\Models\ActivityLog::record('KELAS_UPDATE', "Memperbarui data kelas {$kelas->nama_kelas}.");

        return redirect()->route('web.kelas.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $kelas = Kelas::withCount('siswas')->findOrFail($id);

        if ($kelas->siswas_count > 0) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena memiliki data siswa terkait.');
        }

        $nama = $kelas->nama_kelas;
        $kelas->delete();
        \App\Models\ActivityLog::record('KELAS_DELETE', "Menghapus data kelas {$nama}.");

        return redirect()->route('web.kelas.index')->with('success', 'Data kelas berhasil dihapus.');
    }
}
