<?php

namespace App\Traits;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Ukt;
use App\Models\User;

trait ResolvesStudentUser
{
    /**
     * Resolve or auto-link Mahasiswa model for the authenticated student user.
     */
    protected function getStudentMahasiswa(User $user): Mahasiswa
    {
        if ($user->id_mahasiswa) {
            $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'ukt', 'dosenPa'])->find($user->id_mahasiswa);
            if ($mahasiswa) {
                return $mahasiswa;
            }
        }

        if ($user->id_siswa) {
            $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'ukt', 'dosenPa'])->find($user->id_siswa);
            if ($mahasiswa) {
                if (!$user->id_mahasiswa) {
                    $user->update(['id_mahasiswa' => $mahasiswa->id]);
                }
                return $mahasiswa;
            }

            // Sync from existing Siswa record
            $siswa = Siswa::find($user->id_siswa);
            if ($siswa) {
                $prodi = ProgramStudi::first();
                $ukt = Ukt::first();

                $mahasiswa = Mahasiswa::create([
                    'id' => $siswa->id,
                    'nim' => $siswa->nis ?? ('230101' . str_pad((string) $siswa->id, 4, '0', STR_PAD_LEFT)),
                    'nisn' => $siswa->nisn,
                    'nik' => $siswa->nik ?? '3201123456780001',
                    'nama' => $siswa->nama,
                    'id_prodi' => $prodi?->id,
                    'id_ukt' => $ukt?->id,
                    'alamat' => $siswa->alamat,
                    'nama_ibu_kandung' => 'Ibu Kandung',
                    'no_telp' => $siswa->no_telp,
                    'status_kelulusan' => $siswa->status_kelulusan ?? 'Aktif',
                    'consent_pdp_at' => $siswa->consent_pdp_at ?? now(),
                ]);

                $user->update(['id_mahasiswa' => $mahasiswa->id]);
                return $mahasiswa;
            }
        }

        // Fallback: Create first student record linked to this user
        $prodi = ProgramStudi::first();
        $ukt = Ukt::first();

        $mahasiswa = Mahasiswa::create([
            'nim' => '230101' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
            'nik' => '3201123456780001',
            'nama' => $user->name,
            'id_prodi' => $prodi?->id,
            'id_ukt' => $ukt?->id,
            'alamat' => 'Jl. Boulevard Kampus No. 1',
            'nama_ibu_kandung' => 'Ibu Kandung',
            'no_telp' => '081234567890',
            'status_kelulusan' => 'Aktif',
            'consent_pdp_at' => now(),
        ]);

        $user->update(['id_mahasiswa' => $mahasiswa->id]);
        return $mahasiswa;
    }
}
