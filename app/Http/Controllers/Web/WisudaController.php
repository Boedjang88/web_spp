<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Siswa;
use App\Services\Academic\GraduationClearanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WisudaController extends Controller
{
    public function index(GraduationClearanceService $clearanceService): View
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();

        $clearanceReport = null;
        if ($siswa) {
            try {
                $clearanceReport = $clearanceService->verifyStudentClearance($siswa);
            } catch (\Throwable $e) {
                $clearanceReport = [
                    'is_eligible' => false,
                    'summary' => $e->getMessage(),
                ];
            }
        }

        return view('siakad.wisuda.index', compact('siswa', 'clearanceReport'));
    }

    public function register(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();

        if ($siswa) {
            $siswa->update([
                'status_kelulusan' => 'Lulus Wisuda',
                'tgl_kelulusan' => now(),
                'nomor_ijazah' => 'PIN/' . date('Y') . '/' . sprintf('%06d', $siswa->id),
            ]);
        }

        ActivityLog::record('WISUDA_REGISTER', "Mahasiswa mendaftar wisuda & clearance PIN kelulusan.");

        return redirect()->route('siakad.wisuda.index')->with('success', 'Pendaftaran Wisuda berhasil! Nomor Penomoran Ijazah Nasional (PIN) telah diterbitkan.');
    }
}
