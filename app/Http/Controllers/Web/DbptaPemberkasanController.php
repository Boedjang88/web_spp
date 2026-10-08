<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Siswa;
use App\Models\TugasAkhir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DbptaPemberkasanController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();

        $tugasAkhir = TugasAkhir::where('id_siswa', $siswa?->id ?? 1)->first();

        return view('siakad.dbpta.pemberkasan', compact('siswa', 'tugasAkhir'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'link_berkas' => 'required|url|max:255',
        ]);

        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();

        if ($siswa && $tugasAkhir = TugasAkhir::where('id_siswa', $siswa->id)->first()) {
            $tugasAkhir->update([
                'file_revisi_path' => $validated['link_berkas'],
            ]);
        }

        ActivityLog::record('PEMBERKASAN_SUBMIT', "Mahasiswa mengunggah berkas final skripsi/TA.");

        return redirect()->route('siakad.dbpta.pemberkasan.index')->with('success', 'Pemberkasan dokumen final Skripsi berhasil diunggah!');
    }
}
