<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\TahunAkademik;
use App\Services\Academic\SmartKrsService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmartKrsController extends Controller
{
    public function __construct(
        protected SmartKrsService $krsService
    ) {}

    /**
     * Display Smart KRS Registration Portal
     */
    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        $activeYear = TahunAkademik::active()->first() ?? TahunAkademik::latest()->first();

        $krs = null;
        $availableClasses = collect();
        $isCleared = false;

        if ($siswa && $activeYear) {
            $isCleared = $this->krsService->isFinanciallyCleared($siswa->id, $activeYear->id);

            $krs = Krs::with(['details.kelasKuliah.mataKuliah', 'details.kelasKuliah.jadwalKuliahs.ruangan'])
                ->where('id_siswa', $siswa->id)
                ->where('id_tahun_akademik', $activeYear->id)
                ->first();

            $availableClasses = KelasKuliah::with(['mataKuliah', 'jadwalKuliahs.ruangan', 'jadwalKuliahs.dosen'])
                ->where('id_tahun_akademik', $activeYear->id)
                ->get();
        }

        return view('siakad.krs.index', compact('siswa', 'activeYear', 'krs', 'availableClasses', 'isCleared'));
    }

    /**
     * Enroll in a Course Class (with Pessimistic Locking)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id_kelas_kuliah' => 'required|exists:kelas_kuliahs,id',
            'id_tahun_akademik' => 'required|exists:tahun_akademiks,id',
        ]);

        $user = auth()->user();
        if (!$user->id_siswa) {
            return back()->with('error', 'Akun Anda tidak tertaut dengan data mahasiswa.');
        }

        try {
            $this->krsService->enrollClassWithPessimisticLock(
                $user->id_siswa,
                (int) $request->id_kelas_kuliah,
                (int) $request->id_tahun_akademik
            );

            return back()->with('success', 'Mata kuliah berhasil ditambahkan ke rencana studi Anda.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Drop a Course Class from KRS
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = auth()->user();
        if (!$user->id_siswa) {
            return back()->with('error', 'Akses ditolak.');
        }

        try {
            $this->krsService->dropClass($user->id_siswa, $id);
            return back()->with('success', 'Mata kuliah berhasil dibatalkan dari KRS.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
