<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\KknRegistrasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KknController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? \App\Models\Siswa::first();

        $kknRegistration = KknRegistrasi::where('id_siswa', $siswa?->id ?? 1)
            ->latest()
            ->first();

        return view('siakad.kkn.index', compact('siswa', 'kknRegistration'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lokasi_kkn' => 'required|string|max:150',
            'kelompok' => 'required|string|max:50',
            'dpl_name' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $siswa = $user?->siswa ?? $user?->mahasiswa ?? \App\Models\Siswa::first();
        $siswaId = $siswa?->id ?? 1;

        $tahun = \App\Models\TahunAkademik::first() ?? \App\Models\TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->startOfYear(),
            'tgl_selesai' => now()->endOfYear(),
            'is_active' => true,
        ]);

        KknRegistrasi::updateOrCreate(
            ['id_siswa' => $siswaId, 'id_tahun_akademik' => $tahun->id],
            [
                'lokasi_kkn' => $validated['lokasi_kkn'],
                'kelompok' => $validated['kelompok'],
                'kecamatan' => 'Kecamatan Ciawi',
                'kabupaten' => 'Kabupaten Bogor',
                'status_pendaftaran' => 'SUBMITTED',
            ]
        );

        ActivityLog::record('KKN_REGISTER', "Mahasiswa mendaftar KKN di lokasi: {$validated['lokasi_kkn']}");

        return redirect()->route('siakad.kkn.index')->with('success', 'Pendaftaran Kuliah Kerja Nyata (KKN) berhasil disimpan!');
    }
}
