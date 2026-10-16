<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Spp;
use App\Models\Ukt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UktController extends Controller
{
    protected function getIndexRoute(): string
    {
        if (request()->routeIs('web.spp.*') || request()->is('web/spp*')) {
            return 'web.spp.index';
        }
        return Route::has('web.ukt.index') ? 'web.ukt.index' : 'web.spp.index';
    }

    public function index(Request $request): View
    {
        $query = Spp::withCount('siswas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tahun', 'like', "%{$search}%")
                  ->orWhere('nominal', 'like', "%{$search}%");
        }

        $sppList = $query->orderBy('tahun', 'desc')->paginate(10)->withQueryString();

        $viewName = view()->exists('ukt.index') ? 'ukt.index' : 'spp.index';
        return view($viewName, compact('sppList'));
    }

    public function create(): View
    {
        $viewName = view()->exists('ukt.create') ? 'ukt.create' : 'spp.create';
        return view($viewName);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|digits:4|unique:spps,tahun',
            'nominal' => 'required|numeric|min:0',
        ], [
            'tahun.required' => 'Tahun UKT wajib diisi.',
            'tahun.unique' => 'Tahun UKT sudah ada.',
            'nominal.required' => 'Nominal UKT wajib diisi.',
        ]);

        $spp = Spp::create($validated);

        // Auto-sync into Ukt SIAKAD
        try {
            Ukt::updateOrCreate(
                ['tahun' => $spp->tahun],
                [
                    'nominal' => $spp->nominal,
                ]
            );
        } catch (\Throwable $e) {}

        ActivityLog::record('UKT_CREATE', "Menambahkan tarif UKT tahun {$spp->tahun} sebesar Rp " . number_format($spp->nominal, 0, ',', '.'));

        return redirect()->route($this->getIndexRoute())->with('success', 'Tarif UKT berhasil ditambahkan.');
    }

    public function edit(string|int $id): View
    {
        $spp = Spp::findOrFail($id);
        $viewName = view()->exists('ukt.edit') ? 'ukt.edit' : 'spp.edit';
        return view($viewName, compact('spp'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $spp = Spp::findOrFail($id);

        $validated = $request->validate([
            'tahun' => ['required', 'integer', 'digits:4', Rule::unique('spps', 'tahun')->ignore($spp->id)],
            'nominal' => 'required|numeric|min:0',
        ], [
            'tahun.required' => 'Tahun UKT wajib diisi.',
            'tahun.unique' => 'Tahun UKT sudah digunakan.',
            'nominal.required' => 'Nominal UKT wajib diisi.',
        ]);

        $spp->update($validated);

        // Auto-sync into Ukt SIAKAD
        try {
            Ukt::updateOrCreate(
                ['tahun' => $spp->tahun],
                [
                    'nominal' => $spp->nominal,
                ]
            );
        } catch (\Throwable $e) {}

        ActivityLog::record('UKT_UPDATE', "Memperbarui tarif UKT tahun {$spp->tahun} menjadi Rp " . number_format($spp->nominal, 0, ',', '.'));

        return redirect()->route($this->getIndexRoute())->with('success', 'Tarif UKT berhasil diperbarui.');
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $spp = Spp::findOrFail($id);

        if ($spp->siswas()->count() > 0 || $spp->pembayarans()->count() > 0) {
            return back()->with('error', "Tarif UKT tahun {$spp->tahun} tidak dapat dihapus karena masih digunakan oleh data mahasiswa atau riwayat transaksi pembayaran.");
        }

        $tahun = $spp->tahun;
        $spp->delete();
        ActivityLog::record('UKT_DELETE', "Menghapus tarif UKT tahun {$tahun}.");

        return redirect()->route($this->getIndexRoute())->with('success', 'Tarif UKT berhasil dihapus.');
    }
}
