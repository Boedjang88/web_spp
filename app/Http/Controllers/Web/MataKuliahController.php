<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Mapel;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MataKuliahController extends Controller
{
    protected function getIndexRoute(): string
    {
        if (request()->routeIs('web.mapel.*') || request()->is('web/mapel*')) {
            return 'web.mapel.index';
        }
        return Route::has('web.matakuliah.index') ? 'web.matakuliah.index' : 'web.mapel.index';
    }

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

        $viewName = view()->exists('matakuliah.index') ? 'matakuliah.index' : 'mapel.index';
        return view($viewName, compact('mapels'));
    }

    public function create(): View
    {
        $viewName = view()->exists('matakuliah.create') ? 'matakuliah.create' : 'mapel.create';
        return view($viewName);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mapels,kode_mapel',
            'nama_mapel' => 'required|string|max:100',
            'kelompok' => 'required|string|max:50',
            'kkm' => 'required|integer|min:0|max:100',
        ], [
            'kode_mapel.required' => 'Kode mata kuliah wajib diisi.',
            'kode_mapel.unique' => 'Kode mata kuliah sudah digunakan.',
            'nama_mapel.required' => 'Nama mata kuliah wajib diisi.',
        ]);

        $mapel = Mapel::create($validated);

        // Auto-sync into MataKuliah SIAKAD
        try {
            MataKuliah::updateOrCreate(
                ['kode_mk' => $mapel->kode_mapel],
                [
                    'nama_mk' => $mapel->nama_mapel,
                    'sks_total' => 3,
                    'sks_teori' => 2,
                    'sks_praktik' => 1,
                    'semester_paket' => 1,
                    'sifat' => 'WAJIB',
                    'id_prodi' => ProgramStudi::first()?->id ?? 1,
                ]
            );
        } catch (\Throwable $e) {}

        ActivityLog::record('MATAKULIAH_CREATE', "Menambahkan mata kuliah {$mapel->nama_mapel} ({$mapel->kode_mapel}).");

        return redirect()->route($this->getIndexRoute())->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(string|int $id): View
    {
        $mapel = Mapel::findOrFail($id);
        $viewName = view()->exists('matakuliah.edit') ? 'matakuliah.edit' : 'mapel.edit';
        return view($viewName, compact('mapel'));
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

        // Auto-sync into MataKuliah SIAKAD
        try {
            MataKuliah::updateOrCreate(
                ['kode_mk' => $mapel->kode_mapel],
                [
                    'nama_mk' => $mapel->nama_mapel,
                ]
            );
        } catch (\Throwable $e) {}

        ActivityLog::record('MATAKULIAH_UPDATE', "Memperbarui data mata kuliah {$mapel->nama_mapel}.");

        return redirect()->route($this->getIndexRoute())->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $mapel = Mapel::findOrFail($id);

        if ($mapel->jadwals()->count() > 0 || $mapel->nilais()->count() > 0) {
            return back()->with('error', "Mata kuliah {$mapel->nama_mapel} tidak dapat dihapus karena masih terhubung dengan data jadwal perkuliahan atau nilai mahasiswa.");
        }

        $nama = $mapel->nama_mapel;
        $mapel->delete();

        ActivityLog::record('MATAKULIAH_DELETE', "Menghapus mata kuliah {$nama}.");

        return redirect()->route($this->getIndexRoute())->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
