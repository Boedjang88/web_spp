<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Services\Academic\SmartKrsService;
use App\Traits\ResolvesStudentUser;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmartKrsController extends Controller
{
    use ResolvesStudentUser;

    public function __construct(
        protected SmartKrsService $krsService
    ) {}

    /**
     * Display Smart KRS Registration Portal
     */
    public function index(): View
    {
        $user = auth()->user();
        $mahasiswa = $this->getStudentMahasiswa($user);
        $siswa = $user->siswa ?? $mahasiswa;

        $activeYear = TahunAkademik::active()->first() ?? TahunAkademik::latest()->first();

        $krs = null;
        $availableClasses = collect();
        $isCleared = false;

        if ($mahasiswa && $activeYear) {
            $isCleared = $this->krsService->isFinanciallyCleared($mahasiswa->id, $activeYear->id);

            $krs = Krs::with(['details.kelasKuliah.mataKuliah', 'details.kelasKuliah.jadwalKuliahs.ruangan'])
                ->where('id_siswa', $mahasiswa->id)
                ->where('id_tahun_akademik', $activeYear->id)
                ->first();

            $availableClasses = KelasKuliah::with(['mataKuliah', 'jadwalKuliahs.ruangan', 'jadwalKuliahs.dosen'])
                ->where('id_tahun_akademik', $activeYear->id)
                ->get();
        }

        return view('siakad.krs.index', compact('siswa', 'mahasiswa', 'activeYear', 'krs', 'availableClasses', 'isCleared'));
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
        $mahasiswa = $this->getStudentMahasiswa($user);

        try {
            $this->krsService->enrollClassWithPessimisticLock(
                $mahasiswa->id,
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
        $mahasiswa = $this->getStudentMahasiswa($user);

        try {
            $this->krsService->dropClass($mahasiswa->id, $id);
            return back()->with('success', 'Mata kuliah berhasil dibatalkan dari KRS.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
