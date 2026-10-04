<?php

namespace App\Services\Academic;

use App\Models\JadwalKuliah;
use App\Models\KelasKuliah;
use App\Models\Ruangan;
use Carbon\Carbon;
use Exception;

class SchedulingService
{
    /**
     * Validate and detect timetable clashes (Lecturer overlap, Room clash, Room capacity)
     *
     * @throws Exception
     */
    public function validateScheduleConflict(
        int $idKelasKuliah,
        int $idRuangan,
        int $idGuru,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        ?int $ignoreJadwalId = null
    ): array {
        $jamMulaiParsed = Carbon::createFromTimeString($jamMulai);
        $jamSelesaiParsed = Carbon::createFromTimeString($jamSelesai);

        if ($jamSelesaiParsed->lte($jamMulaiParsed)) {
            throw new Exception("Jam selesai ({$jamSelesai}) harus lebih besar daripada jam mulai ({$jamMulai}).");
        }

        $kelas = KelasKuliah::findOrFail($idKelasKuliah);
        $ruangan = Ruangan::findOrFail($idRuangan);

        // 1. Check Room Capacity
        if ($kelas->kuota_maksimal > $ruangan->kapasitas) {
            return [
                'has_conflict' => true,
                'conflict_type' => 'ROOM_CAPACITY_EXCEEDED',
                'message' => "Kapasitas ruangan {$ruangan->nama_ruangan} ({$ruangan->kapasitas} kursi) tidak mencukupi untuk kuota kelas ({$kelas->kuota_maksimal} mahasiswa).",
            ];
        }

        // 2. Check Room Double-Booking Clash in the same Academic Year
        $roomClashQuery = JadwalKuliah::where('id_ruangan', $idRuangan)
            ->where('hari', $hari)
            ->whereHas('kelasKuliah', function ($q) use ($kelas) {
                $q->where('id_tahun_akademik', $kelas->id_tahun_akademik);
            })
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where(function ($sub) use ($jamMulai, $jamSelesai) {
                    $sub->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                });
            });

        if ($ignoreJadwalId) {
            $roomClashQuery->where('id', '!=', $ignoreJadwalId);
        }

        $roomClash = $roomClashQuery->with(['kelasKuliah.mataKuliah'])->first();
        if ($roomClash) {
            return [
                'has_conflict' => true,
                'conflict_type' => 'ROOM_DOUBLE_BOOKING',
                'message' => "Bentrok Ruangan! Ruangan {$ruangan->nama_ruangan} sudah digunakan oleh mata kuliah {$roomClash->kelasKuliah?->mataKuliah?->nama_mk} ({$roomClash->jam_mulai} - {$roomClash->jam_selesai}).",
            ];
        }

        // 3. Check Lecturer Overlap Clash
        $lecturerClashQuery = JadwalKuliah::where('id_guru', $idGuru)
            ->where('hari', $hari)
            ->whereHas('kelasKuliah', function ($q) use ($kelas) {
                $q->where('id_tahun_akademik', $kelas->id_tahun_akademik);
            })
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                    ->where('jam_selesai', '>', $jamMulai);
            });

        if ($ignoreJadwalId) {
            $lecturerClashQuery->where('id', '!=', $ignoreJadwalId);
        }

        $lecturerClash = $lecturerClashQuery->with(['kelasKuliah.mataKuliah', 'ruangan'])->first();
        if ($lecturerClash) {
            return [
                'has_conflict' => true,
                'conflict_type' => 'LECTURER_OVERLAP',
                'message' => "Bentrok Dosen! Dosen sudah memiliki jadwal mengajar di kelas {$lecturerClash->kelasKuliah?->mataKuliah?->nama_mk} di {$lecturerClash->ruangan?->nama_ruangan} ({$lecturerClash->jam_mulai} - {$lecturerClash->jam_selesai}).",
            ];
        }

        return [
            'has_conflict' => false,
            'conflict_type' => null,
            'message' => 'Jadwal valid dan bebas bentrok.',
        ];
    }
}
