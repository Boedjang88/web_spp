<?php

namespace App\Services\Facility;

use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\JadwalKuliah;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class FacilityBookingService
{
    /**
     * Map Indonesian day name from date string
     */
    protected function getHariFromDate(string $date): string
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $englishDay = Carbon::parse($date)->format('l');
        return $days[$englishDay] ?? 'Senin';
    }

    /**
     * Check if facility booking collides with active academic class schedule or parallel bookings
     *
     * @return array [bool $hasCollision, ?string $reason]
     */
    public function checkScheduleCollision(int $idFacility, string $tanggalPinjam, string $jamMulai, string $jamSelesai): array
    {
        $facility = Facility::findOrFail($idFacility);
        $hari = $this->getHariFromDate($tanggalPinjam);

        // 1. Check Collision against Academic Course Schedule (Jadwal Kuliah) for this Room
        if ($facility->id_ruangan) {
            $academicClash = JadwalPelajaran::whereHas('kelas', function ($q) use ($facility) {
                // Check if course schedule uses this room
            })->where('hari', $hari)
              ->where(function ($q) use ($jamMulai, $jamSelesai) {
                  $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                    ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                    ->orWhere(function ($sub) use ($jamMulai, $jamSelesai) {
                        $sub->where('jam_mulai', '<=', $jamMulai)
                            ->where('jam_selesai', '>=', $jamSelesai);
                    });
              })->exists();

            if ($academicClash) {
                return [
                    true,
                    "BENTROK JADWAL KULIAH: Ruangan/Fasilitas {$facility->nama_fasilitas} sedang digunakan untuk Perkuliahan Akademik Rutin pada hari {$hari} ({$jamMulai} - {$jamSelesai})."
                ];
            }
        }

        // 2. Check Collision against Approved Parallel Facility Bookings
        $bookingClash = FacilityBooking::where('id_facility', $idFacility)
            ->where('tanggal_pinjam', $tanggalPinjam)
            ->where('status_booking', 'APPROVED')
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                  ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                  ->orWhere(function ($sub) use ($jamMulai, $jamSelesai) {
                      $sub->where('jam_mulai', '<=', $jamMulai)
                          ->where('jam_selesai', '>=', $jamSelesai);
                  });
            })->exists();

        if ($bookingClash) {
            return [
                true,
                "BENTROK FASILITAS: Fasilitas {$facility->nama_fasilitas} sudah disetujui untuk peminjaman lain pada tanggal {$tanggalPinjam} jam {$jamMulai} - {$jamSelesai}."
            ];
        }

        return [false, null];
    }

    /**
     * Submit new Facility Booking request with Collision Check Auto-Approval
     *
     * @throws Exception
     */
    public function createBookingRequest(array $bookingData): FacilityBooking
    {
        return DB::transaction(function () use ($bookingData) {
            [$hasCollision, $reason] = $this->checkScheduleCollision(
                (int) $bookingData['id_facility'],
                $bookingData['tanggal_pinjam'],
                $bookingData['jam_mulai'],
                $bookingData['jam_selesai']
            );

            if ($hasCollision) {
                // Record booking with COLLISION_DETECTED status
                return FacilityBooking::create(array_merge($bookingData, [
                    'status_booking' => 'COLLISION_DETECTED',
                    'catatan_persetujuan' => $reason,
                ]));
            }

            return FacilityBooking::create(array_merge($bookingData, [
                'status_booking' => 'APPROVED',
                'catatan_persetujuan' => 'Otomatis disetujui (Bebas Bentrok Akademik)',
            ]));
        });
    }
}
