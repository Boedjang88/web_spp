<?php

namespace App\Http\Controllers\Api;

use App\Models\BapPerkuliahan;
use App\Models\PresensiKuliah;
use App\Models\Ruangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GeoAttendanceController extends BaseApiController
{
    /**
     * Generate Expiring QR Token for Lecturer BAP Session (Valid for 60 seconds)
     */
    public function generateSessionToken(int $idBap): JsonResponse
    {
        $bap = BapPerkuliahan::with('ruangan')->findOrFail($idBap);

        $token = 'ATT-QR-' . strtoupper(bin2hex(random_bytes(8)));
        $cacheKey = "qr_attendance_bap_{$idBap}";

        // Store in cache for 60 seconds
        Cache::put($cacheKey, $token, now()->addSeconds(60));

        return $this->sendResponse([
            'id_bap' => $bap->id,
            'pertemuan_ke' => $bap->pertemuan_ke,
            'qr_token' => $token,
            'expires_in_seconds' => 60,
            'ruangan' => $bap->ruangan?->nama_ruangan,
            'target_lat' => $bap->ruangan?->latitude,
            'target_long' => $bap->ruangan?->longitude,
            'radius_meter' => $bap->ruangan?->radius_meter ?? 50,
        ], 'Token QR Presensi berhasil dibuat.');
    }

    /**
     * Submit Student QR Scan with Geo-fencing Haversine validation
     */
    public function submitAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_bap' => 'required|exists:bap_perkuliahans,id',
            'qr_token' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'device_fingerprint' => 'nullable|string',
        ]);

        $user = $request->user();
        if (!$user->id_siswa) {
            return $this->sendError('Akses ditolak. Anda bukan mahasiswa.', [], 403);
        }

        $idBap = (int) $validated['id_bap'];
        $cacheKey = "qr_attendance_bap_{$idBap}";
        $cachedToken = Cache::get($cacheKey);

        // 1. Verify Expiring QR Token
        if (!$cachedToken || $cachedToken !== $validated['qr_token']) {
            return $this->sendError('Token QR Presensi tidak valid atau telah kadaluarsa. Silakan scan ulang.', [], 422);
        }

        $bap = BapPerkuliahan::with('ruangan')->findOrFail($idBap);
        $ruangan = $bap->ruangan;

        $targetLat = (float) ($ruangan?->latitude ?? -6.917464);
        $targetLong = (float) ($ruangan?->longitude ?? 107.619123);
        $allowedRadius = $ruangan?->radius_meter ?? 50;

        // 2. Haversine Distance Calculation
        $submitLat = (float) $validated['latitude'];
        $submitLong = (float) $validated['longitude'];

        $distanceMeter = $this->calculateHaversineDistance($submitLat, $submitLong, $targetLat, $targetLong);

        if ($distanceMeter > $allowedRadius) {
            return $this->sendError("Presensi Ditolak! Anda berada di luar jangkauan ruangan ({$distanceMeter} meter dari {$ruangan?->nama_ruangan}, radius maksimal {$allowedRadius} meter).", [
                'jarak_meter' => $distanceMeter,
                'radius_maksimal' => $allowedRadius,
            ], 422);
        }

        // 3. Record Attendance
        $presensi = PresensiKuliah::updateOrCreate(
            [
                'id_bap' => $idBap,
                'id_siswa' => $user->id_siswa,
            ],
            [
                'status_kehadiran' => 'Hadir',
                'submit_lat' => $submitLat,
                'submit_long' => $submitLong,
                'jarak_meter_dari_ruangan' => $distanceMeter,
                'device_fingerprint' => $validated['device_fingerprint'] ?? null,
                'waktu_scan' => now(),
            ]
        );

        return $this->sendResponse([
            'presensi' => $presensi,
            'status' => 'HADIR_VERIFIED',
            'jarak_meter' => $distanceMeter,
            'waktu_scan' => now()->toDateTimeString(),
        ], 'Presensi kehadiran perkuliahan berhasil diverifikasi.');
    }

    /**
     * Compute Haversine distance in meters
     */
    protected function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000; // in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }
}
