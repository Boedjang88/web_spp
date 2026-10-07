<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Facility;
use App\Models\FacilityBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityBookingController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $facilities = Facility::with('ruangan')->where('status_fasilitas', 'Tersedia')->get();
        
        $bookings = FacilityBooking::with(['facility', 'pemohon'])
            ->latest()
            ->paginate(15);

        return view('siakad.facility.index', compact('facilities', 'bookings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_facility' => 'required|exists:facilities,id',
            'tujuan_penggunaan' => 'required|string|max:255',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $user = auth()->user();

        FacilityBooking::create([
            'id_facility' => $validated['id_facility'],
            'id_pemohon' => $user->id,
            'tujuan_penggunaan' => $validated['tujuan_penggunaan'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'status_booking' => 'APPROVED',
            'catatan_persetujuan' => 'Permohonan disetujui otomatis oleh sistem BAAK.',
        ]);

        ActivityLog::record('FACILITY_BOOK', "User {$user->name} mengajukan peminjaman fasilitas ID #{$validated['id_facility']}");

        return redirect()->route('siakad.fasilitas.index')->with('success', 'Permohonan peminjaman fasilitas kampus berhasil disetujui!');
    }
}
