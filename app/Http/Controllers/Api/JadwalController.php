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

        // Validasi bentrok jadwal (Dosen/Guru, Kelas, dan Ruangan)
        $clash = JadwalPelajaran::where('hari', $validated['hari'])
            ->where(function ($q) use ($validated) {
                $q->where('jam_mulai', '<', $validated['jam_selesai'])
                  ->where('jam_selesai', '>', $validated['jam_mulai']);
            })
            ->where(function ($q) use ($validated) {
                $q->where('id_guru', $validated['id_guru'])
                  ->orWhere('id_kelas', $validated['id_kelas'])
                  ->when(!empty($validated['ruangan']), function ($sub) use ($validated) {
                      $sub->orWhere('ruangan', $validated['ruangan']);
                  });
            })
            ->first();

        if ($clash) {
            $reason = $clash->id_guru == $validated['id_guru']
                ? "Guru/Dosen yang bersangkutan sudah memiliki jadwal mengajar ({$clash->jam_mulai}-{$clash->jam_selesai})."
                : ($clash->id_kelas == $validated['id_kelas']
                    ? "Kelas yang bersangkutan sudah memiliki jadwal pelajaran lain ({$clash->jam_mulai}-{$clash->jam_selesai})."
                    : "Ruangan '{$validated['ruangan']}' sedang digunakan pada jam tersebut.");
            return $this->sendError("Bentrok Jadwal! {$reason}", [], 422);
        }

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

        // Validasi bentrok jadwal (Dosen/Guru, Kelas, dan Ruangan)
        $clash = JadwalPelajaran::where('id', '!=', $id)
            ->where('hari', $validated['hari'])
            ->where(function ($q) use ($validated) {
                $q->where('jam_mulai', '<', $validated['jam_selesai'])
                  ->where('jam_selesai', '>', $validated['jam_mulai']);
            })
            ->where(function ($q) use ($validated) {
                $q->where('id_guru', $validated['id_guru'])
                  ->orWhere('id_kelas', $validated['id_kelas'])
                  ->when(!empty($validated['ruangan']), function ($sub) use ($validated) {
                      $sub->orWhere('ruangan', $validated['ruangan']);
                  });
            })
            ->first();

        if ($clash) {
            $reason = $clash->id_guru == $validated['id_guru']
                ? "Guru/Dosen yang bersangkutan sudah memiliki jadwal mengajar ({$clash->jam_mulai}-{$clash->jam_selesai})."
                : ($clash->id_kelas == $validated['id_kelas']
                    ? "Kelas yang bersangkutan sudah memiliki jadwal pelajaran lain ({$clash->jam_mulai}-{$clash->jam_selesai})."
                    : "Ruangan '{$validated['ruangan']}' sedang digunakan pada jam tersebut.");
            return $this->sendError("Bentrok Jadwal! {$reason}", [], 422);
        }

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
