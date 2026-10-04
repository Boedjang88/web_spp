<?php

namespace App\Http\Controllers\Api;

use App\Models\Mapel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapelController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Mapel::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%")
                  ->orWhere('kode_mapel', 'like', "%{$search}%");
            });
        }

        $mapels = $query->orderBy('nama_mapel')->paginate((int) $request->get('per_page', 15));

        return $this->sendResponse($mapels, 'Daftar mata pelajaran berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mapels,kode_mapel',
            'nama_mapel' => 'required|string|max:100',
            'kelompok' => 'required|string|max:50',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        $mapel = Mapel::create($validated);

        return $this->sendResponse($mapel, 'Mata pelajaran berhasil ditambahkan.', 201);
    }

    public function show(string|int $id): JsonResponse
    {
        $mapel = Mapel::with(['jadwals.guru', 'jadwals.kelas'])->find($id);

        if (!$mapel) {
            return $this->sendError('Mata pelajaran tidak ditemukan.', [], 404);
        }

        return $this->sendResponse($mapel, 'Detail mata pelajaran berhasil diambil.');
    }

    public function update(Request $request, string|int $id): JsonResponse
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return $this->sendError('Mata pelajaran tidak ditemukan.', [], 404);
        }

        $validated = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:20', 'unique:mapels,kode_mapel,' . $mapel->id],
            'nama_mapel' => 'required|string|max:100',
            'kelompok' => 'required|string|max:50',
            'kkm' => 'required|integer|min:0|max:100',
        ]);

        $mapel->update($validated);

        return $this->sendResponse($mapel, 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(string|int $id): JsonResponse
    {
        $mapel = Mapel::find($id);

        if (!$mapel) {
            return $this->sendError('Mata pelajaran tidak ditemukan.', [], 404);
        }

        $mapel->delete();

        return $this->sendResponse(null, 'Mata pelajaran berhasil dihapus.');
    }
}
