<?php

namespace App\Http\Controllers\Api;

use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\TahunAkademik;
use App\Services\Academic\SmartKrsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmartKrsController extends BaseApiController
{
    public function __construct(
        protected SmartKrsService $krsService
    ) {}

    /**
     * Get Current Student KRS Plan & Available Classes
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->id_siswa) {
            return $this->sendError('Akun Anda tidak tertaut dengan data mahasiswa.', [], 403);
        }

        $activeYear = TahunAkademik::active()->first() ?? TahunAkademik::latest()->first();
        if (!$activeYear) {
            return $this->sendError('Tahun akademik aktif belum ditentukan.', [], 404);
        }

        $isCleared = $this->krsService->isFinanciallyCleared($user->id_siswa, $activeYear->id);

        $krs = Krs::with(['details.kelasKuliah.mataKuliah', 'details.kelasKuliah.jadwalKuliahs.ruangan'])
            ->where('id_siswa', $user->id_siswa)
            ->where('id_tahun_akademik', $activeYear->id)
            ->first();

        $availableClasses = KelasKuliah::with(['mataKuliah', 'jadwalKuliahs.ruangan', 'jadwalKuliahs.dosen'])
            ->where('id_tahun_akademik', $activeYear->id)
            ->get();

        return $this->sendResponse([
            'tahun_akademik' => $activeYear,
            'is_financially_cleared' => $isCleared,
            'krs' => $krs,
            'available_classes' => $availableClasses,
        ], 'Data rencana studi berhasil diambil.');
    }

    /**
     * Enroll Class with Pessimistic Locking via API
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_kelas_kuliah' => 'required|exists:kelas_kuliahs,id',
            'id_tahun_akademik' => 'required|exists:tahun_akademiks,id',
        ]);

        $user = $request->user();
        if (!$user->id_siswa) {
            return $this->sendError('Akses ditolak.', [], 403);
        }

        try {
            $detail = $this->krsService->enrollClassWithPessimisticLock(
                $user->id_siswa,
                (int) $validated['id_kelas_kuliah'],
                (int) $validated['id_tahun_akademik']
            );

            return $this->sendResponse($detail, 'Mata kuliah berhasil didaftarkan.', 201);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Drop Class from KRS via API
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user->id_siswa) {
            return $this->sendError('Akses ditolak.', [], 403);
        }

        try {
            $this->krsService->dropClass($user->id_siswa, $id);
            return $this->sendResponse(null, 'Mata kuliah berhasil dibatalkan.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }
}
