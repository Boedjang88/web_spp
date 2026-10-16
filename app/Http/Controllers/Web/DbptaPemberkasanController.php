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
        $siswa = $user?->siswa ?? $user?->mahasiswa;

        $tugasAkhir = $siswa ? TugasAkhir::where('id_siswa', $siswa->id)->first() : null;

        return view('siakad.dbpta.pemberkasan', compact('siswa', 'tugasAkhir'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'link_berkas' => 'required|url|max:255',
        ]);

        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa;

        if (!$siswa) {
            return back()->with('error', 'Profil mahasiswa Anda belum terhubung. Tidak dapat mengunggah berkas.');
        }

        if ($tugasAkhir = TugasAkhir::where('id_siswa', $siswa->id)->first()) {
            $tugasAkhir->update([
                'file_revisi_path' => $validated['link_berkas'],
            ]);

            // Apply digital signature (BSrE) to final thesis submission
            $signatureService = app(\App\Services\Document\DigitalSignatureService::class);
            $signatureService->signTugasAkhir($tugasAkhir, [
                'name' => $siswa->nama,
                'identifier' => $siswa->nisn ?? $siswa->nis ?? (string) $siswa->id,
                'role' => 'Mahasiswa / Penulis Tugas Akhir',
            ], \App\Services\Document\DigitalSignatureService::PROVIDER_BSRE);
        }

        ActivityLog::record('PEMBERKASAN_SUBMIT', "Mahasiswa mengunggah dan menandatangani berkas final skripsi/TA secara digital.");

        return redirect()->route('siakad.dbpta.pemberkasan.index')->with('success', 'Pemberkasan dokumen final Skripsi berhasil diunggah dan ditandatangani secara digital!');
    }
}
