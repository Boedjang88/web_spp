<?php

namespace App\Http\Controllers\Api;

use App\Models\JadwalPelajaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = JadwalPelajaran::with(['kelas', 'mapel', 'guru']);

        if ($request->filled('id_kelas')) {
            $query->where('id_kelas', $request->id_kelas);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $jadwals = $query->orderBy('jam_mulai')->paginate((int) $request->get('per_page', 20));

        return $this->sendResponse($jadwals, 'Daftar jadwal pelajaran berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'id_mapel' => 'required|exists:mapels,id',
            'id_guru' => 'required|exists:gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruangan' => 'nullable|string|max:30',
        ]);

        $jadwal = JadwalPelajaran::create($validated);
        $jadwal->load(['kelas', 'mapel', 'guru']);

        return $this->sendResponse($jadwal, 'Jadwal pelajaran berhasil ditambahkan.', 201);
    }

    public function show(string|int $id): JsonResponse
    {
        $jadwal = JadwalPelajaran::with(['kelas', 'mapel', 'guru'])->find($id);

        if (!$jadwal) {
            return $this->sendError('Jadwal pelajaran tidak ditemukan.', [], 404);
        }

        return $this->sendResponse($jadwal, 'Detail jadwal pelajaran berhasil diambil.');
    }

    public function update(Request $request, string|int $id): JsonResponse
    {
        $jadwal = JadwalPelajaran::find($id);

        if (!$jadwal) {
            return $this->sendError('Jadwal pelajaran tidak ditemukan.', [], 404);
        }

        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'id_mapel' => 'required|exists:mapels,id',
            'id_guru' => 'required|exists:gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruangan' => 'nullable|string|max:30',
        ]);

        $jadwal->update($validated);
        $jadwal->load(['kelas', 'mapel', 'guru']);

        return $this->sendResponse($jadwal, 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(string|int $id): JsonResponse
    {
        $jadwal = JadwalPelajaran::find($id);

        if (!$jadwal) {
            return $this->sendError('Jadwal pelajaran tidak ditemukan.', [], 404);
        }

        $jadwal->delete();

        return $this->sendResponse(null, 'Jadwal pelajaran berhasil dihapus.');
    }
}
