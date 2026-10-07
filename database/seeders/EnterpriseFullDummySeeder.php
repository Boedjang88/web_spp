<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\BankMitra;
use App\Models\BapPerkuliahan;
use App\Models\Beasiswa;
use App\Models\CourseMaterial;
use App\Models\Dosen;
use App\Models\EdomEvaluasi;
use App\Models\EdomEvaluasiItem;
use App\Models\EdomPertanyaan;
use App\Models\EmployerFeedback;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Fakultas;
use App\Models\Gedung;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\KknRegistrasi;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Kurikulum;
use App\Models\LogbookBimbingan;
use App\Models\Mahasiswa;
use App\Models\Mapel;
use App\Models\MataKuliah;
use App\Models\MbkmKonversi;
use App\Models\MbkmKonversiDetail;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\PembayaranUkt;
use App\Models\PendaftaranBeasiswa;
use App\Models\Presensi;
use App\Models\PresensiMahasiswa;
use App\Models\ProgramStudi;
use App\Models\Ruangan;
use App\Models\SidangSkripsi;
use App\Models\Siswa;
use App\Models\SkpiAktivitas;
use App\Models\Spp;
use App\Models\Submission;
use App\Models\SuratAkademik;
use App\Models\TagihanUkt;
use App\Models\TahunAkademik;
use App\Models\TracerStudy;
use App\Models\TugasAkhir;
use App\Models\Ukt;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EnterpriseFullDummySeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // 1. TAHUN AKADEMIK & SPP & BANK MITRA
        // =========================================================================
        $taActive = TahunAkademik::firstOrCreate(
            ['kode_tahun' => '20251'],
            [
                'nama_tahun' => '2025/2026 Ganjil',
                'semester' => 'Ganjil',
                'is_active' => true,
                'tgl_krs_mulai' => now()->subMonths(2),
                'tgl_krs_selesai' => now()->addMonth(),
            ]
        );

        $taPast = TahunAkademik::firstOrCreate(
            ['kode_tahun' => '20242'],
            [
                'nama_tahun' => '2024/2025 Genap',
                'semester' => 'Genap',
                'is_active' => false,
                'tgl_krs_mulai' => now()->subYear(),
                'tgl_krs_selesai' => now()->subMonths(8),
            ]
        );

        $sppActive = Spp::firstOrCreate(['tahun' => 2026], ['nominal' => 3500000]);

        $banksData = [
            ['kode' => 'BNI', 'nama' => 'Bank Negara Indonesia (BNI) H2H Direct', 'prefix' => '8801', 'secret' => 'SECRET-BNI-987'],
            ['kode' => 'MANDIRI', 'nama' => 'Bank Mandiri Multi Payment H2H', 'prefix' => '8802', 'secret' => 'SECRET-MANDIRI-654'],
            ['kode' => 'BRI', 'nama' => 'Bank Rakyat Indonesia (BRI) BRIVA Direct', 'prefix' => '8803', 'secret' => 'SECRET-BRI-321'],
            ['kode' => 'BSI', 'nama' => 'Bank Syariah Indonesia (BSI) Syariah Payment', 'prefix' => '8804', 'secret' => 'SECRET-BSI-147'],
        ];

        $bankMitras = [];
        foreach ($banksData as $b) {
            $bankMitras[] = BankMitra::firstOrCreate(
                ['kode_bank' => $b['kode']],
                ['nama_bank' => $b['nama'], 'prefix_va' => $b['prefix'], 'secret_key' => $b['secret'], 'is_active' => true]
            );
        }

        // =========================================================================
        // 2. FAKULTAS, PROGRAM STUDI, & UKT GOLONGAN
        // =========================================================================
        $fakultasFTI = Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FTI'],
            ['nama_fakultas' => 'Fakultas Teknologi Informasi & Bisnis Digital', 'dekan' => 'Prof. Dr. Ir. Agung Perkasa, M.Sc.']
        );

        $prodisData = [
            ['kode' => 'IF', 'nama' => 'Teknik Informatika', 'jenjang' => 'S1'],
            ['kode' => 'SI', 'nama' => 'Sistem Informasi', 'jenjang' => 'S1'],
            ['kode' => 'BD', 'nama' => 'Bisnis Digital', 'jenjang' => 'S1'],
            ['kode' => 'TK', 'nama' => 'Teknik Komputer', 'jenjang' => 'D3'],
        ];

        $prodis = [];
        foreach ($prodisData as $p) {
            $prodis[] = ProgramStudi::firstOrCreate(
                ['kode_prodi' => $p['kode']],
                ['id_fakultas' => $fakultasFTI->id, 'nama_prodi' => $p['nama'], 'jenjang' => $p['jenjang'], 'akreditasi' => 'Unggul']
            );
        }

        $uktsData = [
            ['gol' => 'UKT 1', 'nominal' => 1000000],
            ['gol' => 'UKT 2', 'nominal' => 2500000],
            ['gol' => 'UKT 3', 'nominal' => 4500000],
            ['gol' => 'UKT 4', 'nominal' => 6000000],
            ['gol' => 'UKT 5', 'nominal' => 7500000],
        ];

        $ukts = [];
        foreach ($uktsData as $u) {
            $ukts[] = Ukt::firstOrCreate(
                ['kelompok_ukt' => $u['gol'], 'tahun' => 2026],
                ['nominal' => $u['nominal'], 'deskripsi' => 'Standar Biaya Kuliah ' . $u['gol']]
            );
        }

        // =========================================================================
        // 3. GEDUNG, RUANGAN & FASILITAS KAMPUS
        // =========================================================================
        $gedungA = Gedung::firstOrCreate(['kode_gedung' => 'GD-A'], ['nama_gedung' => 'Gedung Utama Rektorat & BAAK']);
        $gedungB = Gedung::firstOrCreate(['kode_gedung' => 'GD-B'], ['nama_gedung' => 'Gedung Laboratorium Integrated Cloud & AI']);

        $ruanganList = [
            ['kode' => 'LAB-01', 'nama' => 'Lab Software Engineering & Web API', 'gedung' => $gedungB->id, 'kap' => 40],
            ['kode' => 'LAB-02', 'nama' => 'Lab Artificial Intelligence & Data Science', 'gedung' => $gedungB->id, 'kap' => 40],
            ['kode' => 'LAB-03', 'nama' => 'Lab Network Operating Center & Security', 'gedung' => $gedungB->id, 'kap' => 35],
            ['kode' => 'TEORI-A101', 'nama' => 'Ruang Kuliah Teori A101 Interactive', 'gedung' => $gedungA->id, 'kap' => 50],
            ['kode' => 'AUD-MAIN', 'nama' => 'Auditorium Utama Kampus Enterprise', 'gedung' => $gedungA->id, 'kap' => 300],
        ];

        $ruangans = [];
        foreach ($ruanganList as $r) {
            $ruangans[] = Ruangan::firstOrCreate(
                ['kode_ruangan' => $r['kode']],
                ['id_gedung' => $r['gedung'], 'nama_ruangan' => $r['nama'], 'kapasitas' => $r['kap']]
            );
        }

        $facilityItems = [
            ['kode' => 'FAC-LAB-AI', 'nama' => 'Lab Workstation High-Performance AI GPU', 'kat' => 'Ruang Lab', 'ruang' => $ruangans[1]->id, 'kap' => 40, 'desk' => 'Workstation NVIDIA RTX 4090 untuk riset AI & Deep Learning'],
            ['kode' => 'FAC-LAB-WEB', 'nama' => 'Lab Web Enterprise & Microservices', 'kat' => 'Ruang Lab', 'ruang' => $ruangans[0]->id, 'kap' => 40, 'desk' => 'Dual Monitor Workstation untuk Pemrograman Fullstack API'],
            ['kode' => 'FAC-AUDITORIUM', 'nama' => 'Auditorium Utama Sound System & Stage', 'kat' => 'Auditorium', 'ruang' => $ruangans[4]->id, 'kap' => 300, 'desk' => 'Fasilitas Seminar Nasional, Yudisium & Kuliah Umum'],
            ['kode' => 'FAC-MEET-CONF', 'nama' => 'Ruang Conference & Video Broadcast B201', 'kat' => 'Ruang Teori', 'ruang' => $ruangans[3]->id, 'kap' => 50, 'desk' => 'Proyektor Laser HD, Microphones & Smart Whiteboard'],
            ['kode' => 'FAC-NOC-RACK', 'nama' => 'Rack Server Router & Cisco Switches Enterprise', 'kat' => 'Laboratorium', 'ruang' => $ruangans[2]->id, 'kap' => 35, 'desk' => 'Perangkat Praktikum Jaringan Komputer & Cyber Security'],
        ];

        $facilities = [];
        foreach ($facilityItems as $fi) {
            $facilities[] = Facility::firstOrCreate(
                ['kode_fasilitas' => $fi['kode']],
                [
                    'nama_fasilitas' => $fi['nama'],
                    'kategori' => $fi['kat'],
                    'id_ruangan' => $fi['ruang'],
                    'kapasitas' => $fi['kap'],
                    'status_fasilitas' => 'Tersedia',
                    'deskripsi' => $fi['desk'],
                ]
            );
        }

        // =========================================================================
        // 4. DOSEN / GURU (10 Records)
        // =========================================================================
        $dosenList = [
            ['nip' => '198501152010011002', 'nama' => 'Dr. Budi Santoso, M.Kom.', 'email' => 'budi.santoso@univ.ac.id', 'gender' => 'L', 'nidn' => '0415018501', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'M.Kom.'],
            ['nip' => '198807222015022001', 'nama' => 'Dr. Sri Wahyuni, M.Pd.', 'email' => 'sri.wahyuni@univ.ac.id', 'gender' => 'P', 'nidn' => '0422078802', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'M.Pd.'],
            ['nip' => '199012052018011003', 'nama' => 'Dr. Hendra Pratama, S.Kom., M.T.', 'email' => 'hendra.pratama@univ.ac.id', 'gender' => 'L', 'nidn' => '0405129003', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.Kom., M.T.'],
            ['nip' => '199203112019032004', 'nama' => 'Prof. Dr. Rina Supriyati, M.T.', 'email' => 'rina.supriyati@univ.ac.id', 'gender' => 'P', 'nidn' => '0411039204', 'gelar_depan' => 'Prof. Dr.', 'gelar_belakang' => 'M.T.'],
            ['nip' => '198906152016011005', 'nama' => 'Dr. Ir. Agung Perkasa, M.Sc.', 'email' => 'agung.perkasa@univ.ac.id', 'gender' => 'L', 'nidn' => '0415068905', 'gelar_depan' => 'Dr. Ir.', 'gelar_belakang' => 'M.Sc.'],
            ['nip' => '199104182017022006', 'nama' => 'Nabila Salsabila, M.T.', 'email' => 'nabila.salsabila@univ.ac.id', 'gender' => 'P', 'nidn' => '0418049106', 'gelar_depan' => '', 'gelar_belakang' => 'M.T.'],
            ['nip' => '198709282014011007', 'nama' => 'Fajar Sidik, M.Kom.', 'email' => 'fajar.sidik@univ.ac.id', 'gender' => 'L', 'nidn' => '0428098707', 'gelar_depan' => '', 'gelar_belakang' => 'M.Kom.'],
            ['nip' => '199311022020012008', 'nama' => 'Dewi Lestari, S.T., M.Sc.', 'email' => 'dewi.lestari@univ.ac.id', 'gender' => 'P', 'nidn' => '0402119308', 'gelar_depan' => '', 'gelar_belakang' => 'S.T., M.Sc.'],
        ];

        $gurus = [];
        $dosens = [];
        foreach ($dosenList as $idx => $d) {
            $guru = Guru::firstOrCreate(
                ['nip' => $d['nip']],
                [
                    'nama_guru' => $d['nama'],
                    'jenis_kelamin' => $d['gender'],
                    'no_telp' => '0812' . rand(10000000, 99999999),
                    'email' => $d['email'],
                    'alamat' => 'Kampus SIAKAD Enterprise, Bandung',
                ]
            );
            $gurus[] = $guru;

            $dosen = Dosen::firstOrCreate(
                ['nidn' => $d['nidn']],
                [
                    'nip' => $d['nip'],
                    'nama_dosen' => $d['nama'],
                    'gelar_depan' => $d['gelar_depan'],
                    'gelar_belakang' => $d['gelar_belakang'],
                    'jenis_kelamin' => $d['gender'],
                    'id_prodi' => $prodis[$idx % count($prodis)]->id,
                    'email' => $d['email'],
                    'no_telp' => '0812' . rand(10000000, 99999999),
                    'alamat' => 'Jl. Dago No. ' . ($idx * 10 + 5) . ', Bandung',
                ]
            );
            $dosens[] = $dosen;
        }

        // =========================================================================
        // 5. KELAS & MATA KULIAH & KELAS KULIAH
        // =========================================================================
        $kelasNames = ['IF-1A', 'IF-1B', 'IF-3A', 'IF-3B', 'SI-2A', 'SI-4A', 'BD-1A', 'BD-3A'];
        $kelases = [];
        foreach ($kelasNames as $kName) {
            $prodiName = str_starts_with($kName, 'IF') ? 'Teknik Informatika' : (str_starts_with($kName, 'SI') ? 'Sistem Informasi' : 'Bisnis Digital');
            $kelases[] = Kelas::firstOrCreate(['nama_kelas' => $kName], ['kompetensi_keahlian' => $prodiName]);
        }

        $kurikulums = [];
        foreach ($prodis as $p) {
            $kurikulums[] = Kurikulum::firstOrCreate(
                ['id_prodi' => $p->id, 'nama_kurikulum' => 'Kurikulum Merdeka OBE ' . $p->nama_prodi],
                ['tahun_mulai' => 2024, 'total_sks_lulus' => 144, 'is_active' => true]
            );
        }

        $mkList = [
            ['kode' => 'IF-101', 'nama' => 'Algoritma & Pemrograman Dasar', 'sks' => 3, 'sem' => 1, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'IF-102', 'nama' => 'Pemrograman Web Enterprise & API', 'sks' => 3, 'sem' => 3, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'IF-201', 'nama' => 'Basis Data Skala Besar & NoSQL', 'sks' => 3, 'sem' => 3, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'IF-202', 'nama' => 'Arsitektur Sistem Informasi Cloud', 'sks' => 3, 'sem' => 5, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'IF-301', 'nama' => 'Kecerdasan Buatan & Machine Learning', 'sks' => 3, 'sem' => 5, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'IF-302', 'nama' => 'Rekayasa Perangkat Lunak Enterprise', 'sks' => 3, 'sem' => 5, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'SI-101', 'nama' => 'Analisis & Perancangan Sistem Enterprise', 'sks' => 3, 'sem' => 3, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'SI-102', 'nama' => 'Manajemen Proyek TI & Tata Kelola', 'sks' => 3, 'sem' => 3, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'SI-201', 'nama' => 'E-Business & Arsitektur Enterprise', 'sks' => 3, 'sem' => 5, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'SI-202', 'nama' => 'Audit Sistem Informasi & GRC', 'sks' => 3, 'sem' => 5, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'BD-101', 'nama' => 'Manajemen Bisnis Digital & Startup', 'sks' => 3, 'sem' => 1, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'BD-102', 'nama' => 'Digital Marketing & Growth Hacking', 'sks' => 3, 'sem' => 3, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'BD-201', 'nama' => 'Analisis Pasar & Big Data Business', 'sks' => 3, 'sem' => 5, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'TK-101', 'nama' => 'Keamanan Jaringan & Cyber Security', 'sks' => 3, 'sem' => 3, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'TK-102', 'nama' => 'System Administration & Linux Server', 'sks' => 3, 'sem' => 3, 'jenis' => 'Wajib Program Studi'],
            ['kode' => 'TK-201', 'nama' => 'Cloud Infrastructure & DevOps CI/CD', 'sks' => 3, 'sem' => 5, 'jenis' => 'Wajib Program Studi'],
        ];

        $mataKuliahs = [];
        $mapels = [];
        foreach ($mkList as $idx => $mk) {
            $assignedKur = $kurikulums[$idx % count($kurikulums)];
            $mKuliah = MataKuliah::firstOrCreate(
                ['kode_mk' => $mk['kode']],
                [
                    'id_kurikulum' => $assignedKur->id,
                    'nama_mk' => $mk['nama'],
                    'sks_teori' => 2,
                    'sks_praktik' => 1,
                    'sks_total' => $mk['sks'],
                    'semester_rekomendasi' => $mk['sem'],
                    'jenis_mk' => $mk['jenis'],
                ]
            );
            $mataKuliahs[] = $mKuliah;

            $mapels[] = Mapel::firstOrCreate(
                ['kode_mapel' => $mk['kode']],
                ['nama_mapel' => $mk['nama'], 'kelompok' => 'Kejuruan', 'kkm' => 75]
            );
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $timeSlots = [
            ['mulai' => '08:00', 'selesai' => '10:30'],
            ['mulai' => '10:45', 'selesai' => '13:15'],
            ['mulai' => '13:30', 'selesai' => '16:00'],
            ['mulai' => '16:15', 'selesai' => '18:45'],
        ];

        $kelasKuliahs = [];

        foreach ($mataKuliahs as $mIdx => $mk) {
            // Assign 2-3 courses per dosen (cycle through dosens)
            $assignedDosen = $dosens[$mIdx % count($dosens)];
            $assignedRuang = $ruangans[$mIdx % count($ruangans)];
            $slot = $timeSlots[$mIdx % 4];
            $hari = $hariList[$mIdx % 5];

            $kKuliah = KelasKuliah::firstOrCreate(
                ['id_mk' => $mk->id, 'id_tahun_akademik' => $taActive->id, 'nama_kelas' => 'Kelas A'],
                [
                    'id_dosen' => $assignedDosen->id,
                    'kuota_maksimal' => 40,
                    'total_terisi' => 0,
                    'ruang' => $assignedRuang->nama_ruangan,
                    'hari' => $hari,
                    'jam_mulai' => $slot['mulai'] . ':00',
                    'jam_selesai' => $slot['selesai'] . ':00',
                ]
            );
            $kelasKuliahs[] = $kKuliah;

            // Generate 2-4 daily sessions per class across Monday-Friday
            foreach ($kelases as $kIdx => $kls) {
                JadwalPelajaran::firstOrCreate(
                    ['id_kelas' => $kls->id, 'id_mapel' => $mapels[$mIdx]->id, 'hari' => $hariList[($kIdx + $mIdx) % 5]],
                    [
                        'id_guru' => $gurus[$mIdx % count($gurus)]->id,
                        'jam_mulai' => $timeSlots[($kIdx + $mIdx) % 4]['mulai'],
                        'jam_selesai' => $timeSlots[($kIdx + $mIdx) % 4]['selesai'],
                        'ruangan' => $assignedRuang->nama_ruangan,
                    ]
                );
            }
        }

        // =========================================================================
        // 6. MAHASISWA & SISWA & USER ACCOUNTS (50 Total Students)
        // =========================================================================
        $namaDepan = ['Muhammad', 'Siti', 'Ahmad', 'Anisa', 'Bagus', 'Dwi', 'Fajar', 'Gita', 'Hadi', 'Indah', 'Kevin', 'Lani', 'Mahendra', 'Nabila', 'Oscar', 'Putri', 'Rahmat', 'Rizky', 'Tiar', 'Yusuf', 'Andi', 'Bella', 'Cahyo', 'Dini', 'Eko'];
        $namaBelakang = ['Fauzan', 'Nurhaliza', 'Pratama', 'Rahmawati', 'Setiawan', 'Lestari', 'Nugraha', 'Gutawa', 'Kurniawan', 'Permatasari', 'Sanjaya', 'Wijaya', 'Saputra', 'Putri', 'Lawalata', 'Hidayat', 'Santoso', 'Utami', 'Wibowo', 'Zulkarnain', 'Kusuma', 'Mahendra', 'Pratiwi', 'Subagyo', 'Firmansyah'];

        $siswas = [];
        $mahasiswas = [];
        $usersStudents = [];

        for ($i = 1; $i <= 50; $i++) {
            $fn = $namaDepan[($i - 1) % count($namaDepan)];
            $ln = $namaBelakang[($i - 1) % count($namaBelakang)];
            $fullName = $fn . ' ' . $ln;
            $nim = '10924094' . sprintf('%04d', $i);
            $selectedKelas = $kelases[($i - 1) % count($kelases)];
            $selectedProdi = $prodis[($i - 1) % count($prodis)];
            $selectedDosenPa = $dosens[($i - 1) % count($dosens)];
            $selectedUkt = $ukts[($i - 1) % count($ukts)];

            $userStudent = User::firstOrCreate(
                ['email' => strtolower($fn . '.' . $ln . $i . '@student.ac.id')],
                [
                    'name' => $fullName,
                    'password' => Hash::make('password123'),
                    'role' => 'mahasiswa',
                    'is_active' => true,
                ]
            );
            $usersStudents[] = $userStudent;

            $siswa = Siswa::firstOrCreate(
                ['nisn' => $nim],
                [
                    'nis' => $nim,
                    'nama' => $fullName,
                    'id_kelas' => $selectedKelas->id,
                    'id_spp' => $sppActive->id,
                    'alamat' => 'Jl. Kampus Merdeka No. ' . ($i * 2) . ', Bandung',
                    'no_telp' => '0858' . sprintf('%08d', $i * 11111),
                    'status_kelulusan' => 'Aktif',
                    'consent_pdp_at' => now(),
                    'consent_pdp_ip' => '114.10.114.' . ($i % 250),
                ]
            );
            $siswas[] = $siswa;

            $mahasiswa = Mahasiswa::firstOrCreate(
                ['nim' => $nim],
                [
                    'nisn' => $nim,
                    'nik' => '327501' . sprintf('%010d', $i * 98765),
                    'nama' => $fullName,
                    'id_prodi' => $selectedProdi->id,
                    'id_dosen_pa' => $selectedDosenPa->id,
                    'id_ukt' => $selectedUkt->id,
                    'alamat' => 'Jl. Kampus Merdeka No. ' . ($i * 2) . ', Bandung',
                    'nama_ibu_kandung' => 'Ibu ' . $ln,
                    'no_telp' => '0858' . sprintf('%08d', $i * 11111),
                    'no_hp_wali' => '0813' . sprintf('%08d', $i * 22222),
                    'status_kelulusan' => 'Aktif',
                    'total_skpi_points' => rand(15, 85),
                    'consent_pdp_at' => now(),
                    'consent_pdp_ip' => '114.10.114.' . ($i % 250),
                ]
            );
            $mahasiswas[] = $mahasiswa;

            // Link User account
            $userStudent->update([
                'id_siswa' => $siswa->id,
                'id_mahasiswa' => $mahasiswa->id,
            ]);
        }

        // =========================================================================
        // 7. KRS, KRS DETAIL, & NILAI (All 50 Students Enrolled in Courses)
        // =========================================================================
        $krsList = [];
        $krsDetailList = [];

        foreach ($mahasiswas as $idx => $mhs) {
            $siswaLinked = $siswas[$idx];

            // Create KRS
            $krs = Krs::firstOrCreate(
                ['id_siswa' => $siswaLinked->id, 'id_tahun_akademik' => $taActive->id],
                [
                    'id_dosen_wali' => $gurus[$idx % count($gurus)]->id,
                    'max_sks_diizinkan' => 24,
                    'total_sks_diambil' => 12,
                    'status_krs' => 'Disetujui',
                    'catatan_pembimbing' => 'KRS telah diverifikasi dan disetujui Dosen PA.',
                    'tgl_pengajuan' => now()->subDays(30),
                    'tgl_persetujuan' => now()->subDays(28),
                ]
            );
            $krsList[] = $krs;

            // Enrol into 4 KelasKuliah
            $enrolledKK = array_slice($kelasKuliahs, ($idx % 4), 4);
            foreach ($enrolledKK as $kkIdx => $kk) {
                $kd = KrsDetail::firstOrCreate(
                    ['id_krs' => $krs->id, 'id_kelas_kuliah' => $kk->id],
                    [
                        'status_ambil' => 'Baru',
                        'nilai_tugas' => 90,
                        'nilai_uts' => 88,
                        'nilai_uas' => 92,
                        'nilai_akhir_angka' => 90.0,
                        'nilai_akhir_huruf' => 'A',
                        'bobot_mutu' => 4.00,
                        'is_lulus' => true,
                        'is_published' => true,
                    ]
                );
                $krsDetailList[] = $kd;

                // Increment total_terisi
                $kk->increment('total_terisi');
            }

            // Create Legacy Nilai
            foreach ($mapels as $mIdx => $mpl) {
                $nTugas = rand(75, 95);
                $nUts = rand(70, 92);
                $nUas = rand(75, 98);
                $nAkhir = round(($nTugas * 0.3) + ($nUts * 0.3) + ($nUas * 0.4), 2);
                $pred = $nAkhir >= 85 ? 'A' : ($nAkhir >= 75 ? 'B' : 'C');

                Nilai::updateOrCreate(
                    ['id_siswa' => $siswaLinked->id, 'id_mapel' => $mpl->id],
                    [
                        'id_guru' => $gurus[$mIdx % count($gurus)]->id,
                        'nilai_tugas' => $nTugas,
                        'nilai_uts' => $nUts,
                        'nilai_uas' => $nUas,
                        'nilai_akhir' => $nAkhir,
                        'predikat' => $pred,
                    ]
                );
            }
        }

        // =========================================================================
        // 8. KEUANGAN H2H: TAGIHAN UKT, PEMBAYARAN UKT, & TRANSAKSI BANK
        // =========================================================================
        foreach ($mahasiswas as $idx => $mhs) {
            $siswaLinked = $siswas[$idx];
            $bankChosen = $bankMitras[$idx % count($bankMitras)];
            $uktAssigned = $ukts[$idx % count($ukts)];

            $biayaUkt = $uktAssigned->nominal;
            $biayaPraktikum = 500000;
            $biayaKemahasiswaan = 250000;
            $totalTagihan = $biayaUkt + $biayaPraktikum + $biayaKemahasiswaan;

            $nomorVa = '880192' . sprintf('%010d', $mhs->id);
            $nomorInvoice = 'INV-20251-' . sprintf('%06d', $mhs->id);

            // 35 Lunas, 10 Sebagian, 5 Belum Bayar
            $statusPayment = ($idx < 35) ? 'Lunas' : (($idx < 45) ? 'Sebagian' : 'Belum Bayar');
            $jumlahBayar = ($statusPayment === 'Lunas') ? $totalTagihan : (($statusPayment === 'Sebagian') ? $totalTagihan / 2 : 0);

            $tagihan = TagihanUkt::firstOrCreate(
                ['id_mahasiswa' => $mhs->id, 'id_tahun_akademik' => $taActive->id],
                [
                    'id_bank_mitra' => $bankChosen->id,
                    'nomor_va' => $nomorVa,
                    'nomor_invoice' => $nomorInvoice,
                    'biaya_ukt' => $biayaUkt,
                    'biaya_praktikum' => $biayaPraktikum,
                    'biaya_kemahasiswaan' => $biayaKemahasiswaan,
                    'total_tagihan' => $totalTagihan,
                    'status_pembayaran' => $statusPayment,
                    'tgl_jatuh_tempo' => now()->addDays(15),
                ]
            );

            if ($jumlahBayar > 0) {
                PembayaranUkt::firstOrCreate(
                    ['nomor_kuitansi' => 'KW-20251-' . sprintf('%06d', $mhs->id)],
                    [
                        'id_user' => 1,
                        'id_tagihan_ukt' => $tagihan->id,
                        'id_mahasiswa' => $mhs->id,
                        'id_ukt' => $uktAssigned->id,
                        'nomor_transaksi_bank' => $bankChosen->kode_bank . '-H2H-202509-' . sprintf('%06d', rand(100000, 999999)),
                        'jumlah_bayar' => $jumlahBayar,
                        'semester_dibayar' => 'Ganjil',
                        'tahun_dibayar' => '2025',
                        'kode_bank' => $bankChosen->kode_bank,
                        'channel_bayar' => $bankChosen->nama_bank,
                        'status_transaksi' => 'SUCCESS',
                        'tgl_bayar' => now()->subDays(rand(1, 20)),
                    ]
                );

                Pembayaran::firstOrCreate(
                    ['id_siswa' => $siswaLinked->id, 'bulan_dibayar' => 'Januari', 'tahun_dibayar' => '2026'],
                    [
                        'id_petugas' => 1,
                        'id_spp' => $sppActive->id,
                        'tgl_bayar' => now()->subDays(rand(1, 20)),
                        'jumlah_bayar' => 3500000,
                    ]
                );
            }
        }

        // =========================================================================
        // 9. BAP PERKULIAHAN & PRESENSI MAHASISWA (300+ Attendance Log Entries)
        // =========================================================================
        foreach ($kelasKuliahs as $kkIdx => $kk) {
            // Create 8 BAP meetings
            for ($m = 1; $m <= 8; $m++) {
                $bap = BapPerkuliahan::firstOrCreate(
                    ['id_kelas_kuliah' => $kk->id, 'pertemuan_ke' => $m],
                    [
                        'id_guru' => $gurus[$kkIdx % count($gurus)]->id,
                        'tanggal_pelaksanaan' => now()->subWeeks(9 - $m)->toDateString(),
                        'jam_mulai_real' => '08:00:00',
                        'jam_selesai_real' => '10:30:00',
                        'materi_pembahasan' => 'Pertemuan ' . $m . ': Modul ' . $m . ' Arsitektur & Praktikum Enterprise',
                        'catatan_dosen' => 'Pembahasan materi berjalan interaktif dengan antusiasme mahasiswa tinggi.',
                        'total_mahasiswa_hadir' => 0,
                    ]
                );

                // Find enrolled students
                $enrolledKrsDetails = KrsDetail::where('id_kelas_kuliah', $kk->id)->with('krs')->get();
                $hadirCount = 0;

                foreach ($enrolledKrsDetails as $kdItem) {
                    $siswaId = $kdItem->krs->id_siswa;
                    $mhsItem = $mahasiswas[($siswaId - 1) % count($mahasiswas)];
                    $statusAtt = (rand(1, 100) <= 85) ? 'Hadir' : ((rand(1, 100) <= 50) ? 'Izin' : 'Sakit');

                    if ($statusAtt === 'Hadir') {
                        $hadirCount++;
                    }

                    PresensiMahasiswa::firstOrCreate(
                        ['id_mahasiswa' => $mhsItem->id, 'id_kelas_kuliah' => $kk->id, 'id_bap' => $bap->id],
                        [
                            'waktu_hadir' => now()->subWeeks(9 - $m)->addMinutes(rand(1, 25)),
                            'latitude' => -6.2146 + (rand(-100, 100) / 10000),
                            'longitude' => 106.8451 + (rand(-100, 100) / 10000),
                            'status' => $statusAtt,
                            'device_fingerprint' => hash('sha256', 'STUDENT-' . $mhsItem->id . '-DEVICE'),
                            'verified_at' => now()->subWeeks(9 - $m),
                        ]
                    );
                }

                $bap->update(['total_mahasiswa_hadir' => $hadirCount]);
            }
        }

        // =========================================================================
        // 10. LMS: COURSE MATERIALS, ASSIGNMENTS & SUBMISSIONS (150+ Submissions)
        // =========================================================================
        foreach ($kelasKuliahs as $kkIdx => $kk) {
            // Course Materials
            for ($mat = 1; $mat <= 3; $mat++) {
                CourseMaterial::firstOrCreate(
                    ['id_kelas_kuliah' => $kk->id, 'minggu_ke' => $mat * 2],
                    [
                        'judul' => 'Modul ' . ($mat * 2) . ': Pedoman Arsitektur Cloud & REST API',
                        'deskripsi' => 'Materi slide perkuliahan dan source code contoh kasus enterprise.',
                        'file_path' => 'materials/modul_' . $kk->id . '_' . $mat . '.pdf',
                        'original_filename' => 'Modul_Kuliah_' . ($mat * 2) . '.pdf',
                        'file_type' => 'application/pdf',
                        'file_size' => 2450000,
                        'publish_at' => now()->subWeeks(6 - $mat),
                        'is_active' => true,
                    ]
                );
            }

            // Assignments
            for ($asg = 1; $asg <= 2; $asg++) {
                $assignment = Assignment::firstOrCreate(
                    ['id_kelas_kuliah' => $kk->id, 'judul' => 'Tugas ' . $asg . ': Implementation & API Integration ' . $kk->nama_kelas],
                    [
                        'deskripsi' => 'Rancang dan buatlah service REST API menggunakan arsitektur bersih dan sertakan pengujian otomatis.',
                        'komponen_penilaian' => 'TUGAS',
                        'bobot_persen' => 20.00,
                        'deadline_at' => now()->addDays($asg * 5),
                        'allow_late_submission' => true,
                        'late_grace_minutes' => 60,
                        'is_published' => true,
                    ]
                );

                // Submissions from enrolled students
                $enrolledKd = KrsDetail::where('id_kelas_kuliah', $kk->id)->with('krs')->get();
                foreach ($enrolledKd as $sIdx => $kdItem) {
                    $siswaId = $kdItem->krs->id_siswa;
                    $mhsItem = $mahasiswas[($siswaId - 1) % count($mahasiswas)];
                    $mhsId = $mhsItem->id;
                    $nilaiSubmitted = rand(80, 98);

                    Submission::firstOrCreate(
                        ['id_assignment' => $assignment->id, 'id_mahasiswa' => $mhsId],
                        [
                            'id_siswa' => $siswaId,
                            'file_path' => 'submissions/asg_' . $assignment->id . '_mhs_' . $mhsId . '.pdf',
                            'original_filename' => 'Tugas' . $asg . '_NIM_' . $mhsItem->nim . '.pdf',
                            'file_mime' => 'application/pdf',
                            'file_size' => 1850000,
                            'submitted_at' => now()->subDays(rand(1, 4)),
                            'submission_microtime' => microtime(true),
                            'submission_token' => 'SUB-TOK-' . strtoupper(Str::random(10)),
                            'hash_receipt' => hash('sha256', 'SUB-' . $assignment->id . '-' . $mhsId),
                            'device_fingerprint' => hash('sha256', 'DEV-' . $mhsId),
                            'client_ip' => '114.10.114.' . ($mhsId % 250),
                            'is_late' => false,
                            'nilai' => $nilaiSubmitted,
                            'feedback' => 'Analisis dan struktur kode sangat rapih, pemisahan layer modul terstruktur dengan baik.',
                            'graded_at' => now()->subDay(),
                            'graded_by_dosen' => $gurus[$kkIdx % count($gurus)]->id,
                        ]
                    );
                }
            }
        }

        // =========================================================================
        // 11. EDOM (EVALUASI DOSEN OLEH MAHASISWA)
        // =========================================================================
        $edomQuestions = [
            ['kat' => 'Pedagogik', 'teks' => 'Dosen menyampaikan materi kuliah secara jelas, terstruktur, dan mudah dipahami.'],
            ['kat' => 'Pedagogik', 'teks' => 'Dosen memberikan umpan balik (feedback) atas tugas dan ujian mahasiswa.'],
            ['kat' => 'Profesional', 'teks' => 'Dosen menguasai materi perkuliahan dan dapat menjawab pertanyaan dengan tepat.'],
            ['kat' => 'Profesional', 'teks' => 'Dosen hadir tepat waktu sesuai dengan jadwal yang telah disepakati.'],
            ['kat' => 'Kepribadian', 'teks' => 'Dosen bersikap adil, objektif, dan menghargai pendapat mahasiswa.'],
            ['kat' => 'Sosial', 'teks' => 'Dosen berkomunikasi dengan sopan dan membangun suasana akademik yang kondusif.'],
        ];

        $pertanyaans = [];
        foreach ($edomQuestions as $qIdx => $q) {
            $pertanyaans[] = EdomPertanyaan::firstOrCreate(
                ['teks_pertanyaan' => $q['teks']],
                ['kategori' => $q['kat'], 'urutan' => $qIdx + 1, 'is_active' => true]
            );
        }

        foreach (array_slice($krsDetailList, 0, 40) as $kdEval) {
            $krsOwner = Krs::find($kdEval->id_krs);
            $kelasOwner = KelasKuliah::find($kdEval->id_kelas_kuliah);

            if ($krsOwner && $kelasOwner) {
                $siswaOwnerId = $krsOwner->id_siswa;
                $assignedGuruId = $gurus[($kdEval->id_kelas_kuliah - 1) % count($gurus)]->id;

                $edom = EdomEvaluasi::firstOrCreate(
                    ['id_krs_detail' => $kdEval->id, 'id_guru' => $assignedGuruId],
                    [
                        'id_siswa' => $siswaOwnerId,
                        'skor_rata_rata' => 4.85,
                        'kritik_saran' => 'Penyampaian materi sangat interaktif dan membantu pemahaman praktikum.',
                    ]
                );

                foreach ($pertanyaans as $pItem) {
                    EdomEvaluasiItem::firstOrCreate(
                        ['id_edom_evaluasi' => $edom->id, 'id_edom_pertanyaan' => $pItem->id],
                        ['skor_nilai' => rand(4, 5)]
                    );
                }
            }
        }

        // =========================================================================
        // 12. FASILITAS & BOOKING FASILITAS KAMPUS
        // =========================================================================
        for ($fb = 0; $fb < 20; $fb++) {
            $userApplicant = $usersStudents[$fb % count($usersStudents)];
            $facilityTarget = $facilities[$fb % count($facilities)];
            $statusBkg = ($fb % 3 == 0) ? 'APPROVED' : (($fb % 3 == 1) ? 'PENDING' : 'REJECTED');

            FacilityBooking::firstOrCreate(
                ['id_facility' => $facilityTarget->id, 'id_pemohon' => $userApplicant->id, 'tanggal_pinjam' => now()->addDays($fb + 1)->toDateString()],
                [
                    'tujuan_penggunaan' => 'Kegiatan Workshop & Praktikum Mandiri Kelompok ' . ($fb + 1),
                    'jam_mulai' => '09:00:00',
                    'jam_selesai' => '12:00:00',
                    'status_booking' => $statusBkg,
                    'catatan_persetujuan' => ($statusBkg === 'APPROVED') ? 'Permohonan disetujui BAAK, harap menjaga kebersihan ruangan.' : 'Sedang diverifikasi petugas.',
                ]
            );
        }

        // =========================================================================
        // 13. BEASISWA & E-SURAT AKADEMIK
        // =========================================================================
        $beasiswaKip = Beasiswa::firstOrCreate(
            ['nama_beasiswa' => 'Beasiswa KIP-Kuliah Kemendikbudristek 2026'],
            ['penyelenggara' => 'Kemendikbudristek', 'jenis_cakupan' => 'FULL', 'persentase_potongan' => 100.00, 'kuota' => 50, 'is_active' => true]
        );

        $beasiswaPrestasi = Beasiswa::firstOrCreate(
            ['nama_beasiswa' => 'Beasiswa Prestasi Akademik & Research Innovation'],
            ['penyelenggara' => 'Yayasan Kampus Enterprise', 'jenis_cakupan' => 'PARSIAL', 'persentase_potongan' => 50.00, 'kuota' => 30, 'is_active' => true]
        );

        for ($k = 0; $k < 15; $k++) {
            PendaftaranBeasiswa::firstOrCreate(
                ['id_beasiswa' => ($k % 2 == 0 ? $beasiswaKip->id : $beasiswaPrestasi->id), 'id_siswa' => $siswas[$k]->id],
                ['status_pengajuan' => 'DISETUJUI', 'ipk_terakhir' => 3.88, 'tgl_pengajuan' => now()->subDays(12), 'catatan' => 'Lolos verifikasi kelayakan dokumen & portofolio']
            );
        }

        foreach (array_slice($siswas, 0, 15) as $sItem) {
            SuratAkademik::firstOrCreate(
                ['id_siswa' => $sItem->id, 'jenis_surat' => 'Surat Keterangan Mahasiswa Aktif'],
                [
                    'nomor_surat' => 'SKMA/2026/03/' . sprintf('%04d', $sItem->id),
                    'perihal' => 'Surat Keterangan Mahasiswa Aktif',
                    'keperluan' => 'Persyaratan Beasiswa, Magang & BPJS Kesehatan',
                    'qr_verification_token' => hash('sha256', $sItem->id . 'SKMA-2026-TOKEN'),
                    'file_pdf_path' => 'documents/surat_aktif/skma_' . $sItem->id . '.pdf',
                    'status' => 'DISETUJUI',
                    'tgl_terbit' => now(),
                ]
            );
        }

        // =========================================================================
        // 14. KKN REGISTRASI, TUGAS AKHIR & SIDANG SKRIPSI
        // =========================================================================
        foreach (array_slice($siswas, 5, 10) as $idx => $sKkn) {
            KknRegistrasi::firstOrCreate(
                ['id_siswa' => $sKkn->id, 'id_tahun_akademik' => $taActive->id],
                [
                    'nama_kelompok' => 'Kelompok KKN Digital ' . ($idx % 4 + 1) . ' Desa Sukamaju',
                    'desa_lokasi' => 'Desa Sukamaju',
                    'kecamatan' => 'Mustikajaya',
                    'kabupaten' => 'Bekasi',
                    'status_pendaftaran' => 'APPROVED',
                    'nilai_huruf' => 'A',
                    'nilai_angka' => 92.50,
                ]
            );
        }

        foreach (array_slice($siswas, 10, 10) as $idx => $sTa) {
            $ta = TugasAkhir::updateOrCreate(
                ['id_siswa' => $sTa->id],
                [
                    'judul_skripsi' => 'Rancang Bangun Architecture Cloud & High-Throughput API pada ' . $sTa->nama,
                    'abstrak_id' => 'Penelitian ini membahas pengembangan arsitektur cloud SIAKAD enterprise...',
                    'status_skripsi' => 'Lulus Yudisium',
                    'file_proposal_path' => 'uploads/proposal/prop_' . $sTa->id . '.pdf',
                    'file_naskah_akhir_path' => 'uploads/skripsi/skripsi_' . $sTa->id . '.pdf',
                    'tgl_lulus_sidang' => now()->subDays(10),
                ]
            );

            LogbookBimbingan::firstOrCreate(
                ['id_tugas_akhir' => $ta->id, 'arahan_dosen_pembimbing' => 'Revisi Bab 1-4 disetujui, lanjut pendaftaran sidang akhir.'],
                [
                    'id_guru' => $gurus[0]->id,
                    'catatan_kemajuan_mahasiswa' => 'Penyelesaian naskah skripsi bab akhir dan pengujian performa sistem.',
                    'tanggal_bimbingan' => now()->subDays(20),
                    'status_acc' => 'Disetujui',
                    'tgl_disetujui' => now()->subDays(19),
                ]
            );

            SidangSkripsi::firstOrCreate(
                ['id_tugas_akhir' => $ta->id],
                [
                    'jenis_sidang' => 'Sidang Akhir',
                    'waktu_sidang' => now()->subDays(10),
                    'nilai_rata_rata' => 90.00,
                    'nilai_huruf' => 'A',
                    'hasil_keputusan' => 'Lulus Tanpa Revisi',
                    'catatan_revisi_sidang' => 'Naskah final telah memenuhi kriteria kelulusan yudisium.',
                ]
            );
        }

        // =========================================================================
        // 15. TRACER STUDY & EMPLOYER FEEDBACK (Alumni Data)
        // =========================================================================
        $companyList = [
            ['perusahaan' => 'PT GoTo Gojek Tokopedia Tbk', 'posisi' => 'Software Engineer Enterprise', 'gaji' => 12500000],
            ['perusahaan' => 'PT Bank Mandiri (Persero) Tbk', 'posisi' => 'Backend Developer API', 'gaji' => 11000000],
            ['perusahaan' => 'Google Indonesia', 'posisi' => 'Cloud Solutions Architect', 'gaji' => 18000000],
            ['perusahaan' => 'PT Traveloka Indonesia', 'posisi' => 'Data Engineer', 'gaji' => 13000000],
            ['perusahaan' => 'PT Shopee International Indonesia', 'posisi' => 'Product Manager', 'gaji' => 14000000],
        ];

        foreach (array_slice($siswas, 35, 10) as $idx => $sTracer) {
            $comp = $companyList[$idx % count($companyList)];

            $tracer = TracerStudy::firstOrCreate(
                ['id_siswa' => $sTracer->id],
                [
                    'tahun_lulus' => 2025,
                    'status_alumni' => 'Bekerja',
                    'nama_instansi_kerja' => $comp['perusahaan'],
                    'jabatan_posisi' => $comp['posisi'],
                    'gaji_pertama' => $comp['gaji'],
                    'masa_tunggu_bulan' => rand(1, 3),
                    'keselarasan_bidang' => 'Sangat Selaras',
                ]
            );

            EmployerFeedback::firstOrCreate(
                ['id_tracer_study' => $tracer->id],
                [
                    'access_token' => 'FB-TOK-' . strtoupper(Str::random(12)),
                    'nama_penilai_atasan' => 'VP of Engineering - ' . $comp['perusahaan'],
                    'jabatan_penilai' => 'Vice President Engineering',
                    'email_perusahaan' => 'hr@' . Str::slug($comp['perusahaan']) . '.com',
                    'nama_perusahaan' => $comp['perusahaan'],
                    'skor_integritas_etika' => 5,
                    'skor_keahlian_bidang' => 5,
                    'skor_bahasa_asing' => 4,
                    'skor_penggunaan_ti' => 5,
                    'skor_komunikasi' => 5,
                    'skor_kerjasama_tim' => 5,
                    'skor_pengembangan_diri' => 5,
                    'saran_kurikulum' => 'Lulusan sangat kompeten dan menguasai arsitektur cloud modern.',
                    'is_completed' => true,
                    'completed_at' => now()->subDays(5),
                ]
            );
        }

        // =========================================================================
        // 16. SKPI AKTIVITAS & MBKM KONVERSI
        // =========================================================================
        $skpiItems = [
            ['kat' => 'Prestasi & Kompetisi', 'id' => 'Juara 1 Hackathon Innovation Cloud & AI Enterprise', 'en' => '1st Winner National Cloud & AI Enterprise Hackathon', 'org' => 'Kemendikbudristek & Google', 'poin' => 25],
            ['kat' => 'Sertifikasi Keahlian', 'id' => 'AWS Certified Solutions Architect Associate', 'en' => 'AWS Certified Solutions Architect Associate', 'org' => 'Amazon Web Services', 'poin' => 30],
            ['kat' => 'Organisasi & Kepemimpinan', 'id' => 'Ketua Himpunan Mahasiswa Teknik Informatika', 'en' => 'President of Informatics Engineering Student Association', 'org' => 'Himpunan Mahasiswa IF', 'poin' => 20],
            ['kat' => 'Sertifikasi Keahlian', 'id' => 'Google Cloud Associate Cloud Engineer', 'en' => 'Google Cloud Associate Cloud Engineer', 'org' => 'Google Cloud', 'poin' => 30],
        ];

        foreach (array_slice($siswas, 0, 20) as $sIdx => $sSkpi) {
            $item = $skpiItems[$sIdx % count($skpiItems)];

            SkpiAktivitas::firstOrCreate(
                ['id_siswa' => $sSkpi->id, 'nama_kegiatan_id' => $item['id']],
                [
                    'kategori' => $item['kat'],
                    'nama_kegiatan_en' => $item['en'],
                    'penyelenggara' => $item['org'],
                    'tahun_kegiatan' => 2025,
                    'poin_sacs' => $item['poin'],
                    'file_bukti_sertifikat' => 'certificates/cert_' . $sSkpi->id . '.pdf',
                    'status_verifikasi' => 'Disetujui Kaprodi',
                    'pejabat_verifikator' => 'Kaprodi Teknik Informatika',
                    'tgl_verifikasi' => now()->subDays(15),
                ]
            );
        }
    }
}
