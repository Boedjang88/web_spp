<?php

namespace App\Http\Controllers\Web;

use App\Domain\Academic\Actions\EnrollKknAction;
use App\Domain\Academic\Services\KknRegistrationService;
use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KknController extends Controller
{
    public function __construct(
        protected KknRegistrationService $kknService,
        protected EnrollKknAction $enrollKknAction
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa;

        $kknRegistration = $siswa
            ? $this->kknService->getLatestRegistrationForStudent($siswa->id)
            : null;

        $passedSks = $siswa ? $this->kknService->getPassedSks($siswa) : 0;
        $isEligible = $siswa ? $this->kknService->isEligible($siswa) : false;
        $minSks = KknRegistrationService::MIN_KKN_SKS;

        return view('siakad.kkn.index', compact('siswa', 'kknRegistration', 'passedSks', 'isEligible', 'minSks'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lokasi_kkn' => 'required|string|max:150',
            'kelompok' => 'required|string|max:50',
            'dpl_name' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa;
        if (!$siswa) {
            return back()->with('error', 'Profil mahasiswa Anda belum terhubung. Tidak dapat mendaftar KKN.');
        }

        try {
            $this->enrollKknAction->execute($siswa, $validated);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('siakad.kkn.index')->with('success', 'Pendaftaran Kuliah Kerja Nyata (KKN) berhasil disimpan!');
    }
}
