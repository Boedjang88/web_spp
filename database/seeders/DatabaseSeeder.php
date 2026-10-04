<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\Presensi;
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
        // 1. Users (4-Tier RBAC)
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@sekolah.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@sekolah.id'],
            [
                'name' => 'Administrator Tata Usaha',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@sekolah.id'],
            [
                'name' => 'Petugas Loket 1',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'is_active' => true,
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

        // 4. Data Guru & Tenaga Pendidik
        $guru1 = Guru::firstOrCreate(
            ['nip' => '198501152010011002'],
            [
                'nama_guru' => 'Budi Santoso, S.Kom., M.T.',
                'jenis_kelamin' => 'L',
                'no_telp' => '081234567891',
                'email' => 'budi.santoso@smkmerdeka.sch.id',
                'alamat' => 'Jl. Dago No. 15, Bandung',
            ]
        );

        $guru2 = Guru::firstOrCreate(
            ['nip' => '198807222015022001'],
            [
                'nama_guru' => 'Sri Wahyuni, M.Pd.',
                'jenis_kelamin' => 'P',
                'no_telp' => '081234567892',
                'email' => 'sri.wahyuni@smkmerdeka.sch.id',
                'alamat' => 'Jl. Setiabudi No. 40, Bandung',
            ]
        );

        $guru3 = Guru::firstOrCreate(
            ['nip' => '199012052018011003'],
            [
                'nama_guru' => 'Hendra Pratama, S.Kom.',
                'jenis_kelamin' => 'L',
                'no_telp' => '081234567893',
                'email' => 'hendra.pratama@smkmerdeka.sch.id',
                'alamat' => 'Jl. Riau No. 100, Bandung',
            ]
        );

        // 5. Data Mata Pelajaran (Mapel)
        $mapelRPL = Mapel::firstOrCreate(
            ['kode_mapel' => 'RPL-01'],
            [
                'nama_mapel' => 'Pemrograman Web & Perangkat Bergerak',
                'kelompok' => 'Kejuruan',
                'kkm' => 75,
            ]
        );

        $mapelDB = Mapel::firstOrCreate(
            ['kode_mapel' => 'RPL-02'],
            [
                'nama_mapel' => 'Basis Data & Arsitektur API',
                'kelompok' => 'Kejuruan',
                'kkm' => 75,
            ]
        );

        $mapelMat = Mapel::firstOrCreate(
            ['kode_mapel' => 'MAT-01'],
            [
                'nama_mapel' => 'Matematika Terapan',
                'kelompok' => 'Umum',
                'kkm' => 70,
            ]
        );

        $mapelIndo = Mapel::firstOrCreate(
            ['kode_mapel' => 'IND-01'],
            [
                'nama_mapel' => 'Bahasa Indonesia',
                'kelompok' => 'Umum',
                'kkm' => 75,
            ]
        );

        // 6. Data Jadwal Pelajaran
        JadwalPelajaran::firstOrCreate(
            ['id_kelas' => $rpl1->id, 'id_mapel' => $mapelRPL->id, 'hari' => 'Senin'],
            [
                'id_guru' => $guru1->id,
                'jam_mulai' => '07:30',
                'jam_selesai' => '09:30',
                'ruangan' => 'Lab RPL 1',
            ]
        );

        JadwalPelajaran::firstOrCreate(
            ['id_kelas' => $rpl1->id, 'id_mapel' => $mapelDB->id, 'hari' => 'Senin'],
            [
                'id_guru' => $guru3->id,
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
                'ruangan' => 'Lab Database',
            ]
        );

        JadwalPelajaran::firstOrCreate(
            ['id_kelas' => $rpl1->id, 'id_mapel' => $mapelMat->id, 'hari' => 'Selasa'],
            [
                'id_guru' => $guru2->id,
                'jam_mulai' => '07:30',
                'jam_selesai' => '09:00',
                'ruangan' => 'Ruang Teori 12',
            ]
        );

        // 7. Data Siswa
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

        // 8. Data Nilai Siswa
        Nilai::firstOrCreate(
            ['id_siswa' => $siswa1->id, 'id_mapel' => $mapelRPL->id, 'semester' => 'Ganjil', 'tahun_ajaran' => '2025/2026'],
            [
                'id_guru' => $guru1->id,
                'nilai_tugas' => 88,
                'nilai_uts' => 85,
                'nilai_uas' => 92,
                'nilai_akhir' => 88.7,
                'predikat' => 'B',
                'catatan' => 'Sangat menguasai konsep MVC Laravel dan REST API.',
            ]
        );

        Nilai::firstOrCreate(
            ['id_siswa' => $siswa1->id, 'id_mapel' => $mapelDB->id, 'semester' => 'Ganjil', 'tahun_ajaran' => '2025/2026'],
            [
                'id_guru' => $guru3->id,
                'nilai_tugas' => 90,
                'nilai_uts' => 92,
                'nilai_uas' => 95,
                'nilai_akhir' => 92.6,
                'predikat' => 'A',
                'catatan' => 'Sangat mahir merancang normalisasi database relasional.',
            ]
        );

        Nilai::firstOrCreate(
            ['id_siswa' => $siswa1->id, 'id_mapel' => $mapelMat->id, 'semester' => 'Ganjil', 'tahun_ajaran' => '2025/2026'],
            [
                'id_guru' => $guru2->id,
                'nilai_tugas' => 80,
                'nilai_uts' => 78,
                'nilai_uas' => 82,
                'nilai_akhir' => 80.2,
                'predikat' => 'B',
                'catatan' => 'Capaian logika matematika sangat baik.',
            ]
        );

        // 9. Data Presensi Kehadiran
        Presensi::firstOrCreate(
            ['id_siswa' => $siswa1->id, 'tanggal' => now()->toDateString()],
            ['id_kelas' => $rpl1->id, 'status' => 'Hadir', 'keterangan' => 'Tepat waktu']
        );
        Presensi::firstOrCreate(
            ['id_siswa' => $siswa2->id, 'tanggal' => now()->toDateString()],
            ['id_kelas' => $rpl1->id, 'status' => 'Hadir', 'keterangan' => 'Tepat waktu']
        );

        // 10. Data Transaksi Pembayaran SPP
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
                'jumlah_bayar' => 300000,
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
                'jumlah_bayar' => 300000,
            ]
        );

        // 11. User Akun Terkait (Guru & Siswa)
        User::firstOrCreate(
            ['email' => 'guru@sekolah.id'],
            [
                'name' => 'Budi Santoso, S.Kom., M.T.',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'id_guru' => $guru1->id,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'siswa@sekolah.id'],
            [
                'name' => 'Muhammad Fauzan',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'id_siswa' => $siswa1->id,
                'is_active' => true,
            ]
        );
    }
}
