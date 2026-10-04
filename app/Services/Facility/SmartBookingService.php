<?php

namespace App\Services\Facility;

use App\Models\BookingFasilitas;
use App\Models\JadwalKuliah;
use App\Models\Ruangan;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class SmartBookingService
{
    /**
     * Reserve room/lab asset while checking against active academic timetable
     *
     * @throws Exception
     */
    public function createReservation(
        int $idRuangan,
        int $idUser,
        string $namaKegiatan,
        string $organisasi,
        string $tanggalBooking,
        string $jamMulai,
        string $jamSelesai
    ): BookingFasilitas {
        return DB::transaction(function () use (
            $idRuangan,
            $idUser,
            $namaKegiatan,
            $organisasi,
            $tanggalBooking,
            $jamMulai,
            $jamSelesai
        ) {
            $ruangan = Ruangan::findOrFail($idRuangan);
            $dayNameIndo = Carbon::parse($tanggalBooking)->locale('id')->isoFormat('dddd');

            // 1. Check Clash with Regular Academic Timetable
            $academicClash = JadwalKuliah::where('id_ruangan', $idRuangan)
                ->where('hari', $dayNameIndo)
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                })
                ->with('kelasKuliah.mataKuliah')
                ->first();

            if ($academicClash) {
                throw new Exception("Ruangan {$ruangan->nama_ruangan} sedang dipakai untuk perkuliahan reguler {$academicClash->kelasKuliah?->mataKuliah?->nama_mk} ({$academicClash->jam_mulai}-{$academicClash->jam_selesai}).");
            }

            // 2. Check Clash with other approved/pending reservations on the same date
            $bookingClash = BookingFasilitas::where('id_ruangan', $idRuangan)
                ->where('tanggal_booking', $tanggalBooking)
                ->whereIn('status_persetujuan', ['Diajukan', 'Disetujui'])
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                })
                ->first();

            if ($bookingClash) {
                throw new Exception("Ruangan {$ruangan->nama_ruangan} telah dibooking untuk kegiatan '{$bookingClash->nama_kegiatan}' pada jam {$bookingClash->jam_mulai}-{$bookingClash->jam_selesai}.");
            }

            // 3. Create Booking Record
            return BookingFasilitas::create([
                'id_ruangan' => $idRuangan,
                'id_user' => $idUser,
                'nama_kegiatan' => $namaKegiatan,
                'organisasi_pemohon' => $organisasi,
                'tanggal_booking' => $tanggalBooking,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'status_persetujuan' => 'Diajukan',
            ]);
        });
    }
}
