<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BapPerkuliahan;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\PresensiMahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class StudentAttendanceController extends Controller
{
    /**
     * Display student attendance dashboard and active sessions.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $studentId = $user->id_mahasiswa ?? $user->id_siswa;
        $mahasiswa = Mahasiswa::with(['prodi.fakultas'])->findOrFail($studentId);

        $activeTa = TahunAkademik::where('is_active', true)->first()
            ?? TahunAkademik::latest()->first();

        // Retrieve student's approved enrolled classes via KRS
        $krs = Krs::with('details.kelasKuliah.mataKuliah', 'details.kelasKuliah.dosen')
            ->where(function ($q) use ($mahasiswa, $user) {
                $q->where('id_siswa', $mahasiswa->id);
                if ($user->id_siswa) {
                    $q->orWhere('id_siswa', $user->id_siswa);
                }
            })
            ->where('status_krs', 'Disetujui')
            ->latest()
            ->first();

        $enrolledKelasIds = $krs?->details->pluck('id_kelas_kuliah')->filter()->values()->toArray() ?? [];

        // Active / Today's BAP perkuliahan sessions
        $todayBaps = BapPerkuliahan::with(['kelasKuliah.mataKuliah', 'dosen', 'ruangan'])
            ->whereIn('id_kelas_kuliah', $enrolledKelasIds)
            ->whereDate('tanggal_pelaksanaan', now()->toDateString())
            ->get();

        // Complete Attendance History for this student
        $attendanceHistory = PresensiMahasiswa::with(['kelasKuliah.mataKuliah', 'bap.ruangan'])
            ->where('id_mahasiswa', $mahasiswa->id)
            ->orderBy('waktu_hadir', 'desc')
            ->paginate(15);

        // Compute Attendance Metrics
        $totalHadir = PresensiMahasiswa::where('id_mahasiswa', $mahasiswa->id)->where('status', 'Hadir')->count();
        $totalIzin = PresensiMahasiswa::where('id_mahasiswa', $mahasiswa->id)->where('status', 'Izin')->count();
        $totalSakit = PresensiMahasiswa::where('id_mahasiswa', $mahasiswa->id)->where('status', 'Sakit')->count();
        $totalAlpa = PresensiMahasiswa::where('id_mahasiswa', $mahasiswa->id)->where('status', 'Alpa')->count();
        $totalRecords = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;
        $kehadiranPersen = $totalRecords > 0 ? round(($totalHadir / $totalRecords) * 100, 1) : 100.0;

        return view('siakad.presensi.index', compact(
            'mahasiswa',
            'krs',
            'todayBaps',
            'attendanceHistory',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'kehadiranPersen'
        ));
    }

    /**
     * Submit Student Geo-fenced Presence Check-in via Web Portal
     */
    public function checkIn(Request $request)
    {
        $user = $request->user();
        $studentId = $user->id_mahasiswa ?? $user->id_siswa;
        $mahasiswa = Mahasiswa::findOrFail($studentId);

        $lat = $request->input('lat', $request->input('latitude'));
        $lng = $request->input('lng', $request->input('longitude'));

        $request->merge([
            'latitude' => $lat,
            'longitude' => $lng,
        ]);

        $validated = $request->validate([
            'id_bap' => 'nullable|exists:bap_perkuliahans,id',
            'id_kelas_kuliah' => 'nullable|exists:kelas_kuliahs,id',
            'qr_token' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if (empty($validated['id_kelas_kuliah']) && empty($validated['id_bap'])) {
            return back()->with('error', 'Pilih sesi perkuliahan atau masukkan ID kelas.');
        }

        $idKelas = !empty($validated['id_kelas_kuliah']) ? (int) $validated['id_kelas_kuliah'] : null;
        $idBap = !empty($validated['id_bap']) ? (int) $validated['id_bap'] : null;

        // Verify Token in Cache (10s rotating window)
        $validToken = false;
        if ($idBap) {
            $cachedToken = Cache::get("qr_attendance_bap_{$idBap}");
            if ($cachedToken && $cachedToken === $validated['qr_token']) {
                $validToken = true;
            }
        } elseif ($idKelas) {
            $cachedToken = Cache::get("qr_attendance_kelas_{$idKelas}");
            if ($cachedToken && $cachedToken === $validated['qr_token']) {
                $validToken = true;
            }
        }

        if (!$validToken) {
            return back()->with('error', 'Token QR Presensi tidak valid atau telah kadaluarsa (Expired > 10 detik). Silakan minta kode baru dari dosen.');
        }

        if ($idBap && !$idKelas) {
            $bap = BapPerkuliahan::find($idBap);
            $idKelas = $bap?->id_kelas_kuliah;
        }

        // Room Coordinates Check with 20m Haversine limit
        $targetLat = -6.917464;
        $targetLong = 107.619123;
        if ($idBap) {
            $bap = BapPerkuliahan::with('ruangan')->find($idBap);
            if ($bap && $bap->ruangan) {
                $targetLat = (float) $bap->ruangan->latitude;
                $targetLong = (float) $bap->ruangan->longitude;
            }
        }

        $submitLat = (float) $validated['latitude'];
        $submitLong = (float) $validated['longitude'];
        $distance = $this->calculateHaversineDistance($submitLat, $submitLong, $targetLat, $targetLong);

        if ($distance > 20) {
            return back()->with('error', "Presensi Ditolak! Anda berada {$distance} meter dari ruang perkuliahan (Batas maksimal 20 meter).");
        }

        PresensiMahasiswa::updateOrCreate(
            [
                'id_mahasiswa' => $mahasiswa->id,
                'id_kelas_kuliah' => $idKelas,
            ],
            [
                'id_bap' => $idBap,
                'waktu_hadir' => now(),
                'latitude' => $submitLat,
                'longitude' => $submitLong,
                'status' => 'Hadir',
                'device_fingerprint' => hash('sha256', $request->ip() . '|' . $request->userAgent()),
                'verified_at' => now(),
            ]
        );

        return back()->with('success', "Presensi Berhasil! Kehadiran Anda telah tercatat dan terverifikasi di lokasi ({$distance} meter).");
    }

    protected function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): int
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return (int) round($earthRadius * $c);
    }
}
