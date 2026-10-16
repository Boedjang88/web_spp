<?php

namespace App\Http\Controllers\Web;

use App\Domain\Academic\Actions\VerifyExamCardEligibilityAction;
use App\Domain\Academic\Services\AcademicStudentService;
use App\Domain\Finance\Actions\GenerateStudentBillingStatementAction;
use App\Domain\Finance\Services\SppBillingService;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function __construct(
        protected AcademicStudentService $academicService,
        protected SppBillingService $billingService,
        protected VerifyExamCardEligibilityAction $verifyExamCardAction,
        protected GenerateStudentBillingStatementAction $billingStatementAction
    ) {}

    protected function getIndexRoute(): string
    {
        if (request()->routeIs('web.siswa.*') || request()->is('web/siswa*')) {
            return 'web.siswa.index';
        }
        return Route::has('web.mahasiswa.index') ? 'web.mahasiswa.index' : 'web.siswa.index';
    }

    public function index(Request $request): View
    {
        $siswas = $this->academicService->getPaginatedStudents(
            $request->search,
            $request->id_kelas,
            10
        );
        $kelasList = $this->academicService->getKelasList();

        $viewName = view()->exists('mahasiswa.index') ? 'mahasiswa.index' : 'siswa.index';
        return view($viewName, compact('siswas', 'kelasList'));
    }

    public function create(): View
    {
        $kelasList = $this->academicService->getKelasList();
        $sppList = $this->academicService->getSppList();

        $viewName = view()->exists('mahasiswa.create') ? 'mahasiswa.create' : 'siswa.create';
        return view($viewName, compact('kelasList', 'sppList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nisn' => 'required|string|size:10|unique:siswas,nisn',
            'nis' => 'required|string|max:8',
            'nama' => 'required|string|max:50',
            'id_kelas' => 'required|exists:kelas,id',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'id_spp' => 'required|exists:spps,id',
        ], [
            'nisn.required' => 'NIM / NISN wajib diisi.',
            'nisn.size' => 'NIM / NISN harus 10 digit angka.',
            'nisn.unique' => 'NIM / NISN sudah terdaftar.',
            'nama.required' => 'Nama mahasiswa wajib diisi.',
            'id_kelas.required' => 'Kelas perkuliahan wajib dipilih.',
            'id_spp.required' => 'Tarif UKT perkuliahan wajib dipilih.',
        ]);

        $siswa = Siswa::create($validated);

        // Auto-sync into Mahasiswa SIAKAD
        try {
            Mahasiswa::updateOrCreate(
                ['nisn' => $siswa->nisn],
                [
                    'nim' => $siswa->nim ?? $siswa->nisn,
                    'nama' => $siswa->nama,
                    'alamat' => $siswa->alamat,
                    'no_telp' => $siswa->no_telp,
                    'id_prodi' => ProgramStudi::first()?->id ?? 1,
                    'id_ukt' => $siswa->id_spp,
                ]
            );
        } catch (\Throwable $e) {}

        ActivityLog::record('MAHASISWA_CREATE', "Menambahkan data mahasiswa baru {$siswa->nama} (NIM: {$siswa->nisn}).");

        return redirect()->route($this->getIndexRoute())->with('success', 'Data mahasiswa berhasil ditambahkan.');
    }

    public function show(string|int $id): View
    {
        $siswa = Siswa::with(['kelas', 'spp', 'pembayarans.petugas'])->findOrFail($id);
        $tunggakan = $this->billingService->calculateStudentArrears($siswa);

        $viewName = view()->exists('mahasiswa.show') ? 'mahasiswa.show' : 'siswa.show';
        return view($viewName, compact('siswa', 'tunggakan'));
    }

    public function suratTagihan(string|int $id): View
    {
        $siswa = Siswa::findOrFail($id);

        if (auth()->check() && (auth()->user()->role === 'siswa' || auth()->user()->role === 'mahasiswa')) {
            $studentId = auth()->user()->id_siswa ?? auth()->user()->id_mahasiswa;
            abort_if(!$studentId || (int)$studentId !== (int)$siswa->id, 403, 'Akses ditolak: Anda hanya dapat mencetak surat tagihan milik Anda sendiri.');
        }

        $statement = $this->billingStatementAction->execute($siswa);

        $viewName = view()->exists('mahasiswa.surat-tagihan') ? 'mahasiswa.surat-tagihan' : 'siswa.surat-tagihan';
        return view($viewName, ['siswa' => $statement['siswa']]);
    }

    public function edit(string|int $id): View
    {
        $siswa = Siswa::findOrFail($id);
        $kelasList = $this->academicService->getKelasList();
        $sppList = $this->academicService->getSppList();

        $viewName = view()->exists('mahasiswa.edit') ? 'mahasiswa.edit' : 'siswa.edit';
        return view($viewName, compact('siswa', 'kelasList', 'sppList'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nisn' => ['required', 'string', 'size:10', Rule::unique('siswas', 'nisn')->ignore($siswa->id)],
            'nis' => 'required|string|max:8',
            'nama' => 'required|string|max:50',
            'id_kelas' => 'required|exists:kelas,id',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'id_spp' => 'required|exists:spps,id',
        ], [
            'nisn.required' => 'NIM / NISN wajib diisi.',
            'nisn.size' => 'NIM / NISN harus 10 digit.',
            'nisn.unique' => 'NIM / NISN sudah digunakan mahasiswa lain.',
            'nama.required' => 'Nama mahasiswa wajib diisi.',
        ]);

        $siswa->update($validated);

        // Auto-sync update into Mahasiswa SIAKAD
        try {
            Mahasiswa::updateOrCreate(
                ['nisn' => $siswa->nisn],
                [
                    'nim' => $siswa->nim ?? $siswa->nisn,
                    'nama' => $siswa->nama,
                    'alamat' => $siswa->alamat,
                    'no_telp' => $siswa->no_telp,
                    'id_ukt' => $siswa->id_spp,
                ]
            );
        } catch (\Throwable $e) {}

        ActivityLog::record('MAHASISWA_UPDATE', "Memperbarui biodata mahasiswa {$siswa->nama} (NIM: {$siswa->nisn}).");

        return redirect()->route($this->getIndexRoute())->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function kartuUjian(string|int $id): View|RedirectResponse
    {
        $siswa = Siswa::with(['kelas', 'spp'])->findOrFail($id);

        // Security check for Mahasiswa role: cannot view other students' exam pass
        if (auth()->check() && (auth()->user()->role === 'siswa' || auth()->user()->role === 'mahasiswa')) {
            $studentId = auth()->user()->id_siswa ?? auth()->user()->id_mahasiswa;
            abort_if(!$studentId || (int)$studentId !== (int)$siswa->id, 403, 'Akses ditolak. Anda hanya dapat mencetak kartu ujian milik Anda sendiri.');
        }

        $eligibility = $this->verifyExamCardAction->execute($siswa);
        $tunggakan = $eligibility['tunggakan'];
        $isLunas = $eligibility['is_lunas'];
        $isAttendanceEligible = $eligibility['is_attendance_eligible'];
        $attendanceRate = $eligibility['attendance_rate'];
        $minAttendance = $eligibility['min_attendance'];
        $canPrint = $eligibility['can_print'];

        $viewName = view()->exists('mahasiswa.kartu-ujian') ? 'mahasiswa.kartu-ujian' : 'siswa.kartu-ujian';
        return view($viewName, compact('siswa', 'tunggakan', 'isLunas', 'isAttendanceEligible', 'attendanceRate', 'minAttendance', 'canPrint'));
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->pembayarans()->count() > 0) {
            return back()->with('error', "Mahasiswa {$siswa->nama} tidak dapat dihapus karena sudah memiliki riwayat transaksi pembayaran UKT.");
        }

        $nama = $siswa->nama;
        $siswa->delete();

        ActivityLog::record('MAHASISWA_DELETE', "Menghapus data mahasiswa {$nama}.");

        return redirect()->route($this->getIndexRoute())->with('success', "Data mahasiswa {$nama} berhasil dihapus.");
    }
}
