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
    public const MIN_PROPOSAL_SKS = 110;

    public function getMinSks(): int
    {
        return (int) config('academic.skripsi_min_sks', self::MIN_PROPOSAL_SKS);
    }

    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa;

        $tugasAkhir = $siswa
            ? TugasAkhir::where('id_siswa', $siswa->id)->latest()->first()
            : null;

        $minSks = $this->getMinSks();
        $passedSks = $siswa ? app(\App\Services\Academic\GraduationClearanceService::class)->getTotalPassedSks($siswa) : 0;
        $isGraduatedOrInactive = $siswa && in_array($siswa->status_kelulusan, ['Lulus', 'Lulus Wisuda', 'LULUS', 'Dikeluarkan', 'Drop Out']);
        $isEligible = !$isGraduatedOrInactive && ($passedSks >= $minSks);

        return view('siakad.dbpta.proposal', compact('siswa', 'tugasAkhir', 'passedSks', 'isEligible', 'minSks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'abstrak' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa;
        if (!$siswa) {
            return back()->with('error', 'Profil mahasiswa Anda belum terhubung. Tidak dapat mengajukan proposal skripsi.');
        }
        $siswaId = $siswa->id;

        if (in_array($siswa->status_kelulusan, ['Lulus', 'Lulus Wisuda', 'LULUS', 'Dikeluarkan', 'Drop Out'])) {
            return back()->with('error', "Mahasiswa dengan status akademik '{$siswa->status_kelulusan}' tidak diizinkan mengajukan proposal skripsi.");
        }

        $minSks = $this->getMinSks();
        $passedSks = app(\App\Services\Academic\GraduationClearanceService::class)->getTotalPassedSks($siswa);
        if ($passedSks < $minSks) {
            return back()->with('error', "Mahasiswa belum memenuhi syarat SKS untuk mengajukan proposal Tugas Akhir/Skripsi. SKS lulus saat ini: {$passedSks} SKS (Syarat minimum: {$minSks} SKS).");
        }

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
