<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Siswa;
use App\Traits\ResolvesStudentUser;
use Illuminate\Http\Request;

class StudentBiodataController extends Controller
{
    use ResolvesStudentUser;

    /**
     * Display student biodata form
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $this->getStudentMahasiswa($user);

        return view('siakad.profile.biodata', compact('mahasiswa'));
    }

    /**
     * Update student biodata and encrypted PDP fields
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $mahasiswa = $this->getStudentMahasiswa($user);

        $validated = $request->validate([
            // Foto Profil (Avatar)
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

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

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/avatars');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $user->avatar_path = 'uploads/avatars/' . $filename;
        }

        $user->name = $validated['nama'];
        $user->save();

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

        return back()->with('success', 'Biodata dan foto profil mahasiswa berhasil diperbarui!');
    }
}
