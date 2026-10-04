<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Mapel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MapelController extends Controller
{
    public function index(Request $request): View
    {
        $query = Mapel::withCount('jadwals');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%")
                  ->orWhere('kode_mapel', 'like', "%{$search}%")
                  ->orWhere('kelompok', 'like', "%{$search}%");
            });
        }

        $mapels = $query->orderBy('nama_mapel')->paginate(10)->withQueryString();

        return view('mapel.index', compact('mapels'));
    }

    public function create(): View
    {
        return view('mapel.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mapels,kode_mapel',
            'nama_mapel' => 'required|string|max:100',
            'kelompok' => 'required|string|max:50',
            'kkm' => 'required|integer|min:0|max:100',
        ], [
            'kode_mapel.required' => 'Kode mapel wajib diisi.',
            'kode_mapel.unique' => 'Kode mapel sudah digunakan.',
            'nama_mapel.required' => 'Nama mata pelajaran wajib diisi.',
        ]);

        $mapel = Mapel::create($validated);
        ActivityLog::record('MAPEL_CREATE', "Menambahkan mata pelajaran {$mapel->nama_mapel} ({$mapel->kode_mapel}).");

        return redirect()->route('web.mapel.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(string|int $id): View
    {
        $mapel = Mapel::findOrFail($id);
        return view('mapel.edit', compact('mapel'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $mapel = Mapel::findOrFail($id);

        $validated = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:20', Rule::unique('mapels', 'kode_mapel')->ignore($mapel->id)],
            'nama_mapel' => 'required|string|max:100',
            'kelompok' => 'required|string|max:50',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        $mapel->update($validated);
        ActivityLog::record('MAPEL_UPDATE', "Memperbarui data mapel {$mapel->nama_mapel}.");

        return redirect()->route('web.mapel.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $mapel = Mapel::findOrFail($id);

        if ($mapel->jadwals()->count() > 0 || $mapel->nilais()->count() > 0) {
            return back()->with('error', "Mata pelajaran {$mapel->nama_mapel} tidak dapat dihapus karena masih terhubung dengan data jadwal pelajaran atau nilai siswa.");
        }

        $nama = $mapel->nama_mapel;
        $mapel->delete();

        ActivityLog::record('MAPEL_DELETE', "Menghapus mata pelajaran {$nama}.");

        return redirect()->route('web.mapel.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
