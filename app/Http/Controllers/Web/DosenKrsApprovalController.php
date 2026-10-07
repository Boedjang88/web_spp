<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Guru;
use App\Models\Krs;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DosenKrsApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $guru = $user->guru ?? Guru::first();
        $activeTa = TahunAkademik::where('is_active', true)->first();

        $krsList = Krs::with(['siswa.kelas', 'details.kelasKuliah.mataKuliah'])
            ->where('id_tahun_akademik', $activeTa?->id ?? 1)
            ->when($guru && !$user->isAdmin() && !$user->isSuperAdmin(), function ($query) use ($guru) {
                $query->where('id_dosen_wali', $guru->id);
            })
            ->latest('updated_at')
            ->paginate(15);

        return view('siakad.dosen.krs-approval', compact('guru', 'activeTa', 'krsList'));
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $krs = Krs::findOrFail($id);
        $krs->update([
            'status_krs' => 'Disetujui',
            'tgl_persetujuan' => now(),
            'catatan_pembimbing' => $request->input('catatan_pembimbing', 'KRS disetujui Dosen Pembimbing Akademik.'),
        ]);

        ActivityLog::record('KRS_APPROVE', "Dosen PA menyetujui KRS Mahasiswa ID #{$krs->id_siswa}");

        return redirect()->back()->with('success', 'KRS Mahasiswa berhasil disetujui!');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate(['catatan_pembimbing' => 'required|string|max:255']);

        $krs = Krs::findOrFail($id);
        $krs->update([
            'status_krs' => 'Ditolak',
            'catatan_pembimbing' => $request->input('catatan_pembimbing'),
        ]);

        ActivityLog::record('KRS_REJECT', "Dosen PA menolak KRS Mahasiswa ID #{$krs->id_siswa}");

        return redirect()->back()->with('success', 'KRS Mahasiswa telah ditolak dengan catatan.');
    }
}
