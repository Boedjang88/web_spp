<?php

namespace App\Http\Controllers\Api;

use App\Models\Guru;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuruController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Guru::withCount('jadwals');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_guru', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $gurus = $query->orderBy('nama_guru')->paginate((int) $request->get('per_page', 15));

        return $this->sendResponse($gurus, 'Daftar tenaga pendidik / guru berhasil diambil.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:20|unique:gurus,nip',
            'nama_guru' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ]);

        $guru = Guru::create($validated);

        return $this->sendResponse($guru, 'Data guru berhasil ditambahkan.', 201);
    }

    public function show(string|int $id): JsonResponse
    {
        $guru = Guru::with(['jadwals.mapel', 'jadwals.kelas'])->find($id);

        if (!$guru) {
            return $this->sendError('Data guru tidak ditemukan.', [], 404);
        }

        return $this->sendResponse($guru, 'Detail guru berhasil diambil.');
    }

    public function update(Request $request, string|int $id): JsonResponse
    {
        $guru = Guru::find($id);

        if (!$guru) {
            return $this->sendError('Data guru tidak ditemukan.', [], 404);
        }

        $validated = $request->validate([
            'nip' => ['nullable', 'string', 'max:20', 'unique:gurus,nip,' . $guru->id],
            'nama_guru' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
        ]);

        $guru->update($validated);

        return $this->sendResponse($guru, 'Data guru berhasil diperbarui.');
    }

    public function destroy(string|int $id): JsonResponse
    {
        $guru = Guru::find($id);

        if (!$guru) {
            return $this->sendError('Data guru tidak ditemukan.', [], 404);
        }

        if ($guru->jadwals()->count() > 0 || $guru->nilais()->count() > 0) {
            return $this->sendError('Data guru tidak dapat dihapus karena masih terhubung dengan data jadwal mengajar atau nilai siswa.', [], 422);
        }

        $guru->delete();

        return $this->sendResponse(null, 'Data guru berhasil dihapus.');
    }
}
