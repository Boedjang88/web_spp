<?php

namespace App\Http\Controllers\Api;

use App\Models\Presensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresensiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Presensi::with(['siswa', 'kelas']);

        if ($request->filled('id_kelas')) {
            $query->where('id_kelas', $request->id_kelas);
        }

        if ($request->filled('id_siswa')) {
            $query->where('id_siswa', $request->id_siswa);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $presensis = $query->orderBy('tanggal', 'desc')->paginate((int) $request->get('per_page', 30));

        return $this->sendResponse($presensis, 'Data presensi siswa berhasil diambil.');
    }

    public function storeBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'presensi' => 'required|array|min:1',
            'presensi.*.id_siswa' => 'required|exists:siswas,id',
            'presensi.*.status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        $saved = [];
        foreach ($validated['presensi'] as $item) {
            $saved[] = Presensi::updateOrCreate(
                [
                    'id_siswa' => $item['id_siswa'],
                    'tanggal' => $validated['tanggal'],
                ],
                [
                    'id_kelas' => $validated['id_kelas'],
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]
            );
        }

        return $this->sendResponse([
            'total_siswa' => count($saved),
            'tanggal' => $validated['tanggal'],
            'id_kelas' => $validated['id_kelas'],
        ], 'Presensi kehadiran siswa berhasil dicatat.', 201);
    }
}
