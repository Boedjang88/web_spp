<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Ukt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PmbController extends Controller
{
    public function showRegistrationForm(): View
    {
        $prodis = ProgramStudi::orderBy('nama_prodi')->get();
        return view('pmb.register', compact('prodis'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'id_prodi' => 'required|exists:program_studis,id',
            'nik' => 'nullable|string|max:30',
            'no_telp' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'nama_ibu_kandung' => 'nullable|string|max:150',
            'asal_sekolah' => 'nullable|string|max:150',
        ]);

        // Generate NIM
        $lastMhs = Mahasiswa::latest('id')->first();
        $nextId = ($lastMhs ? $lastMhs->id : 0) + 1;
        $nim = '10924094' . sprintf('%04d', $nextId);

        // Find or create default SPP & Kelas for new student
        $defaultSpp = Spp::firstOrCreate(['tahun' => 2026], ['nominal' => 3500000]);
        $prodi = ProgramStudi::find($validated['id_prodi']);
        $defaultKelas = Kelas::firstOrCreate(
            ['nama_kelas' => ($prodi->kode_prodi ?? 'IF') . '-1A'],
            ['kompetensi_keahlian' => $prodi->nama_prodi ?? 'Teknik Informatika']
        );
        $defaultUkt = Ukt::first() ?? Ukt::create(['kelompok_ukt' => 'UKT 3', 'nominal' => 4500000, 'tahun' => 2026]);

        // Create User
        $user = User::create([
            'name' => $validated['nama'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        // Create Siswa record
        $siswa = Siswa::create([
            'nisn' => $nim,
            'nis' => $nim,
            'nama' => $validated['nama'],
            'id_kelas' => $defaultKelas->id,
            'id_spp' => $defaultSpp->id,
            'alamat' => $validated['alamat'],
            'no_telp' => $validated['no_telp'],
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
            'consent_pdp_ip' => $request->ip(),
        ]);

        // Create Mahasiswa record
        $mahasiswa = Mahasiswa::create([
            'nim' => $nim,
            'nisn' => $nim,
            'nik' => $validated['nik'] ?? ('327501' . sprintf('%010d', rand(100000, 999999))),
            'nama' => $validated['nama'],
            'id_prodi' => $validated['id_prodi'],
            'id_ukt' => $defaultUkt->id,
            'alamat' => $validated['alamat'],
            'nama_ibu_kandung' => $validated['nama_ibu_kandung'] ?? 'Ibu Mahasiswa',
            'no_telp' => $validated['no_telp'],
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
            'consent_pdp_ip' => $request->ip(),
        ]);

        $user->update([
            'id_siswa' => $siswa->id,
            'id_mahasiswa' => $mahasiswa->id,
        ]);

        auth()->login($user);

        return redirect()->route('dashboard')->with('success', "Pendaftaran Mahasiswa Baru Berhasil! NIM Anda: {$nim}");
    }
}
