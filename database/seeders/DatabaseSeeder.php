<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\BankMitra;
use App\Models\BapPerkuliahan;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\FinancialClearance;
use App\Models\Gedung;
use App\Models\Guru;
use App\Models\JadwalKuliah;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\Mapel;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\PembayaranUkt;
use App\Models\Presensi;
use App\Models\PresensiMahasiswa;
use App\Models\ProgramStudi;
use App\Models\Ruangan;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\Submission;
use App\Models\TagihanUkt;
use App\Models\TahunAkademik;
use App\Models\Ukt;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with proper enterprise university dummy data.
     */
    public function run(): void
    {
        // =========================================================================
        // 1. LEGACY / BACKWARD COMPATIBILITY DATA (Ensures all tests pass 100%)
        // =========================================================================
        $spp2024 = Spp::firstOrCreate(['tahun' => 2024], ['nominal' => 250000]);
        $spp2025 = Spp::firstOrCreate(['tahun' => 2025], ['nominal' => 300000]);
        $spp2026 = Spp::firstOrCreate(['tahun' => 2026], ['nominal' => 350000]);

        $rpl1 = Kelas::firstOrCreate(['nama_kelas' => 'IF-3A'], ['kompetensi_keahlian' => 'Teknik Informatika']);
        $rpl2 = Kelas::firstOrCreate(['nama_kelas' => 'IF-3B'], ['kompetensi_keahlian' => 'Teknik Informatika']);
        $tkj1 = Kelas::firstOrCreate(['nama_kelas' => 'SI-2A'], ['kompetensi_keahlian' => 'Sistem Informasi']);
        $dkv1 = Kelas::firstOrCreate(['nama_kelas' => 'BD-1A'], ['kompetensi_keahlian' => 'Bisnis Digital']);

        $guru1 = Guru::firstOrCreate(
            ['nip' => '198501152010011002'],
            [
                'nama_guru' => 'Dr. Budi Santoso, M.Kom.',
                'jenis_kelamin' => 'L',
                'no_telp' => '081234567891',
                'email' => 'budi.santoso@univ.ac.id',
                'alamat' => 'Jl. Dago No. 15, Bandung',
            ]
        );

        $guru2 = Guru::firstOrCreate(
            ['nip' => '198807222015022001'],
            [
                'nama_guru' => 'Dr. Sri Wahyuni, M.Pd.',
                'jenis_kelamin' => 'P',
                'no_telp' => '081234567892',
                'email' => 'sri.wahyuni@univ.ac.id',
                'alamat' => 'Jl. Setiabudi No. 40, Bandung',
            ]
        );

        $guru3 = Guru::firstOrCreate(
            ['nip' => '199012052018011003'],
            [
                'nama_guru' => 'Dr. Hendra Pratama, S.Kom., M.T.',
                'jenis_kelamin' => 'L',
                'no_telp' => '081234567893',
                'email' => 'hendra.pratama@univ.ac.id',
                'alamat' => 'Jl. Riau No. 100, Bandung',
            ]
        );

        $mapelRPL = Mapel::firstOrCreate(['kode_mapel' => 'RPL-01'], ['nama_mapel' => 'Pemrograman Web & Perangkat Bergerak', 'kelompok' => 'Kejuruan', 'kkm' => 75]);
        $mapelDB = Mapel::firstOrCreate(['kode_mapel' => 'RPL-02'], ['nama_mapel' => 'Basis Data & Arsitektur API', 'kelompok' => 'Kejuruan', 'kkm' => 75]);
        $mapelMat = Mapel::firstOrCreate(['kode_mapel' => 'MAT-01'], ['nama_mapel' => 'Matematika Terapan', 'kelompok' => 'Umum', 'kkm' => 70]);

        JadwalPelajaran::firstOrCreate(
            ['id_kelas' => $rpl1->id, 'id_mapel' => $mapelRPL->id, 'hari' => 'Senin'],
            ['id_guru' => $guru1->id, 'jam_mulai' => '07:30', 'jam_selesai' => '09:30', 'ruangan' => 'Lab RPL 1']
        );

        $siswa1 = Siswa::firstOrCreate(
            ['nisn' => '0051234567'],
            [
                'nis' => '2301010001',
                'nama' => 'Muhammad Fauzan',
                'id_kelas' => $rpl1->id,
                'alamat' => 'Jl. Merdeka No. 45, Bandung',
                'no_telp' => '081234567890',
                'id_spp' => $spp2025->id,
                'status_kelulusan' => 'Aktif',
                'consent_pdp_at' => now(),
            ]
        );

        $siswa2 = Siswa::firstOrCreate(
            ['nisn' => '0057654321'],
            [
                'nis' => '2301010002',
                'nama' => 'Siti Nurhaliza',
                'id_kelas' => $rpl1->id,
                'alamat' => 'Jl. Pahlawan No. 12, Bandung',
                'no_telp' => '081298765432',
                'id_spp' => $spp2025->id,
                'status_kelulusan' => 'Aktif',
                'consent_pdp_at' => now(),
            ]
        );

        // =========================================================================
        // 2. UNIVERSITY ACADEMIC STRUCTURE (Fakultas, Prodi, Kurikulum, TA)
        // =========================================================================
        $fti = Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FTI'],
            [
                'nama_fakultas' => 'Fakultas Teknologi Informasi',
                'nama_fakultas_en' => 'Faculty of Information Technology',
                'dekan' => 'Prof. Dr. Ir. H. Ahmad Dahlan, M.Sc.',
                'nip_dekan' => '197005121995031001',
            ]
        );

        $feb = Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FEB'],
            [
                'nama_fakultas' => 'Fakultas Ekonomi & Bisnis',
                'nama_fakultas_en' => 'Faculty of Economics & Business',
                'dekan' => 'Prof. Dr. Maya Kartika, S.E., M.M., Ak.',
                'nip_dekan' => '197208141997022001',
            ]
        );

        $fh = Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FH'],
            [
                'nama_fakultas' => 'Fakultas Hukum',
                'nama_fakultas_en' => 'Faculty of Law',
                'dekan' => 'Prof. Dr. Bambang Wijaya, S.H., M.H.',
                'nip_dekan' => '196803201993031002',
            ]
        );

        $ft = Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FT'],
            [
                'nama_fakultas' => 'Fakultas Teknik',
                'nama_fakultas_en' => 'Faculty of Engineering',
                'dekan' => 'Dr. Ir. Ridwan Kamil, M.T.',
                'nip_dekan' => '197110041998031001',
            ]
        );

        // Program Studi
        $prodiIF = ProgramStudi::firstOrCreate(
            ['kode_prodi' => '55201'],
            ['id_fakultas' => $fti->id, 'nama_prodi' => 'Teknik Informatika', 'nama_prodi_en' => 'Informatics Engineering', 'jenjang' => 'S1', 'akreditasi' => 'Unggul', 'kaprodi' => 'Dr. Budi Santoso, M.Kom.', 'nip_kaprodi' => '198501152010011002']
        );

        $prodiSI = ProgramStudi::firstOrCreate(
            ['kode_prodi' => '57201'],
            ['id_fakultas' => $fti->id, 'nama_prodi' => 'Sistem Informasi', 'nama_prodi_en' => 'Information Systems', 'jenjang' => 'S1', 'akreditasi' => 'Baik Sekali', 'kaprodi' => 'Dr. Hendra Pratama, S.Kom., M.T.', 'nip_kaprodi' => '199012052018011003']
        );

        $prodiMN = ProgramStudi::firstOrCreate(
            ['kode_prodi' => '61201'],
            ['id_fakultas' => $feb->id, 'nama_prodi' => 'Manajemen', 'nama_prodi_en' => 'Management', 'jenjang' => 'S1', 'akreditasi' => 'Unggul', 'kaprodi' => 'Dr. Maya Kartika, S.E., M.M.', 'nip_kaprodi' => '197208141997022001']
        );

        $prodiAK = ProgramStudi::firstOrCreate(
            ['kode_prodi' => '62201'],
            ['id_fakultas' => $feb->id, 'nama_prodi' => 'Akuntansi', 'nama_prodi_en' => 'Accounting', 'jenjang' => 'S1', 'akreditasi' => 'A', 'kaprodi' => 'Dra. Rina Astuti, M.Si., Ak.', 'nip_kaprodi' => '197504102000032001']
        );

        $prodiHK = ProgramStudi::firstOrCreate(
            ['kode_prodi' => '74201'],
            ['id_fakultas' => $fh->id, 'nama_prodi' => 'Ilmu Hukum', 'nama_prodi_en' => 'Legal Studies', 'jenjang' => 'S1', 'akreditasi' => 'Unggul', 'kaprodi' => 'Dr. Suhendra, S.H., M.H.', 'nip_kaprodi' => '197809152003121001']
        );

        // Kurikulum MBKM
        $kurikulumIF = Kurikulum::firstOrCreate(['id_prodi' => $prodiIF->id, 'tahun_mulai' => 2024], ['nama_kurikulum' => 'Kurikulum MBKM Informatika 2024', 'total_sks_lulus' => 144, 'is_active' => true]);
        $kurikulumSI = Kurikulum::firstOrCreate(['id_prodi' => $prodiSI->id, 'tahun_mulai' => 2024], ['nama_kurikulum' => 'Kurikulum MBKM Sistem Informasi 2024', 'total_sks_lulus' => 144, 'is_active' => true]);
        $kurikulumMN = Kurikulum::firstOrCreate(['id_prodi' => $prodiMN->id, 'tahun_mulai' => 2024], ['nama_kurikulum' => 'Kurikulum MBKM Manajemen 2024', 'total_sks_lulus' => 144, 'is_active' => true]);

        // Tahun Akademik
        $taPast1 = TahunAkademik::firstOrCreate(['kode_tahun' => '20241'], ['nama_tahun' => '2024/2025 Ganjil', 'semester' => 'Ganjil', 'tgl_mulai' => '2024-09-01', 'tgl_selesai' => '2025-01-31', 'tgl_krs_mulai' => '2024-08-15', 'tgl_krs_selesai' => '2024-09-10', 'is_active' => false]);
        $taPast2 = TahunAkademik::firstOrCreate(['kode_tahun' => '20242'], ['nama_tahun' => '2024/2025 Genap', 'semester' => 'Genap', 'tgl_mulai' => '2025-02-01', 'tgl_selesai' => '2025-06-30', 'tgl_krs_mulai' => '2025-01-15', 'tgl_krs_selesai' => '2025-02-10', 'is_active' => false]);
        $taActive = TahunAkademik::firstOrCreate(['kode_tahun' => '20251'], ['nama_tahun' => '2025/2026 Ganjil', 'semester' => 'Ganjil', 'tgl_mulai' => '2025-09-01', 'tgl_selesai' => '2026-01-31', 'tgl_krs_mulai' => '2025-08-15', 'tgl_krs_selesai' => '2025-09-10', 'is_active' => true]);

        // UKT Groups
        $uktIF_1 = Ukt::firstOrCreate(['id_prodi' => $prodiIF->id, 'kelompok_ukt' => 'Kelompok 1', 'tahun' => 2025], ['nominal' => 500000, 'biaya_praktikum' => 0, 'biaya_kemahasiswaan' => 100000, 'deskripsi' => 'UKT Golongan 1 (KIP-K)']);
        $uktIF_3 = Ukt::firstOrCreate(['id_prodi' => $prodiIF->id, 'kelompok_ukt' => 'Kelompok 3', 'tahun' => 2025], ['nominal' => 5000000, 'biaya_praktikum' => 500000, 'biaya_kemahasiswaan' => 250000, 'deskripsi' => 'UKT Golongan 3 (Reguler)']);
        $uktIF_5 = Ukt::firstOrCreate(['id_prodi' => $prodiIF->id, 'kelompok_ukt' => 'Kelompok 5', 'tahun' => 2025], ['nominal' => 10000000, 'biaya_praktikum' => 1000000, 'biaya_kemahasiswaan' => 250000, 'deskripsi' => 'UKT Golongan 5 (Mandiri/International)']);

        $uktSI_3 = Ukt::firstOrCreate(['id_prodi' => $prodiSI->id, 'kelompok_ukt' => 'Kelompok 3', 'tahun' => 2025], ['nominal' => 4500000, 'biaya_praktikum' => 400000, 'biaya_kemahasiswaan' => 200000, 'deskripsi' => 'UKT Golongan 3 Reguler SI']);
        $uktMN_3 = Ukt::firstOrCreate(['id_prodi' => $prodiMN->id, 'kelompok_ukt' => 'Kelompok 3', 'tahun' => 2025], ['nominal' => 4000000, 'biaya_praktikum' => 200000, 'biaya_kemahasiswaan' => 200000, 'deskripsi' => 'UKT Golongan 3 Reguler FEB']);

        // Gedung & Ruangan
        $gedungA = Gedung::firstOrCreate(['kode_gedung' => 'GD-A'], ['nama_gedung' => 'Gedung Rektorat & FTI', 'latitude' => -6.917464, 'longitude' => 107.619123]);
        $gedungB = Gedung::firstOrCreate(['kode_gedung' => 'GD-B'], ['nama_gedung' => 'Gedung FEB & Pascasarjana', 'latitude' => -6.918000, 'longitude' => 107.620000]);

        $ruanganLab1 = Ruangan::firstOrCreate(['kode_ruangan' => 'LAB-01'], ['id_gedung' => $gedungA->id, 'nama_ruangan' => 'Laboratorium Software Engineering 1', 'kapasitas' => 40, 'jenis_ruangan' => 'Laboratorium', 'latitude' => -6.917464, 'longitude' => 107.619123, 'radius_meter' => 20, 'is_active' => true]);
        $ruanganLab2 = Ruangan::firstOrCreate(['kode_ruangan' => 'LAB-02'], ['id_gedung' => $gedungA->id, 'nama_ruangan' => 'Laboratorium AI & Cyber Security', 'kapasitas' => 40, 'jenis_ruangan' => 'Laboratorium', 'latitude' => -6.917464, 'longitude' => 107.619123, 'radius_meter' => 20, 'is_active' => true]);
        $ruangan301  = Ruangan::firstOrCreate(['kode_ruangan' => 'RT-301'], ['id_gedung' => $gedungA->id, 'nama_ruangan' => 'Ruang Teori FTI 301', 'kapasitas' => 50, 'jenis_ruangan' => 'Teori', 'latitude' => -6.917464, 'longitude' => 107.619123, 'radius_meter' => 20, 'is_active' => true]);
        $ruanganFEB1 = Ruangan::firstOrCreate(['kode_ruangan' => 'FEB-101'], ['id_gedung' => $gedungB->id, 'nama_ruangan' => 'Ruang Teori FEB 101', 'kapasitas' => 60, 'jenis_ruangan' => 'Teori', 'latitude' => -6.918000, 'longitude' => 107.620000, 'radius_meter' => 25, 'is_active' => true]);

        // =========================================================================
        // 3. DOSEN (Lecturers with Professional Profiles & Titles)
        // =========================================================================
        $dosen1 = Dosen::firstOrCreate(
            ['nidn' => '0415018501'],
            ['id_prodi' => $prodiIF->id, 'nama_dosen' => 'Budi Santoso', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'M.Kom.', 'jenis_kelamin' => 'L', 'no_telp' => '081234567891', 'email' => 'budi.santoso@univ.ac.id', 'alamat' => 'Jl. Dago No. 15, Bandung', 'is_active' => true]
        );

        $dosen2 = Dosen::firstOrCreate(
            ['nidn' => '0405129001'],
            ['id_prodi' => $prodiIF->id, 'nama_dosen' => 'Hendra Pratama', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.Kom., M.T.', 'jenis_kelamin' => 'L', 'no_telp' => '081234567893', 'email' => 'hendra.pratama@univ.ac.id', 'alamat' => 'Jl. Riau No. 100, Bandung', 'is_active' => true]
        );

        $dosen3 = Dosen::firstOrCreate(
            ['nidn' => '0418088802'],
            ['id_prodi' => $prodiSI->id, 'nama_dosen' => 'Rina Astuti', 'gelar_depan' => 'Dr. Eng.', 'gelar_belakang' => 'S.T., M.T.', 'jenis_kelamin' => 'P', 'no_telp' => '081234567894', 'email' => 'rina.astuti@univ.ac.id', 'alamat' => 'Jl. Ir. H. Juanda No. 88, Bandung', 'is_active' => true]
        );

        $dosen4 = Dosen::firstOrCreate(
            ['nidn' => '0412038401'],
            ['id_prodi' => $prodiMN->id, 'nama_dosen' => 'Maya Kartika', 'gelar_depan' => 'Prof. Dr.', 'gelar_belakang' => 'S.E., M.M., Ak.', 'jenis_kelamin' => 'P', 'no_telp' => '081234567895', 'email' => 'maya.kartika@univ.ac.id', 'alamat' => 'Jl. Buah Batu No. 210, Bandung', 'is_active' => true]
        );

        $dosen5 = Dosen::firstOrCreate(
            ['nidn' => '0401017001'],
            ['id_prodi' => $prodiHK->id, 'nama_dosen' => 'Bambang Wijaya', 'gelar_depan' => 'Prof. Dr.', 'gelar_belakang' => 'S.H., M.H.', 'jenis_kelamin' => 'L', 'no_telp' => '081234567896', 'email' => 'bambang.wijaya@univ.ac.id', 'alamat' => 'Jl. Dipatiukur No. 42, Bandung', 'is_active' => true]
        );

        // =========================================================================
        // 4. MAHASISWA & SISWA (15 Proper University Student Profiles)
        // =========================================================================
        $studentDataset = [
            ['nim' => '2301010001', 'nisn' => '0051234567', 'nik' => '3201123456780001', 'nama' => 'Muhammad Fauzan', 'gender' => 'L', 'prodi' => $prodiIF->id, 'pa' => $dosen1->id, 'ukt' => $uktIF_3->id, 'email' => 'fauzan@student.univ.ac.id', 'phone' => '081234567890'],
            ['nim' => '2301010002', 'nisn' => '0057654321', 'nik' => '3201123456780002', 'nama' => 'Siti Nurhaliza', 'gender' => 'P', 'prodi' => $prodiIF->id, 'pa' => $dosen1->id, 'ukt' => $uktIF_3->id, 'email' => 'siti@student.univ.ac.id', 'phone' => '081298765432'],
            ['nim' => '2301010003', 'nisn' => '0051122334', 'nik' => '3201123456780003', 'nama' => 'Ahmad Rizky Pratama', 'gender' => 'L', 'prodi' => $prodiIF->id, 'pa' => $dosen2->id, 'ukt' => $uktIF_5->id, 'email' => 'rizky@student.univ.ac.id', 'phone' => '081311223344'],
            ['nim' => '2301010004', 'nisn' => '0052233445', 'nik' => '3201123456780004', 'nama' => 'Anisa Rahmawati', 'gender' => 'P', 'prodi' => $prodiIF->id, 'pa' => $dosen2->id, 'ukt' => $uktIF_1->id, 'email' => 'anisa@student.univ.ac.id', 'phone' => '081322334455'],
            ['nim' => '2301010005', 'nisn' => '0053344556', 'nik' => '3201123456780005', 'nama' => 'Bagus Setiawan', 'gender' => 'L', 'prodi' => $prodiIF->id, 'pa' => $dosen1->id, 'ukt' => $uktIF_3->id, 'email' => 'bagus@student.univ.ac.id', 'phone' => '081333445566'],
            ['nim' => '2301010006', 'nisn' => '0054455667', 'nik' => '3201123456780006', 'nama' => 'Dwi Lestari', 'gender' => 'P', 'prodi' => $prodiIF->id, 'pa' => $dosen2->id, 'ukt' => $uktIF_3->id, 'email' => 'dwi@student.univ.ac.id', 'phone' => '081344556677'],
            ['nim' => '2301010007', 'nisn' => '0055566778', 'nik' => '3201123456780007', 'nama' => 'Fajar Nugraha', 'gender' => 'L', 'prodi' => $prodiIF->id, 'pa' => $dosen1->id, 'ukt' => $uktIF_3->id, 'email' => 'fajar@student.univ.ac.id', 'phone' => '081355667788'],
            ['nim' => '2301010008', 'nisn' => '0056677889', 'nik' => '3201123456780008', 'nama' => 'Gita Gutawa', 'gender' => 'P', 'prodi' => $prodiIF->id, 'pa' => $dosen2->id, 'ukt' => $uktIF_5->id, 'email' => 'gita@student.univ.ac.id', 'phone' => '081366778899'],
            ['nim' => '2301010009', 'nisn' => '0057788990', 'nik' => '3201123456780009', 'nama' => 'Hadi Kurniawan', 'gender' => 'L', 'prodi' => $prodiIF->id, 'pa' => $dosen1->id, 'ukt' => $uktIF_3->id, 'email' => 'hadi@student.univ.ac.id', 'phone' => '081377889900'],
            ['nim' => '2301010010', 'nisn' => '0058899001', 'nik' => '3201123456780010', 'nama' => 'Indah Permatasari', 'gender' => 'P', 'prodi' => $prodiIF->id, 'pa' => $dosen2->id, 'ukt' => $uktIF_3->id, 'email' => 'indah@student.univ.ac.id', 'phone' => '081388990011'],
            ['nim' => '2302010001', 'nisn' => '0059900112', 'nik' => '3201123456780011', 'nama' => 'Kevin Sanjaya', 'gender' => 'L', 'prodi' => $prodiSI->id, 'pa' => $dosen3->id, 'ukt' => $uktSI_3->id, 'email' => 'kevin@student.univ.ac.id', 'phone' => '081399001122'],
            ['nim' => '2302010002', 'nisn' => '0050011223', 'nik' => '3201123456780012', 'nama' => 'Lani Wijaya', 'gender' => 'P', 'prodi' => $prodiSI->id, 'pa' => $dosen3->id, 'ukt' => $uktSI_3->id, 'email' => 'lani@student.univ.ac.id', 'phone' => '081300112233'],
            ['nim' => '2303010001', 'nisn' => '0051133557', 'nik' => '3201123456780013', 'nama' => 'Mahendra Saputra', 'gender' => 'L', 'prodi' => $prodiMN->id, 'pa' => $dosen4->id, 'ukt' => $uktMN_3->id, 'email' => 'mahendra@student.univ.ac.id', 'phone' => '081311335577'],
            ['nim' => '2303010002', 'nisn' => '0052244668', 'nik' => '3201123456780014', 'nama' => 'Nabila Putri', 'gender' => 'P', 'prodi' => $prodiMN->id, 'pa' => $dosen4->id, 'ukt' => $uktMN_3->id, 'email' => 'nabila@student.univ.ac.id', 'phone' => '081322446688'],
            ['nim' => '2303010003', 'nisn' => '0053355779', 'nik' => '3201123456780015', 'nama' => 'Oscar Lawalata', 'gender' => 'L', 'prodi' => $prodiMN->id, 'pa' => $dosen4->id, 'ukt' => $uktMN_3->id, 'email' => 'oscar@student.univ.ac.id', 'phone' => '081333557799'],
        ];

        $mahasiswaModels = [];
        $siswaModels = [];
        foreach ($studentDataset as $s) {
            $m = Mahasiswa::firstOrCreate(
                ['nim' => $s['nim']],
                [
                    'nisn' => $s['nisn'],
                    'nik' => $s['nik'],
                    'nama' => $s['nama'],
                    'tempat_lahir' => 'Bandung',
                    'tanggal_lahir' => '2004-05-15',
                    'jenis_kelamin' => $s['gender'],
                    'agama' => 'Islam',
                    'id_prodi' => $s['prodi'],
                    'id_dosen_pa' => $s['pa'],
                    'id_ukt' => $s['ukt'],
                    'alamat' => 'Jl. Merdeka No. ' . rand(1, 100) . ', Bandung',
                    'rt' => '0' . rand(1, 9),
                    'rw' => '0' . rand(1, 9),
                    'kelurahan' => 'Babakan Ciamis',
                    'kecamatan' => 'Sumur Bandung',
                    'kota' => 'Kota Bandung',
                    'kode_pos' => '40117',
                    'nama_ibu_kandung' => 'Siti Aminah',
                    'no_telp' => $s['phone'],
                    'email_pribadi' => $s['email'],
                    'status_kelulusan' => 'Aktif',
                    'consent_pdp_at' => now(),
                    'consent_pdp_ip' => '127.0.0.1',
                ]
            );

            $allKelasIds = [$rpl1->id, $rpl2->id, $tkj1->id, $dkv1->id];
            $sis = Siswa::firstOrCreate(
                ['nisn' => $s['nisn']],
                [
                    'nis' => $s['nim'],
                    'nama' => $s['nama'],
                    'id_kelas' => $allKelasIds[$idx % count($allKelasIds)],
                    'alamat' => 'Jl. Merdeka No. ' . rand(1, 100) . ', Bandung',
                    'no_telp' => $s['phone'],
                    'id_spp' => $spp2025->id,
                    'status_kelulusan' => 'Aktif',
                    'consent_pdp_at' => now(),
                ]
            );

            $mahasiswaModels[] = $m;
            $siswaModels[] = $sis;
        }

        $mahasiswa1 = $mahasiswaModels[0];

        // =========================================================================
        // 5. MATA KULIAH & KELAS KULIAH (Academic Courses)
        // =========================================================================
        $mk1 = MataKuliah::firstOrCreate(['kode_mk' => 'IF301'], ['id_kurikulum' => $kurikulumIF->id, 'nama_mk' => 'Arsitektur Sistem Enterprise', 'nama_mk_en' => 'Enterprise System Architecture', 'sks_teori' => 2, 'sks_praktik' => 1, 'sks_total' => 3, 'semester_rekomendasi' => 5, 'jenis_mk' => 'Wajib Program Studi']);
        $mk2 = MataKuliah::firstOrCreate(['kode_mk' => 'IF302'], ['id_kurikulum' => $kurikulumIF->id, 'nama_mk' => 'Pemrograman Web Lanjut & API Security', 'nama_mk_en' => 'Advanced Web Programming & API Security', 'sks_teori' => 1, 'sks_praktik' => 2, 'sks_total' => 3, 'semester_rekomendasi' => 5, 'jenis_mk' => 'Wajib Program Studi']);
        $mk3 = MataKuliah::firstOrCreate(['kode_mk' => 'IF303'], ['id_kurikulum' => $kurikulumIF->id, 'nama_mk' => 'Basis Data Skala Besar & NoSQL', 'nama_mk_en' => 'Big Data & NoSQL Databases', 'sks_teori' => 2, 'sks_praktik' => 1, 'sks_total' => 3, 'semester_rekomendasi' => 5, 'jenis_mk' => 'Wajib Program Studi']);
        $mk4 = MataKuliah::firstOrCreate(['kode_mk' => 'IF401'], ['id_kurikulum' => $kurikulumIF->id, 'nama_mk' => 'Kecerdasan Buatan & Machine Learning', 'nama_mk_en' => 'Artificial Intelligence & Machine Learning', 'sks_teori' => 2, 'sks_praktik' => 1, 'sks_total' => 3, 'semester_rekomendasi' => 7, 'jenis_mk' => 'Wajib Program Studi']);
        $mk5 = MataKuliah::firstOrCreate(['kode_mk' => 'SI201'], ['id_kurikulum' => $kurikulumSI->id, 'nama_mk' => 'Analisis & Perancangan Sistem Informasi', 'nama_mk_en' => 'Information Systems Analysis & Design', 'sks_teori' => 3, 'sks_praktik' => 0, 'sks_total' => 3, 'semester_rekomendasi' => 3, 'jenis_mk' => 'Wajib Program Studi']);
        $mk6 = MataKuliah::firstOrCreate(['kode_mk' => 'MN101'], ['id_kurikulum' => $kurikulumMN->id, 'nama_mk' => 'Manajemen Keuangan & Investasi', 'nama_mk_en' => 'Financial Management & Investment', 'sks_teori' => 3, 'sks_praktik' => 0, 'sks_total' => 3, 'semester_rekomendasi' => 3, 'jenis_mk' => 'Wajib Program Studi']);

        // Kelas Kuliah
        $kelas1 = KelasKuliah::firstOrCreate(['id_mk' => $mk1->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'IF301-A'], ['id_dosen' => $dosen1->id, 'ruang' => 'Lab Software Engineering 1', 'hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30', 'kuota_maksimal' => 40, 'total_terisi' => 10]);
        $kelas2 = KelasKuliah::firstOrCreate(['id_mk' => $mk2->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'IF302-A'], ['id_dosen' => $dosen2->id, 'ruang' => 'Lab AI & Cyber Security', 'hari' => 'Selasa', 'jam_mulai' => '10:00', 'jam_selesai' => '12:30', 'kuota_maksimal' => 40, 'total_terisi' => 10]);
        $kelas3 = KelasKuliah::firstOrCreate(['id_mk' => $mk3->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'IF303-A'], ['id_dosen' => $dosen1->id, 'ruang' => 'Ruang Teori FTI 301', 'hari' => 'Rabu', 'jam_mulai' => '13:00', 'jam_selesai' => '15:30', 'kuota_maksimal' => 40, 'total_terisi' => 10]);
        $kelas4 = KelasKuliah::firstOrCreate(['id_mk' => $mk4->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'IF401-A'], ['id_dosen' => $dosen2->id, 'ruang' => 'Lab AI & Cyber Security', 'hari' => 'Kamis', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30', 'kuota_maksimal' => 35, 'total_terisi' => 8]);
        $kelas5 = KelasKuliah::firstOrCreate(['id_mk' => $mk5->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'SI201-A'], ['id_dosen' => $dosen3->id, 'ruang' => 'Ruang Teori FTI 301', 'hari' => 'Jumat', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30', 'kuota_maksimal' => 50, 'total_terisi' => 5]);
        $kelas6 = KelasKuliah::firstOrCreate(['id_mk' => $mk6->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'MN101-A'], ['id_dosen' => $dosen4->id, 'ruang' => 'Ruang Teori FEB 101', 'hari' => 'Senin', 'jam_mulai' => '13:00', 'jam_selesai' => '15:30', 'kuota_maksimal' => 60, 'total_terisi' => 3]);

        // Jadwal Kuliah
        JadwalKuliah::firstOrCreate(['id_kelas_kuliah' => $kelas1->id, 'hari' => 'Senin'], ['id_ruangan' => $ruanganLab1->id, 'id_guru' => $guru1->id, 'jam_mulai' => '08:00', 'jam_selesai' => '10:30']);
        JadwalKuliah::firstOrCreate(['id_kelas_kuliah' => $kelas2->id, 'hari' => 'Selasa'], ['id_ruangan' => $ruanganLab2->id, 'id_guru' => $guru3->id, 'jam_mulai' => '10:00', 'jam_selesai' => '12:30']);
        JadwalKuliah::firstOrCreate(['id_kelas_kuliah' => $kelas3->id, 'hari' => 'Rabu'], ['id_ruangan' => $ruangan301->id, 'id_guru' => $guru1->id, 'jam_mulai' => '13:00', 'jam_selesai' => '15:30']);
        JadwalKuliah::firstOrCreate(['id_kelas_kuliah' => $kelas4->id, 'hari' => 'Kamis'], ['id_ruangan' => $ruanganLab2->id, 'id_guru' => $guru3->id, 'jam_mulai' => '08:00', 'jam_selesai' => '10:30']);
        JadwalKuliah::firstOrCreate(['id_kelas_kuliah' => $kelas5->id, 'hari' => 'Jumat'], ['id_ruangan' => $ruangan301->id, 'id_guru' => $guru2->id, 'jam_mulai' => '08:00', 'jam_selesai' => '10:30']);

        // =========================================================================
        // 6. BANK MITRA & UKT INVOICING & FINANCIAL CLEARANCE
        // =========================================================================
        $bankBni = BankMitra::firstOrCreate(['kode_bank' => 'BNI'], ['nama_bank' => 'PT Bank Negara Indonesia (Persero) Tbk', 'prefix_va' => '988', 'secret_key' => 'bni_sec_key_production_2026', 'webhook_url' => 'https://siakad.univ.ac.id/api/v1/h2h/webhook/bni', 'is_active' => true]);
        $bankMandiri = BankMitra::firstOrCreate(['kode_bank' => 'MANDIRI'], ['nama_bank' => 'PT Bank Mandiri (Persero) Tbk', 'prefix_va' => '889', 'secret_key' => 'mandiri_sec_key_production_2026', 'webhook_url' => 'https://siakad.univ.ac.id/api/v1/h2h/webhook/mandiri', 'is_active' => true]);

        // Generate UKT Tagihan & Clearances for all students
        foreach ($mahasiswaModels as $index => $mhs) {
            $sis = $siswaModels[$index];
            $isPaid = $index < 12; // 80% cleared, 20% pending lock

            $tagihan = TagihanUkt::firstOrCreate(
                ['id_mahasiswa' => $mhs->id, 'id_tahun_akademik' => $taActive->id],
                [
                    'id_bank_mitra' => $bankBni->id,
                    'nomor_va' => '988' . $mhs->nim . '001',
                    'nomor_invoice' => 'INV-UKT-20251-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'biaya_ukt' => 5000000,
                    'biaya_praktikum' => 500000,
                    'biaya_kemahasiswaan' => 250000,
                    'total_tagihan' => 5750000,
                    'total_potongan_beasiswa' => 0,
                    'total_harus_bayar' => 5750000,
                    'total_sudah_bayar' => $isPaid ? 5750000 : 0,
                    'status_pembayaran' => $isPaid ? 'Lunas' : 'Belum Bayar',
                    'tgl_jatuh_tempo' => now()->addMonth(),
                    'tgl_lunas' => $isPaid ? now()->subDays(rand(1, 15)) : null,
                ]
            );

            if ($isPaid) {
                FinancialClearance::firstOrCreate(
                    ['id_siswa' => $sis->id, 'id_tahun_akademik' => $taActive->id],
                    ['is_krs_unlocked' => true, 'is_uts_unlocked' => true, 'is_uas_unlocked' => true, 'unlocked_at' => now(), 'unlocked_by_channel' => 'H2H_BANK_BNI']
                );

                // KRS Registration
                $krs = Krs::firstOrCreate(
                    ['id_siswa' => $sis->id, 'id_tahun_akademik' => $taActive->id],
                    ['max_sks_diizinkan' => 24, 'total_sks_diambil' => 12, 'status_krs' => 'Disetujui', 'catatan_pembimbing' => 'Rencana studi telah disetujui Dosen PA. Semangat perkuliahan!']
                );

                KrsDetail::firstOrCreate(['id_krs' => $krs->id, 'id_kelas_kuliah' => $kelas1->id], ['status_ambil' => 'Baru']);
                KrsDetail::firstOrCreate(['id_krs' => $krs->id, 'id_kelas_kuliah' => $kelas2->id], ['status_ambil' => 'Baru']);
                KrsDetail::firstOrCreate(['id_krs' => $krs->id, 'id_kelas_kuliah' => $kelas3->id], ['status_ambil' => 'Baru']);
                KrsDetail::firstOrCreate(['id_krs' => $krs->id, 'id_kelas_kuliah' => $kelas4->id], ['status_ambil' => 'Baru']);

                // Academic Grade Record
                Nilai::firstOrCreate(
                    ['id_siswa' => $sis->id, 'id_mapel' => $mapelRPL->id],
                    ['id_guru' => $guru1->id, 'nilai_tugas' => rand(85, 95), 'nilai_uts' => rand(80, 90), 'nilai_uas' => rand(85, 95), 'nilai_akhir' => rand(85, 93), 'predikat' => 'A']
                );
            }
        }

        $tagihan1 = TagihanUkt::where('id_mahasiswa', $mahasiswa1->id)->first();

        // =========================================================================
        // 7. BAP & PRESENSI & LMS ASSIGNMENTS & SUBMISSIONS
        // =========================================================================
        $bap1 = BapPerkuliahan::firstOrCreate(
            ['id_kelas_kuliah' => $kelas1->id, 'pertemuan_ke' => 1],
            [
                'id_guru' => $guru1->id,
                'id_ruangan' => $ruanganLab1->id,
                'tanggal_pelaksanaan' => now()->toDateString(),
                'jam_mulai_real' => '08:00',
                'jam_selesai_real' => '10:30',
                'materi_pembahasan' => 'Pengenalan Arsitektur Domain-Driven Design & High Concurrency Locking',
                'catatan_dosen' => 'Mahasiswa hadir dengan antusias, pengenalan sistem SIAKAD Enterprise.',
                'total_mahasiswa_hadir' => 10,
                'total_mahasiswa_absen' => 0,
                'status_verifikasi' => 'Diverifikasi BAAK',
                'digital_signature_hash' => hash('sha256', 'BAP-IF301-' . now()->toDateString()),
            ]
        );

        PresensiMahasiswa::firstOrCreate(
            ['id_mahasiswa' => $mahasiswa1->id, 'id_kelas_kuliah' => $kelas1->id],
            ['id_bap' => $bap1->id, 'waktu_hadir' => now(), 'latitude' => -6.917464, 'longitude' => 107.619123, 'status' => 'Hadir', 'device_fingerprint' => hash('sha256', '127.0.0.1|WebPortal'), 'verified_at' => now()]
        );

        $assignment1 = Assignment::firstOrCreate(
            ['id_kelas_kuliah' => $kelas1->id, 'judul' => 'Tugas 1: Analisis Arsitektur Microservices'],
            [
                'target_kelas_ids' => [$kelas1->id],
                'deskripsi' => 'Buat dokumen analisis arsitektur high-concurrency dengan implementasi pessimistic locking dan redis cache idempotency.',
                'komponen_penilaian' => 'TUGAS',
                'bobot_persen' => 20.0,
                'deadline_at' => now()->addDays(7),
                'allow_late_submission' => true,
                'late_grace_minutes' => 60,
                'is_published' => true,
            ]
        );

        Submission::firstOrCreate(
            ['id_assignment' => $assignment1->id, 'id_mahasiswa' => $mahasiswa1->id],
            [
                'id_siswa' => $siswa1->id,
                'file_path' => 'submissions/tugas1_fauzan.pdf',
                'original_filename' => 'Tugas1_Arsitektur_Fauzan.pdf',
                'file_mime' => 'application/pdf',
                'file_size' => 1024500,
                'submitted_at' => now()->subHours(2),
                'submission_microtime' => microtime(true),
                'submission_token' => 'SUB-TOK-' . strtoupper(substr(md5(uniqid()), 0, 10)),
                'hash_receipt' => hash('sha256', 'SUB-FAUZAN-TUGAS1'),
                'device_fingerprint' => hash('sha256', '127.0.0.1|Chrome'),
                'client_ip' => '127.0.0.1',
                'is_late' => false,
                'nilai' => 92.5,
                'feedback' => 'Analisis sangat tajam, implementasi locking dan idempotency dijelaskan secara runtut.',
                'graded_at' => now(),
                'graded_by_dosen' => $dosen1->id,
            ]
        );

        // =========================================================================
        // 8. USER ACCOUNTS (Dedicated Enterprise Logins & Legacy Aliases)
        // =========================================================================
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@siakad.ac.id'],
            ['name' => 'Administrator BAAK', 'password' => Hash::make('password123'), 'role' => 'admin', 'is_active' => true]
        );

        User::updateOrCreate(
            ['email' => 'dosen@siakad.ac.id'],
            ['name' => 'Dr. Budi Santoso, M.Kom.', 'password' => Hash::make('password123'), 'role' => 'guru', 'id_guru' => $guru1->id, 'id_dosen' => $dosen1->id, 'is_active' => true]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@siakad.ac.id'],
            ['name' => 'Muhammad Fauzan', 'password' => Hash::make('password123'), 'role' => 'siswa', 'id_siswa' => $siswa1->id, 'id_mahasiswa' => $mahasiswa1->id, 'is_active' => true]
        );

        User::updateOrCreate(
            ['email' => 'keuangan@siakad.ac.id'],
            ['name' => 'Petugas Loket Keuangan BAAK', 'password' => Hash::make('password123'), 'role' => 'petugas', 'is_active' => true]
        );

        // Legacy Test Support Credentials
        User::updateOrCreate(['email' => 'superadmin@sekolah.id'], ['name' => 'Super Administrator', 'password' => Hash::make('password123'), 'role' => 'superadmin', 'is_active' => true]);
        User::updateOrCreate(['email' => 'admin@sekolah.id'], ['name' => 'Administrator BAAK', 'password' => Hash::make('password123'), 'role' => 'admin', 'is_active' => true]);
        User::updateOrCreate(['email' => 'petugas@sekolah.id'], ['name' => 'Petugas Loket Keuangan', 'password' => Hash::make('password123'), 'role' => 'petugas', 'is_active' => true]);
        User::updateOrCreate(['email' => 'guru@sekolah.id'], ['name' => 'Dr. Budi Santoso, M.Kom.', 'password' => Hash::make('password123'), 'role' => 'guru', 'id_guru' => $guru1->id, 'id_dosen' => $dosen1->id, 'is_active' => true]);
        User::updateOrCreate(['email' => 'siswa@sekolah.id'], ['name' => 'Muhammad Fauzan', 'password' => Hash::make('password123'), 'role' => 'siswa', 'id_siswa' => $siswa1->id, 'id_mahasiswa' => $mahasiswa1->id, 'is_active' => true]);
        User::updateOrCreate(['email' => 'superadmin@univ.ac.id'], ['name' => 'Super Administrator SIAKAD', 'password' => Hash::make('password123'), 'role' => 'superadmin', 'is_active' => true]);
        User::updateOrCreate(['email' => 'baak@univ.ac.id'], ['name' => 'BAAK Biro Administrasi Akademik', 'password' => Hash::make('password123'), 'role' => 'admin', 'is_active' => true]);
        User::updateOrCreate(['email' => 'dosen@univ.ac.id'], ['name' => 'Dr. Budi Santoso, M.Kom.', 'password' => Hash::make('password123'), 'role' => 'guru', 'id_guru' => $guru1->id, 'id_dosen' => $dosen1->id, 'is_active' => true]);
        User::updateOrCreate(['email' => 'mahasiswa@univ.ac.id'], ['name' => 'Muhammad Fauzan', 'password' => Hash::make('password123'), 'role' => 'siswa', 'id_siswa' => $siswa1->id, 'id_mahasiswa' => $mahasiswa1->id, 'is_active' => true]);

        // Pembayaran Receipt Record
        if ($tagihan1) {
            PembayaranUkt::firstOrCreate(
                ['nomor_kuitansi' => 'KW-20251-00089'],
                [
                    'id_user' => $adminUser->id,
                    'id_tagihan_ukt' => $tagihan1->id,
                    'id_mahasiswa' => $mahasiswa1->id,
                    'id_ukt' => $uktIF_3->id,
                    'nomor_transaksi_bank' => 'BNI-H2H-20250901-098231',
                    'jumlah_bayar' => 5750000,
                    'semester_dibayar' => 'Ganjil',
                    'tahun_dibayar' => '2025',
                    'kode_bank' => 'BNI',
                    'channel_bayar' => 'Virtual Account BNI Direct',
                    'status_transaksi' => 'SUCCESS',
                    'tgl_bayar' => now(),
                ]
            );

            Pembayaran::firstOrCreate(
                ['id_siswa' => $siswa1->id, 'bulan_dibayar' => 'September'],
                [
                    'id_petugas' => $adminUser->id,
                    'tgl_bayar' => now()->toDateString(),
                    'tahun_dibayar' => '2025',
                    'id_spp' => $spp2025->id,
                    'jumlah_bayar' => 300000,
                ]
            );
        }
    }
}
