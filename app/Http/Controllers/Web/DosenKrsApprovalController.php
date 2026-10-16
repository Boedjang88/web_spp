<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Dosen;
use App\Models\Guru;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DosenKrsApprovalController extends Controller
{
    protected function resolveAdvisorUser()
    {
        $user = auth()->user();
        $guru = $user?->guru ?? ($user?->id_guru ? Guru::find($user->id_guru) : null);
        $dosen = $user?->dosen ?? ($user?->id_dosen ? Dosen::find($user->id_dosen) : null);

        return [$user, $guru, $dosen];
    }

    public function index(Request $request): View
    {
        [$user, $guru, $dosen] = $this->resolveAdvisorUser();

        if (!$user->isAdmin() && !$user->isSuperAdmin() && !$guru && !$dosen) {
            abort(403, 'Akun Anda tidak memiliki hak akses sebagai Dosen Pembimbing Akademik.');
        }

        $activeTa = TahunAkademik::where('is_active', true)->first();

        $krsList = Krs::with(['siswa.kelas', 'details.kelasKuliah.mataKuliah'])
            ->where('id_tahun_akademik', $activeTa?->id ?? 1)
            ->when(!$user->isAdmin() && !$user->isSuperAdmin(), function ($query) use ($guru, $dosen) {
                $query->where(function ($q) use ($guru, $dosen) {
                    if ($guru) {
                        $q->where('id_dosen_wali', $guru->id);
                    }
                    if ($dosen) {
                        $q->orWhereHas('siswa', function ($s) use ($dosen) {
                            $s->whereHas('mahasiswa', function ($m) use ($dosen) {
                                $m->where('id_dosen_pa', $dosen->id);
                            });
                        });
                    }
                });
            })
            ->latest('updated_at')
            ->paginate(15);

        return view('siakad.dosen.krs-approval', compact('guru', 'dosen', 'activeTa', 'krsList'));
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        [$user, $guru, $dosen] = $this->resolveAdvisorUser();

        $krs = Krs::with('siswa')->findOrFail($id);

        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            $isAuthorized = ($guru && $krs->id_dosen_wali == $guru->id)
                || ($dosen && optional(Mahasiswa::where('id', $krs->id_siswa)->orWhere('nim', optional($krs->siswa)->nisn)->first())->id_dosen_pa == $dosen->id);

            abort_if(!$isAuthorized, 403, 'Anda tidak memiliki wewenang untuk menyetujui KRS mahasiswa ini.');
        }

        if ($krs->status_krs === 'Disetujui') {
            return redirect()->back()->with('info', 'KRS mahasiswa ini sudah disetujui sebelumnya.');
        }

        DB::transaction(function () use ($krs, $request) {
            $krs->update([
                'status_krs' => 'Disetujui',
                'tgl_persetujuan' => now(),
                'catatan_pembimbing' => $request->input('catatan_pembimbing', 'KRS disetujui Dosen Pembimbing Akademik.'),
            ]);

            ActivityLog::record('KRS_APPROVE', "Dosen PA menyetujui KRS Mahasiswa ID #{$krs->id_siswa}");
        });

        return redirect()->back()->with('success', 'KRS Mahasiswa berhasil disetujui!');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate(['catatan_pembimbing' => 'required|string|max:255']);

        [$user, $guru, $dosen] = $this->resolveAdvisorUser();

        $krs = Krs::with('siswa')->findOrFail($id);

        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            $isAuthorized = ($guru && $krs->id_dosen_wali == $guru->id)
                || ($dosen && optional(Mahasiswa::where('id', $krs->id_siswa)->orWhere('nim', optional($krs->siswa)->nisn)->first())->id_dosen_pa == $dosen->id);

            abort_if(!$isAuthorized, 403, 'Anda tidak memiliki wewenang untuk menolak KRS mahasiswa ini.');
        }

        DB::transaction(function () use ($krs, $request) {
            $krs->update([
                'status_krs' => 'Ditolak',
                'catatan_pembimbing' => $request->input('catatan_pembimbing'),
            ]);

            ActivityLog::record('KRS_REJECT', "Dosen PA menolak KRS Mahasiswa ID #{$krs->id_siswa}");
        });

        return redirect()->back()->with('success', 'KRS Mahasiswa telah ditolak dengan catatan.');
    }
}
