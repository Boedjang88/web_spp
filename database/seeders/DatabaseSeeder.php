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
use App\Models\TagihanUkt;
use App\Models\TahunAkademik;
use App\Models\Ukt;
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
        // ==========================================
        // 1. LEGACY SCHOOL SEEDING (Backward Compatibility)
        // ==========================================
        $spp2024 = Spp::firstOrCreate(['tahun' => 2024], ['nominal' => 250000]);
        $spp2025 = Spp::firstOrCreate(['tahun' => 2025], ['nominal' => 300000]);
        $spp2026 = Spp::firstOrCreate(['tahun' => 2026], ['nominal' => 350000]);

        $rpl1 = Kelas::firstOrCreate(['nama_kelas' => 'XII RPL 1'], ['kompetensi_keahlian' => 'Rekayasa Perangkat Lunak']);
        $rpl2 = Kelas::firstOrCreate(['nama_kelas' => 'XII RPL 2'], ['kompetensi_keahlian' => 'Rekayasa Perangkat Lunak']);
        $tkj1 = Kelas::firstOrCreate(['nama_kelas' => 'XII TKJ 1'], ['kompetensi_keahlian' => 'Teknik Komputer dan Jaringan']);
        $dkv1 = Kelas::firstOrCreate(['nama_kelas' => 'XII DKV 1'], ['kompetensi_keahlian' => 'Desain Komunikasi Visual']);

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
                'nama_guru' => 'Sri Wahyuni, M.Pd.',
                'jenis_kelamin' => 'P',
                'no_telp' => '081234567892',
                'email' => 'sri.wahyuni@univ.ac.id',
                'alamat' => 'Jl. Setiabudi No. 40, Bandung',
            ]
        );

        $guru3 = Guru::firstOrCreate(
            ['nip' => '199012052018011003'],
            [
                'nama_guru' => 'Hendra Pratama, S.Kom., M.T.',
                'jenis_kelamin' => 'L',
                'no_telp' => '081234567893',
                'email' => 'hendra.pratama@univ.ac.id',
                'alamat' => 'Jl. Riau No. 100, Bandung',
            ]
        );

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

        JadwalPelajaran::firstOrCreate(
            ['id_kelas' => $rpl1->id, 'id_mapel' => $mapelRPL->id, 'hari' => 'Senin'],
            [
                'id_guru' => $guru1->id,
                'jam_mulai' => '07:30',
                'jam_selesai' => '09:30',
                'ruangan' => 'Lab RPL 1',
            ]
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

        // ==========================================
        // 2. UNIVERSITY STRUCTURE SEEDING
        // ==========================================
        $fakultas = Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FTI'],
            [
                'nama_fakultas' => 'Fakultas Teknologi Informasi',
                'nama_fakultas_en' => 'Faculty of Information Technology',
                'dekan' => 'Prof. Dr. Ir. H. Ahmad Dahlan, M.Sc.',
                'nip_dekan' => '197005121995031001',
            ]
        );

        $prodi = ProgramStudi::firstOrCreate(
            ['kode_prodi' => '55201'],
            [
                'id_fakultas' => $fakultas->id,
                'nama_prodi' => 'Teknik Informatika',
                'nama_prodi_en' => 'Informatics Engineering',
                'jenjang' => 'S1',
                'akreditasi' => 'Unggul',
                'kaprodi' => 'Dr. Budi Santoso, M.Kom.',
                'nip_kaprodi' => '198501152010011002',
            ]
        );

        $kurikulum = Kurikulum::firstOrCreate(
            ['id_prodi' => $prodi->id, 'tahun_mulai' => 2024],
            [
                'nama_kurikulum' => 'Kurikulum MBKM Informatika 2024',
                'total_sks_lulus' => 144,
                'is_active' => true,
            ]
        );

        $taActive = TahunAkademik::firstOrCreate(
            ['kode_tahun' => '20251'],
            [
                'nama_tahun' => '2025/2026 Ganjil',
                'semester' => 'Ganjil',
                'tgl_mulai' => '2025-09-01',
                'tgl_selesai' => '2026-01-31',
                'tgl_krs_mulai' => '2025-08-15',
                'tgl_krs_selesai' => '2025-09-10',
                'is_active' => true,
            ]
        );

        // UKT Groups
        $ukt1 = Ukt::firstOrCreate(['id_prodi' => $prodi->id, 'kelompok_ukt' => 'Kelompok 1', 'tahun' => 2025], ['nominal' => 500000, 'biaya_praktikum' => 0, 'biaya_kemahasiswaan' => 100000, 'deskripsi' => 'UKT Golongan 1 (Afirmasi/KIP-K)']);
        $ukt2 = Ukt::firstOrCreate(['id_prodi' => $prodi->id, 'kelompok_ukt' => 'Kelompok 2', 'tahun' => 2025], ['nominal' => 1000000, 'biaya_praktikum' => 250000, 'biaya_kemahasiswaan' => 150000, 'deskripsi' => 'UKT Golongan 2']);
        $ukt3 = Ukt::firstOrCreate(['id_prodi' => $prodi->id, 'kelompok_ukt' => 'Kelompok 3', 'tahun' => 2025], ['nominal' => 5000000, 'biaya_praktikum' => 500000, 'biaya_kemahasiswaan' => 250000, 'deskripsi' => 'UKT Golongan 3 (Reguler)']);
        $ukt4 = Ukt::firstOrCreate(['id_prodi' => $prodi->id, 'kelompok_ukt' => 'Kelompok 4', 'tahun' => 2025], ['nominal' => 7500000, 'biaya_praktikum' => 750000, 'biaya_kemahasiswaan' => 250000, 'deskripsi' => 'UKT Golongan 4']);
        $ukt5 = Ukt::firstOrCreate(['id_prodi' => $prodi->id, 'kelompok_ukt' => 'Kelompok 5', 'tahun' => 2025], ['nominal' => 10000000, 'biaya_praktikum' => 1000000, 'biaya_kemahasiswaan' => 250000, 'deskripsi' => 'UKT Golongan 5']);

        // Gedung & Ruangan
        $gedung = Gedung::firstOrCreate(
            ['kode_gedung' => 'GD-A'],
            [
                'nama_gedung' => 'Gedung Rektorat & Lab Terpadu',
                'latitude' => -6.917464,
                'longitude' => 107.619123,
            ]
        );

        $ruangan1 = Ruangan::firstOrCreate(
            ['kode_ruangan' => 'LAB-01'],
            [
                'id_gedung' => $gedung->id,
                'nama_ruangan' => 'Laboratorium Rekayasa Perangkat Lunak 1',
                'kapasitas' => 40,
                'jenis_ruangan' => 'Laboratorium',
                'latitude' => -6.917464,
                'longitude' => 107.619123,
                'radius_meter' => 20,
                'is_active' => true,
            ]
        );

        $ruangan2 = Ruangan::firstOrCreate(
            ['kode_ruangan' => 'RT-301'],
            [
                'id_gedung' => $gedung->id,
                'nama_ruangan' => 'Ruang Kuliah Teori 301',
                'kapasitas' => 50,
                'jenis_ruangan' => 'Teori',
                'latitude' => -6.917464,
                'longitude' => 107.619123,
                'radius_meter' => 20,
                'is_active' => true,
            ]
        );

        // Dosen
        $dosen1 = Dosen::firstOrCreate(
            ['nidn' => '0415018501'],
            [
                'id_prodi' => $prodi->id,
                'nama_dosen' => 'Budi Santoso',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'M.Kom.',
                'jenis_kelamin' => 'L',
                'no_telp' => '081234567891',
                'email' => 'budi.santoso@univ.ac.id',
                'alamat' => 'Jl. Dago No. 15, Bandung',
                'is_active' => true,
            ]
        );

        $dosen2 = Dosen::firstOrCreate(
            ['nidn' => '0405129001'],
            [
                'id_prodi' => $prodi->id,
                'nama_dosen' => 'Hendra Pratama',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'M.T.',
                'jenis_kelamin' => 'L',
                'no_telp' => '081234567893',
                'email' => 'hendra.pratama@univ.ac.id',
                'alamat' => 'Jl. Riau No. 100, Bandung',
                'is_active' => true,
            ]
        );

        // Mahasiswa
        $mahasiswa1 = Mahasiswa::firstOrCreate(
            ['nim' => '2301010001'],
            [
                'nisn' => '0051234567',
                'nik' => '3201123456780001',
                'nama' => 'Muhammad Fauzan',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2004-05-14',
                'jenis_kelamin' => 'L',
                'agama' => 'Islam',
                'id_prodi' => $prodi->id,
                'id_dosen_pa' => $dosen1->id,
                'id_ukt' => $ukt3->id,
                'alamat' => 'Jl. Merdeka No. 45, Bandung',
                'rt' => '03',
                'rw' => '05',
                'kelurahan' => 'Babakan Ciamis',
                'kecamatan' => 'Sumur Bandung',
                'kota' => 'Kota Bandung',
                'kode_pos' => '40117',
                'nama_ayah' => 'Ahmad Fauzi',
                'nama_ibu_kandung' => 'Siti Aminah',
                'pekerjaan_ayah' => 'Wiraswasta',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'penghasilan_ortu' => 'Rp 5.000.000 - Rp 10.000.000',
                'asal_sekolah' => 'SMA Negeri 1 Bandung',
                'tahun_lulus_sekolah' => '2023',
                'nomor_ijazah_sekolah' => 'DN-02/MA/13/0012345',
                'no_telp' => '081234567890',
                'email_pribadi' => 'fauzan@student.univ.ac.id',
                'no_hp_wali' => '081234567899',
                'status_kelulusan' => 'Aktif',
                'consent_pdp_at' => now(),
                'consent_pdp_ip' => '127.0.0.1',
            ]
        );

        $mahasiswa2 = Mahasiswa::firstOrCreate(
            ['nim' => '2301010002'],
            [
                'nisn' => '0057654321',
                'nik' => '3201123456780002',
                'nama' => 'Siti Nurhaliza',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2004-08-20',
                'jenis_kelamin' => 'P',
                'agama' => 'Islam',
                'id_prodi' => $prodi->id,
                'id_dosen_pa' => $dosen1->id,
                'id_ukt' => $ukt3->id,
                'alamat' => 'Jl. Pahlawan No. 12, Bandung',
                'nama_ibu_kandung' => 'Nurhayati',
                'no_telp' => '081298765432',
                'status_kelulusan' => 'Aktif',
                'consent_pdp_at' => now(),
                'consent_pdp_ip' => '127.0.0.1',
            ]
        );

        // Mata Kuliah
        $mk1 = MataKuliah::firstOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'id_kurikulum' => $kurikulum->id,
                'nama_mk' => 'Arsitektur Sistem Enterprise',
                'nama_mk_en' => 'Enterprise System Architecture',
                'sks_teori' => 2,
                'sks_praktik' => 1,
                'sks_total' => 3,
                'semester_rekomendasi' => 5,
                'jenis_mk' => 'Wajib Program Studi',
            ]
        );

        $mk2 = MataKuliah::firstOrCreate(
            ['kode_mk' => 'IF302'],
            [
                'id_kurikulum' => $kurikulum->id,
                'nama_mk' => 'Pemrograman Web Lanjut & API Security',
                'nama_mk_en' => 'Advanced Web Programming & API Security',
                'sks_teori' => 1,
                'sks_praktik' => 2,
                'sks_total' => 3,
                'semester_rekomendasi' => 5,
                'jenis_mk' => 'Wajib Program Studi',
            ]
        );

        $mk3 = MataKuliah::firstOrCreate(
            ['kode_mk' => 'IF303'],
            [
                'id_kurikulum' => $kurikulum->id,
                'nama_mk' => 'Basis Data Skala Besar & NoSQL',
                'nama_mk_en' => 'Big Data & NoSQL Databases',
                'sks_teori' => 2,
                'sks_praktik' => 1,
                'sks_total' => 3,
                'semester_rekomendasi' => 5,
                'jenis_mk' => 'Wajib Program Studi',
            ]
        );

        // Kelas Kuliah
        $kelas1 = KelasKuliah::firstOrCreate(
            ['id_mk' => $mk1->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'IF301-A'],
            [
                'id_dosen' => $dosen1->id,
                'ruang' => 'Lab RPL 1',
                'hari' => 'Senin',
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:30',
                'kuota_maksimal' => 40,
                'total_terisi' => 1,
            ]
        );

        $kelas2 = KelasKuliah::firstOrCreate(
            ['id_mk' => $mk2->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'IF302-A'],
            [
                'id_dosen' => $dosen2->id,
                'ruang' => 'Lab RPL 1',
                'hari' => 'Selasa',
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:30',
                'kuota_maksimal' => 40,
                'total_terisi' => 1,
            ]
        );

        $kelas3 = KelasKuliah::firstOrCreate(
            ['id_mk' => $mk3->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'IF303-A'],
            [
                'id_dosen' => $dosen1->id,
                'ruang' => 'Ruang Teori 301',
                'hari' => 'Rabu',
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:30',
                'kuota_maksimal' => 40,
                'total_terisi' => 1,
            ]
        );

        // Jadwal Kuliah
        JadwalKuliah::firstOrCreate(
            ['id_kelas_kuliah' => $kelas1->id, 'hari' => 'Senin'],
            [
                'id_ruangan' => $ruangan1->id,
                'id_guru' => $guru1->id,
                'jam_mulai' => '08:00',
                'jam_selesai' => '10:30',
            ]
        );

        JadwalKuliah::firstOrCreate(
            ['id_kelas_kuliah' => $kelas2->id, 'hari' => 'Selasa'],
            [
                'id_ruangan' => $ruangan1->id,
                'id_guru' => $guru3->id,
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:30',
            ]
        );

        JadwalKuliah::firstOrCreate(
            ['id_kelas_kuliah' => $kelas3->id, 'hari' => 'Rabu'],
            [
                'id_ruangan' => $ruangan2->id,
                'id_guru' => $guru1->id,
                'jam_mulai' => '13:00',
                'jam_selesai' => '15:30',
            ]
        );

        // Financial Clearance
        FinancialClearance::firstOrCreate(
            ['id_siswa' => $mahasiswa1->id, 'id_tahun_akademik' => $taActive->id],
            [
                'is_krs_unlocked' => true,
                'is_uts_unlocked' => true,
                'is_uas_unlocked' => true,
                'unlocked_at' => now(),
                'unlocked_by_channel' => 'H2H_BANK_BNI',
            ]
        );

        // KRS Header & Details
        $krs1 = Krs::firstOrCreate(
            ['id_siswa' => $mahasiswa1->id, 'id_tahun_akademik' => $taActive->id],
            [
                'max_sks_diizinkan' => 24,
                'total_sks_diambil' => 9,
                'status_krs' => 'Disetujui',
                'catatan_pembimbing' => 'Rencana studi telah disetujui. Semangat menempuh semester baru.',
            ]
        );

        KrsDetail::firstOrCreate(['id_krs' => $krs1->id, 'id_kelas_kuliah' => $kelas1->id], ['status_ambil' => 'Baru']);
        KrsDetail::firstOrCreate(['id_krs' => $krs1->id, 'id_kelas_kuliah' => $kelas2->id], ['status_ambil' => 'Baru']);
        KrsDetail::firstOrCreate(['id_krs' => $krs1->id, 'id_kelas_kuliah' => $kelas3->id], ['status_ambil' => 'Baru']);

        // BAP Perkuliahan (Active Session for Today)
        $bap1 = BapPerkuliahan::firstOrCreate(
            ['id_kelas_kuliah' => $kelas1->id, 'pertemuan_ke' => 1],
            [
                'id_guru' => $guru1->id,
                'id_ruangan' => $ruangan1->id,
                'tanggal_pelaksanaan' => now()->toDateString(),
                'jam_mulai_real' => '08:00',
                'jam_selesai_real' => '10:30',
                'materi_pembahasan' => 'Pengenalan Arsitektur Domain-Driven Design & High Concurrency Locking',
                'catatan_dosen' => 'Mahasiswa hadir dengan antusias, pengenalan sistem SIAKAD Enterprise.',
                'total_mahasiswa_hadir' => 1,
                'total_mahasiswa_absen' => 0,
                'status_verifikasi' => 'Diverifikasi BAAK',
                'digital_signature_hash' => hash('sha256', 'BAP-IF301-' . now()->toDateString()),
            ]
        );

        // Presensi Mahasiswa
        PresensiMahasiswa::firstOrCreate(
            ['id_mahasiswa' => $mahasiswa1->id, 'id_kelas_kuliah' => $kelas1->id],
            [
                'id_bap' => $bap1->id,
                'waktu_hadir' => now(),
                'latitude' => -6.917464,
                'longitude' => 107.619123,
                'status' => 'Hadir',
                'device_fingerprint' => hash('sha256', '127.0.0.1|WebPortal'),
                'verified_at' => now(),
            ]
        );

        // LMS Assignments
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

        $assignment2 = Assignment::firstOrCreate(
            ['id_kelas_kuliah' => $kelas2->id, 'judul' => 'Tugas 2: Secure File Upload & Hash Receipts'],
            [
                'target_kelas_ids' => [$kelas2->id],
                'deskripsi' => 'Implementasikan upload gateway dengan binary header validation dan penerbitan SHA-256 digital receipt.',
                'komponen_penilaian' => 'TUGAS',
                'bobot_persen' => 25.0,
                'deadline_at' => now()->addDays(10),
                'allow_late_submission' => false,
                'is_published' => true,
            ]
        );

        // Bank Mitra & UKT Invoicing
        $bankBni = BankMitra::firstOrCreate(
            ['kode_bank' => 'BNI'],
            [
                'nama_bank' => 'PT Bank Negara Indonesia (Persero) Tbk',
                'prefix_va' => '988',
                'secret_key' => 'bni_sec_key_production_2026',
                'webhook_url' => 'https://siakad.univ.ac.id/api/v1/h2h/webhook/bni',
                'is_active' => true,
            ]
        );

        $tagihan1 = TagihanUkt::firstOrCreate(
            ['id_mahasiswa' => $mahasiswa1->id, 'id_tahun_akademik' => $taActive->id],
            [
                'id_bank_mitra' => $bankBni->id,
                'nomor_va' => '9882301010001001',
                'nomor_invoice' => 'INV-UKT-20251-0001',
                'biaya_ukt' => 5000000,
                'biaya_praktikum' => 500000,
                'biaya_kemahasiswaan' => 250000,
                'total_tagihan' => 5750000,
                'total_potongan_beasiswa' => 0,
                'total_harus_bayar' => 5750000,
                'total_sudah_bayar' => 5750000,
                'status_pembayaran' => 'Lunas',
                'tgl_jatuh_tempo' => now()->addMonth(),
                'tgl_lunas' => now(),
            ]
        );

        // ==========================================
        // 3. USER ACCOUNTS (4-Tier RBAC + Dual Identifiers)
        // ==========================================
        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@sekolah.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@sekolah.id'],
            [
                'name' => 'Administrator BAAK',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $petugasUser = User::updateOrCreate(
            ['email' => 'petugas@sekolah.id'],
            [
                'name' => 'Petugas Loket Keuangan',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'is_active' => true,
            ]
        );

        $guruUser = User::updateOrCreate(
            ['email' => 'guru@sekolah.id'],
            [
                'name' => 'Dr. Budi Santoso, M.Kom.',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'id_guru' => $guru1->id,
                'id_dosen' => $dosen1->id,
                'is_active' => true,
            ]
        );

        $siswaUser = User::updateOrCreate(
            ['email' => 'siswa@sekolah.id'],
            [
                'name' => 'Muhammad Fauzan',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'id_siswa' => $siswa1->id,
                'id_mahasiswa' => $mahasiswa1->id,
                'is_active' => true,
            ]
        );

        // University Dedicated Logins
        User::updateOrCreate(
            ['email' => 'superadmin@univ.ac.id'],
            [
                'name' => 'Super Administrator SIAKAD',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'baak@univ.ac.id'],
            [
                'name' => 'BAAK Biro Administrasi Akademik',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dosen@univ.ac.id'],
            [
                'name' => 'Dr. Budi Santoso, M.Kom.',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'id_guru' => $guru1->id,
                'id_dosen' => $dosen1->id,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@univ.ac.id'],
            [
                'name' => 'Muhammad Fauzan',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'id_siswa' => $siswa1->id,
                'id_mahasiswa' => $mahasiswa1->id,
                'is_active' => true,
            ]
        );

        PembayaranUkt::firstOrCreate(
            ['nomor_kuitansi' => 'KW-20251-00089'],
            [
                'id_user' => $adminUser->id,
                'id_tagihan_ukt' => $tagihan1->id,
                'id_mahasiswa' => $mahasiswa1->id,
                'id_ukt' => $ukt3->id,
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
    }
}
