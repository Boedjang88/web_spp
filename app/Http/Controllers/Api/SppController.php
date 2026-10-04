<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Spp\StoreSppRequest;
use App\Http\Requests\Api\Spp\UpdateSppRequest;
use App\Http\Resources\Api\SppResource;
use App\Models\Spp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SppController extends BaseApiController
{
    /**
     * Display a listing of SPP
     */
    public function index(Request $request): JsonResponse
    {
        $query = Spp::withCount('siswas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tahun', 'like', "%{$search}%")
                  ->orWhere('nominal', 'like', "%{$search}%");
        }

        $perPage = (int) $request->get('per_page', 15);
        $sppList = $request->has('all') && $request->boolean('all')
            ? $query->orderBy('tahun', 'desc')->get()
            : $query->orderBy('tahun', 'desc')->paginate($perPage);

        return $this->sendResponse(
            SppResource::collection($sppList)->response()->getData(true),
            'Daftar tarif SPP berhasil diambil.'
        );
    }

    /**
     * Store a newly created SPP
     */
    public function store(StoreSppRequest $request): JsonResponse
    {
        $spp = Spp::create($request->validated());

        return $this->sendResponse(
            new SppResource($spp),
            'Tarif SPP berhasil ditambahkan.',
            201
        );
    }

    /**
     * Display the specified SPP
     */
    public function show(string|int $id): JsonResponse
    {
        $spp = Spp::withCount('siswas')->find($id);

        if (!$spp) {
            return $this->sendError('Data tarif SPP tidak ditemukan.', [], 404);
        }

        return $this->sendResponse(
            new SppResource($spp),
            'Detail tarif SPP berhasil diambil.'
        );
    }

    /**
     * Update the specified SPP
     */
    public function update(UpdateSppRequest $request, string|int $id): JsonResponse
    {
        $spp = Spp::find($id);

        if (!$spp) {
            return $this->sendError('Data tarif SPP tidak ditemukan.', [], 404);
        }

        $spp->update($request->validated());

        return $this->sendResponse(
            new SppResource($spp),
            'Tarif SPP berhasil diperbarui.'
        );
    }

    /**
     * Remove the specified SPP
     */
    public function destroy(string|int $id): JsonResponse
    {
        $spp = Spp::withCount('siswas')->find($id);

        if (!$spp) {
            return $this->sendError('Data tarif SPP tidak ditemukan.', [], 404);
        }

        if ($spp->siswas_count > 0) {
            return $this->sendError('Tarif SPP tidak dapat dihapus karena masih digunakan oleh siswa.', [], 422);
        }

        $spp->delete();

        return $this->sendResponse(null, 'Tarif SPP berhasil dihapus.');
    }
}
