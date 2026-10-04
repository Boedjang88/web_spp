<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SppController extends Controller
{
    public function index(Request $request): View
    {
        $query = Spp::withCount('siswas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tahun', 'like', "%{$search}%")
                  ->orWhere('nominal', 'like', "%{$search}%");
        }

        $sppList = $query->orderBy('tahun', 'desc')->paginate(10)->withQueryString();

        return view('spp.index', compact('sppList'));
    }

    public function create(): View
    {
        return view('spp.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|digits:4|unique:spps,tahun',
            'nominal' => 'required|numeric|min:0',
        ], [
            'tahun.required' => 'Tahun SPP wajib diisi.',
            'tahun.unique' => 'Tahun SPP sudah ada.',
            'nominal.required' => 'Nominal SPP wajib diisi.',
        ]);

        $spp = Spp::create($validated);
        \App\Models\ActivityLog::record('SPP_CREATE', "Menambahkan tarif SPP tahun {$spp->tahun} sebesar Rp " . number_format($spp->nominal, 0, ',', '.'));

        return redirect()->route('web.spp.index')->with('success', 'Tarif SPP berhasil ditambahkan.');
    }

    public function edit(string|int $id): View
    {
        $spp = Spp::findOrFail($id);
        return view('spp.edit', compact('spp'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $spp = Spp::findOrFail($id);

        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'digits:4', Rule::unique('spps', 'tahun')->ignore($spp->id)],
            'nominal' => 'required|numeric|min:0',
        ], [
            'tahun.required' => 'Tahun SPP wajib diisi.',
            'tahun.unique' => 'Tahun SPP sudah digunakan.',
            'nominal.required' => 'Nominal SPP wajib diisi.',
        ]);

        $spp->update($validated);
        \App\Models\ActivityLog::record('SPP_UPDATE', "Memperbarui tarif SPP tahun {$spp->tahun} menjadi Rp " . number_format($spp->nominal, 0, ',', '.'));

        return redirect()->route('web.spp.index')->with('success', 'Tarif SPP berhasil diperbarui.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $spp = Spp::findOrFail($id);

        if ($spp->siswas()->count() > 0 || $spp->pembayarans()->count() > 0) {
            return back()->with('error', "Tarif SPP tahun {$spp->tahun} tidak dapat dihapus karena masih digunakan oleh data siswa atau riwayat transaksi pembayaran.");
        }

        $tahun = $spp->tahun;
        $spp->delete();
        \App\Models\ActivityLog::record('SPP_DELETE', "Menghapus tarif SPP tahun {$tahun}.");

        return redirect()->route('web.spp.index')->with('success', 'Tarif SPP berhasil dihapus.');
    }
}
