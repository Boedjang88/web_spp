<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Siswa;
use Illuminate\Http\Request;

class StudentBiodataController extends Controller
{
    /**
     * Display student biodata form
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $studentId = $user->id_mahasiswa ?? $user->id_siswa;
        $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'dosenPa', 'ukt'])->findOrFail($studentId);

        return view('siakad.profile.biodata', compact('mahasiswa'));
    }

    /**
     * Update student biodata and encrypted PDP fields
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $studentId = $user->id_mahasiswa ?? $user->id_siswa;
        $mahasiswa = Mahasiswa::findOrFail($studentId);

        $validated = $request->validate([
            // Data Pribadi
            'nama' => 'required|string|max:150',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:20',
            'no_telp' => 'nullable|string|max:20',
            'email_pribadi' => 'nullable|email|max:100',

            // Alamat Domisili
            'alamat' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',

            // Orang Tua & Wali
            'nama_ayah' => 'nullable|string|max:150',
            'nama_ibu_kandung' => 'nullable|string|max:150',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'penghasilan_ortu' => 'nullable|string|max:100',
            'no_hp_wali' => 'nullable|string|max:20',

            // Asal Pendidikan
            'asal_sekolah' => 'nullable|string|max:150',
            'tahun_lulus_sekolah' => 'nullable|string|max:10',
            'nomor_ijazah_sekolah' => 'nullable|string|max:100',
        ]);

        $mahasiswa->update($validated);

        // Update PDP consent timestamp if not set
        if (!$mahasiswa->consent_pdp_at) {
            $mahasiswa->update([
                'consent_pdp_at' => now(),
                'consent_pdp_ip' => $request->ip(),
            ]);
        }

        // Backward compatibility sync with Siswa model
        if ($user->id_siswa) {
            $siswa = Siswa::find($user->id_siswa);
            if ($siswa) {
                $siswa->update([
                    'nama' => $validated['nama'],
                    'alamat' => $validated['alamat'] ?? $siswa->alamat,
                    'no_telp' => $validated['no_telp'] ?? $siswa->no_telp,
                ]);
            }
        }

        return back()->with('success', 'Data diri dan biodata mahasiswa berhasil diperbarui dan tersimpan aman.');
    }
}
