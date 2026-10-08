<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Siswa;
use App\Models\TugasAkhir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DbptaProposalController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();

        $tugasAkhir = TugasAkhir::where('id_siswa', $siswa?->id ?? 1)
            ->latest()
            ->first();

        return view('siakad.dbpta.proposal', compact('siswa', 'tugasAkhir'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'abstrak' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();
        $siswaId = $siswa?->id ?? 1;

        TugasAkhir::updateOrCreate(
            ['id_siswa' => $siswaId],
            [
                'judul' => $validated['judul'],
                'abstrak' => $validated['abstrak'],
                'status_persetujuan' => 'Pengajuan Proposal',
                'file_proposal_path' => 'documents/proposal/proposal_' . $siswaId . '.pdf',
            ]
        );

        ActivityLog::record('PROPOSAL_SUBMIT', "Mahasiswa mengajukan proposal skripsi/TA: {$validated['judul']}");

        return redirect()->route('siakad.dbpta.proposal.index')->with('success', 'Proposal Tugas Akhir / Skripsi berhasil diajukan untuk tinjauan Komisi Pembimbing!');
    }
}
