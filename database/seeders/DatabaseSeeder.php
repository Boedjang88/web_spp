<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users (Admin & Petugas)
        $admin = User::firstOrCreate(
            ['email' => 'admin@sekolah.id'],
            [
                'name' => 'Administrator SPP',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@sekolah.id'],
            [
                'name' => 'Petugas Loket 1',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
            ]
        );

        // 2. Data SPP
        $spp2024 = Spp::firstOrCreate(['tahun' => 2024], ['nominal' => 250000]);
        $spp2025 = Spp::firstOrCreate(['tahun' => 2025], ['nominal' => 300000]);
        $spp2026 = Spp::firstOrCreate(['tahun' => 2026], ['nominal' => 350000]);

        // 3. Data Kelas
        $rpl1 = Kelas::firstOrCreate(['nama_kelas' => 'XII RPL 1'], ['kompetensi_keahlian' => 'Rekayasa Perangkat Lunak']);
        $rpl2 = Kelas::firstOrCreate(['nama_kelas' => 'XII RPL 2'], ['kompetensi_keahlian' => 'Rekayasa Perangkat Lunak']);
        $tkj1 = Kelas::firstOrCreate(['nama_kelas' => 'XII TKJ 1'], ['kompetensi_keahlian' => 'Teknik Komputer dan Jaringan']);
        $dkv1 = Kelas::firstOrCreate(['nama_kelas' => 'XII DKV 1'], ['kompetensi_keahlian' => 'Desain Komunikasi Visual']);

        // 4. Data Siswa
        $siswa1 = Siswa::firstOrCreate(
            ['nisn' => '0051234567'],
            [
                'nis' => '2122001',
                'nama' => 'Ahmad Fauzi',
                'id_kelas' => $rpl1->id,
                'alamat' => 'Jl. Merdeka No. 45, Bandung',
                'no_telp' => '081234567890',
                'id_spp' => $spp2025->id,
            ]
        );

        $siswa2 = Siswa::firstOrCreate(
            ['nisn' => '0057654321'],
            [
                'nis' => '2122002',
                'nama' => 'Siti Nurhaliza',
                'id_kelas' => $rpl1->id,
                'alamat' => 'Jl. Pahlawan No. 12, Bandung',
                'no_telp' => '081298765432',
                'id_spp' => $spp2025->id,
            ]
        );

        $siswa3 = Siswa::firstOrCreate(
            ['nisn' => '0061122334'],
            [
                'nis' => '2223001',
                'nama' => 'Budi Santoso',
                'id_kelas' => $tkj1->id,
                'alamat' => 'Jl. Cendrawasih No. 8, Bandung',
                'no_telp' => '085612345678',
                'id_spp' => $spp2025->id,
            ]
        );

        $siswa4 = Siswa::firstOrCreate(
            ['nisn' => '0069988776'],
            [
                'nis' => '2223002',
                'nama' => 'Dewi Lestari',
                'id_kelas' => $dkv1->id,
                'alamat' => 'Jl. Diponegoro No. 88, Bandung',
                'no_telp' => '087812345678',
                'id_spp' => $spp2026->id,
            ]
        );

        // 5. Data Transaksi Pembayaran
        Pembayaran::firstOrCreate(
            [
                'id_siswa' => $siswa1->id,
                'bulan_dibayar' => 'Juli',
                'tahun_dibayar' => '2025',
            ],
            [
                'id_petugas' => $petugas->id,
                'tgl_bayar' => '2025-07-10',
                'id_spp' => $spp2025->id,
                'jumlah_bayar' => $spp2025->nominal,
            ]
        );

        Pembayaran::firstOrCreate(
            [
                'id_siswa' => $siswa1->id,
                'bulan_dibayar' => 'Agustus',
                'tahun_dibayar' => '2025',
            ],
            [
                'id_petugas' => $petugas->id,
                'tgl_bayar' => '2025-08-12',
                'id_spp' => $spp2025->id,
                'jumlah_bayar' => $spp2025->nominal,
            ]
        );

        Pembayaran::firstOrCreate(
            [
                'id_siswa' => $siswa2->id,
                'bulan_dibayar' => 'Juli',
                'tahun_dibayar' => '2025',
            ],
            [
                'id_petugas' => $admin->id,
                'tgl_bayar' => '2025-07-15',
                'id_spp' => $spp2025->id,
                'jumlah_bayar' => $spp2025->nominal,
            ]
        );
    }
}

