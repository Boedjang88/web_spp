<?php

namespace App\Http\Controllers\Api;

use App\Events\AttendanceQrRotated;
use App\Models\BapPerkuliahan;
use App\Models\KelasKuliah;
use App\Models\PresensiKuliah;
use App\Models\PresensiMahasiswa;
use App\Models\Ruangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class GeoAttendanceController extends BaseApiController
{
    /**
     * Generate Expiring QR Token for Lecturer Session (Valid for 10 seconds).
     */
    public function generateSessionToken(int $idBap): JsonResponse
    {
        $bap = BapPerkuliahan::with('ruangan')->findOrFail($idBap);

        $token = 'ATT-QR-' . strtoupper(bin2hex(random_bytes(8)));
        $cacheKey = "qr_attendance_bap_{$idBap}";

        // Store in cache for 10 seconds rotating token
        Cache::put($cacheKey, $token, now()->addSeconds(10));

        // Broadcast websocket event for live listener updates
        event(new AttendanceQrRotated($bap->id, $bap->id_kelas_kuliah, $token, 10));

        return $this->sendResponse([
            'id_bap' => $bap->id,
            'id_kelas_kuliah' => $bap->id_kelas_kuliah,
            'pertemuan_ke' => $bap->pertemuan_ke,
            'qr_token' => $token,
            'expires_in_seconds' => 10,
            'ruangan' => $bap->ruangan?->nama_ruangan,
            'target_lat' => $bap->ruangan?->latitude,
            'target_long' => $bap->ruangan?->longitude,
            'radius_meter' => min(20, $bap->ruangan?->radius_meter ?? 20),
        ], 'Token QR Presensi berhasil dibuat.');
    }

    /**
     * Generate Expiring QR Token for Kelas Kuliah directly (Valid for 10 seconds).
     */
    public function generateClassToken(int $idKelasKuliah): JsonResponse
    {
        $kelas = KelasKuliah::findOrFail($idKelasKuliah);
        $token = 'ATT-QR-' . strtoupper(bin2hex(random_bytes(8)));
        $cacheKey = "qr_attendance_kelas_{$idKelasKuliah}";

        Cache::put($cacheKey, $token, now()->addSeconds(10));

        return $this->sendResponse([
            'id_kelas_kuliah' => $kelas->id,
            'nama_kelas' => $kelas->nama_kelas,
            'qr_token' => $token,
            'expires_in_seconds' => 10,
            'radius_meter' => 20,
        ], 'Token QR Presensi Kelas berhasil dibuat.');
    }

    /**
     * Store student attendance with strict 20m Haversine validation and 10s token check.
     */
    public function store(Request $request): JsonResponse
    {
        $lat = $request->input('lat', $request->input('latitude'));
        $lng = $request->input('lng', $request->input('longitude'));

        $request->merge([
            'latitude' => $lat,
            'longitude' => $lng,
        ]);

        $validated = $request->validate([
            'id_kelas_kuliah' => 'nullable|exists:kelas_kuliahs,id',
            'id_bap' => 'nullable|exists:bap_perkuliahans,id',
            'qr_token' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'device_fingerprint' => 'nullable|string',
        ]);

        if (empty($validated['id_kelas_kuliah']) && empty($validated['id_bap'])) {
            throw ValidationException::withMessages([
                'id_kelas_kuliah' => 'ID Kelas Kuliah atau ID BAP wajib disertakan.',
            ]);
        }

        $user = $request->user();
        $studentId = $user->id_mahasiswa ?? $user->id_siswa ?? $user->mahasiswa?->id;
        if (!$studentId) {
            return $this->sendError('Akses ditolak. Anda bukan mahasiswa.', [], 403);
        }

        $idKelas = !empty($validated['id_kelas_kuliah']) ? (int) $validated['id_kelas_kuliah'] : null;
        $idBap = !empty($validated['id_bap']) ? (int) $validated['id_bap'] : null;

        // 1. Token Guard (10-second rotating Redis/Cache check)
        $validToken = false;
        if ($idBap) {
            $cachedBap = Cache::get("qr_attendance_bap_{$idBap}");
            if ($cachedBap && $cachedBap === $validated['qr_token']) {
                $validToken = true;
            }
        } elseif ($idKelas) {
            $cachedKelas = Cache::get("qr_attendance_kelas_{$idKelas}");
            if ($cachedKelas && $cachedKelas === $validated['qr_token']) {
                $validToken = true;
            }
        }

        if ($idBap && !$idKelas) {
            $bap = BapPerkuliahan::find($idBap);
            $idKelas = $bap?->id_kelas_kuliah;
        }

        if (!$validToken) {
            return $this->sendError('Token QR Presensi tidak valid atau telah kadaluarsa (Expired > 10 detik). Silakan scan ulang.', [], 422);
        }

        // 2. Resolve Target Coordinates (Room / Lecture Hall)
        $targetLat = -6.917464;
        $targetLong = 107.619123;
        $allowedRadius = 20; // Strict 20 meters tolerance

        if ($idBap) {
            $bap = BapPerkuliahan::with('ruangan')->find($idBap);
            if ($bap && $bap->ruangan) {
                $targetLat = (float) $bap->ruangan->latitude;
                $targetLong = (float) $bap->ruangan->longitude;
            }
        }

        // 3. Haversine Distance Calculation
        $submitLat = (float) $validated['latitude'];
        $submitLong = (float) $validated['longitude'];
        $distanceMeter = $this->calculateHaversineDistance($submitLat, $submitLong, $targetLat, $targetLong);

        if ($distanceMeter > $allowedRadius) {
            throw ValidationException::withMessages([
                'lokasi' => 'Anda berada di luar jangkauan ruang perkuliahan!',
            ]);
        }

        // 4. Save Presensi Mahasiswa Record
        $presensi = PresensiMahasiswa::updateOrCreate(
            [
                'id_mahasiswa' => $studentId,
                'id_kelas_kuliah' => $idKelas,
            ],
            [
                'id_bap' => $idBap,
                'waktu_hadir' => now(),
                'latitude' => $submitLat,
                'longitude' => $submitLong,
                'status' => 'Hadir',
                'device_fingerprint' => $validated['device_fingerprint'] ?? null,
                'verified_at' => now(),
            ]
        );

        // Also update PresensiKuliah for backward compatibility if bap exists
        if ($idBap) {
            PresensiKuliah::updateOrCreate(
                [
                    'id_bap' => $idBap,
                    'id_siswa' => $studentId,
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
        }

        return $this->sendResponse([
            'presensi' => $presensi,
            'status' => 'HADIR_VERIFIED',
            'jarak_meter' => $distanceMeter,
            'waktu_hadir' => now()->toDateTimeString(),
        ], 'Presensi kehadiran perkuliahan berhasil diverifikasi.');
    }

    /**
     * Legacy submitAttendance route wrapper
     */
    public function submitAttendance(Request $request): JsonResponse
    {
        return $this->store($request);
    }

    /**
     * Compute Haversine distance in meters
     */
    public function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): int
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
