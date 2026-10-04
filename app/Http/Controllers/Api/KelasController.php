<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Kelas\StoreKelasRequest;
use App\Http\Requests\Api\Kelas\UpdateKelasRequest;
use App\Http\Resources\Api\KelasResource;
use App\Models\Kelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KelasController extends BaseApiController
{
    /**
     * Display a listing of Kelas
     */
    public function index(Request $request): JsonResponse
    {
        $query = Kelas::withCount('siswas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('kompetensi_keahlian', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 15);
        $kelasList = $request->has('all') && $request->boolean('all')
            ? $query->orderBy('nama_kelas')->get()
            : $query->orderBy('nama_kelas')->paginate($perPage);

        return $this->sendResponse(
            KelasResource::collection($kelasList)->response()->getData(true),
            'Daftar kelas berhasil diambil.'
        );
    }

    /**
     * Store a newly created Kelas
     */
    public function store(StoreKelasRequest $request): JsonResponse
    {
        $kelas = Kelas::create($request->validated());

        return $this->sendResponse(
            new KelasResource($kelas),
            'Data kelas berhasil ditambahkan.',
            201
        );
    }

    /**
     * Display the specified Kelas
     */
    public function show(string|int $id): JsonResponse
    {
        $kelas = Kelas::withCount('siswas')->find($id);

        if (!$kelas) {
            return $this->sendError('Data kelas tidak ditemukan.', [], 404);
        }

        return $this->sendResponse(
            new KelasResource($kelas),
            'Detail kelas berhasil diambil.'
        );
    }

    /**
     * Update the specified Kelas
     */
    public function update(UpdateKelasRequest $request, string|int $id): JsonResponse
    {
        $kelas = Kelas::find($id);

        if (!$kelas) {
            return $this->sendError('Data kelas tidak ditemukan.', [], 404);
        }

        $kelas->update($request->validated());

        return $this->sendResponse(
            new KelasResource($kelas),
            'Data kelas berhasil diperbarui.'
        );
    }

    /**
     * Remove the specified Kelas
     */
    public function destroy(string|int $id): JsonResponse
    {
        $kelas = Kelas::withCount('siswas')->find($id);

        if (!$kelas) {
            return $this->sendError('Data kelas tidak ditemukan.', [], 404);
        }

        $kelas->delete();

        return $this->sendResponse(null, 'Data kelas berhasil dihapus.');
    }
}
