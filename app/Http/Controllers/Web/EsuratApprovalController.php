<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SuratAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EsuratApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->get('status', 'MENUNGGU_PERSETUJUAN');

        $suratList = SuratAkademik::with(['siswa.kelas'])
            ->when($statusFilter && $statusFilter !== 'ALL', fn($q) => $q->where('status', $statusFilter))
            ->latest()
            ->paginate(15);

        $totalPending = SuratAkademik::where('status', 'MENUNGGU_PERSETUJUAN')->count();
        $totalApproved = SuratAkademik::where('status', 'DISETUJUI')->count();
        $totalRejected = SuratAkademik::where('status', 'DITOLAK')->count();

        return view('siakad.baak.esurat-approval', compact('suratList', 'totalPending', 'totalApproved', 'totalRejected', 'statusFilter'));
    }

    public function approve(int $id): RedirectResponse
    {
        $surat = SuratAkademik::findOrFail($id);
        $surat->update([
            'status' => 'DISETUJUI',
            'tgl_terbit' => now(),
        ]);

        ActivityLog::record('ESURAT_APPROVE', "Admin menyetujui e-Surat #{$surat->nomor_surat}");

        return redirect()->back()->with('success', "Permohonan e-Surat #{$surat->nomor_surat} berhasil disetujui!");
    }

    public function reject(int $id): RedirectResponse
    {
        $surat = SuratAkademik::findOrFail($id);
        $surat->update(['status' => 'DITOLAK']);

        ActivityLog::record('ESURAT_REJECT', "Admin menolak e-Surat #{$surat->nomor_surat}");

        return redirect()->back()->with('success', "Permohonan e-Surat #{$surat->nomor_surat} telah ditolak.");
    }
}
