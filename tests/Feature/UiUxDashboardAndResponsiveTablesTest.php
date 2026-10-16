<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiUxDashboardAndResponsiveTablesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $lecturer;
    private Guru $guru;
    private User $student;
    private Siswa $siswa;
    private Kelas $kelas;
    private Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->spp = Spp::create(['tahun' => 2026, 'nominal' => 3500000]);
        $this->kelas = Kelas::create(['nama_kelas' => 'TI-3A', 'kompetensi_keahlian' => 'Teknik Informatika']);

        $this->admin = User::factory()->create([
            'name' => 'Admin Akademik',
            'email' => 'admin_ui@siakad.test',
            'role' => 'admin',
        ]);

        $this->guru = Guru::create([
            'nip' => '198201012008011002',
            'nama_guru' => 'Dr. Rahmat Hidayat, M.Kom',
            'jenis_kelamin' => 'L',
            'no_telp' => '08120000003',
            'email' => 'dosen_ui@siakad.test',
            'alamat' => 'Kampus Terpadu',
        ]);

        $this->lecturer = User::factory()->create([
            'name' => 'Dr. Rahmat Hidayat, M.Kom',
            'email' => 'dosen_ui@siakad.test',
            'role' => 'dosen',
            'id_guru' => $this->guru->id,
        ]);

        $this->student = User::factory()->create([
            'name' => 'Bintang Mahasiswa',
            'email' => 'mhs_ui@siakad.test',
            'role' => 'mahasiswa',
        ]);
        $this->siswa = Siswa::create([
            'id_user' => $this->student->id,
            'nisn' => '2026110099',
            'nis' => 'NIM2026110099',
            'nama' => 'Bintang Mahasiswa',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'no_telp' => '08120000004',
            'alamat' => 'Kampus Terpadu',
        ]);

        TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => 'Ganjil 2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
            'tgl_mulai' => '2026-09-01',
            'tgl_selesai' => '2027-01-31',
        ]);
    }

    /** @test */
    public function it_renders_optimized_student_dashboard_with_schedule_and_grades(): void
    {
        $response = $this->actingAs($this->student)->get(route('dashboard'));
        $response->assertOk();

        // 1. Header & Identity
        $response->assertSee('Portal Mandiri Mahasiswa');
        $response->assertSee('Selamat Datang, Bintang Mahasiswa!');
        $response->assertSee('Smart KRS');
        $response->assertSee('Presensi');
        $response->assertSee('UKT');

        // 2. Schedule and grades cards directly visible
        $response->assertSee('Jadwal Perkuliahan Hari Ini');
        $response->assertSee('Transkrip Nilai Akademik');
        $response->assertSee('Status Keuangan UKT');
    }

    /** @test */
    public function it_renders_school_student_dashboard_with_school_terminology(): void
    {
        $view = view('dashboard.siswa', [
            'siswa' => $this->siswa,
            'jadwalSiswa' => collect(),
            'nilaiSiswa' => collect(),
            'presensiSummary' => ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'persentase' => 100],
            'tunggakan' => ['total_bulan' => 0, 'total_rupiah' => 0, 'list_bulan' => []],
            'riwayatPembayaran' => collect(),
            'hariIni' => 'Senin',
        ])->render();

        $this->assertStringContainsString('Portal Mandiri Siswa', $view);
        $this->assertStringContainsString('NISN: 2026110099', $view);
        $this->assertStringContainsString('Jadwal Pelajaran', $view);
        $this->assertStringContainsString('Status Keuangan SPP', $view);
    }

    /** @test */
    public function it_renders_optimized_lecturer_dashboard_with_teaching_sessions(): void
    {
        $response = $this->actingAs($this->lecturer)->get(route('dashboard'));
        $response->assertOk();

        // 1. Header & Identity
        $response->assertSee('Dashboard Dosen Pengajar');
        $response->assertSee('Selamat Bertugas, Dr. Rahmat Hidayat, M.Kom!');
        $response->assertSee('Sesi Mengajar Hari Ini');
        $response->assertSee('Total Mata Kuliah Diampu');
        $response->assertSee('Jadwal Mengajar &amp; Kontrol Presensi', false);
    }

    /** @test */
    public function it_renders_school_teacher_dashboard_with_teacher_terminology(): void
    {
        $view = view('dashboard.guru', [
            'guru' => $this->guru,
            'jadwalGuruHariIni' => collect(),
            'totalJadwalAjar' => 3,
            'nilaiTerbaruGuru' => collect(),
            'hariIni' => 'Senin',
        ])->render();

        $this->assertStringContainsString('Dashboard Guru Pengajar', $view);
        $this->assertStringContainsString('NIP: 198201012008011002', $view);
        $this->assertStringContainsString('Mata Pelajaran Diampu', $view);
    }

    /** @test */
    public function it_renders_smart_krs_page_with_responsive_card_and_table_layouts(): void
    {
        $response = $this->actingAs($this->student)->get(route('siakad.krs.index'));
        $response->assertOk();

        // 1. Sections
        $response->assertSee('Kartu Rencana Studi (KRS) Online');
        $response->assertSee('Rencana Studi Terpilih');
        $response->assertSee('Penawaran Kelas Kuliah');
        $response->assertSee('Batas SKS');
        $response->assertSee('SKS Terambil');
        $response->assertSee('Belum ada mata kuliah yang dipilih');

        // 2. Dual presentation classes (Desktop table + Mobile cards) when courses are enrolled
        $detail = new \App\Models\KrsDetail();
        $detail->id = 1;
        $detail->setRelation('kelasKuliah', null);

        $krs = new \App\Models\Krs();
        $krs->id = 1;
        $krs->status_krs = 'Draft';
        $krs->setRelation('details', collect([$detail]));

        $renderedWithKrs = view('siakad.krs.index', [
            'siswa' => $this->siswa,
            'mahasiswa' => $this->siswa,
            'activeYear' => TahunAkademik::active()->first(),
            'krs' => $krs,
            'availableClasses' => collect(),
            'isCleared' => true,
        ])->render();

        $this->assertStringContainsString('hidden sm:block', $renderedWithKrs);
        $this->assertStringContainsString('block sm:hidden', $renderedWithKrs);
    }

    /** @test */
    public function it_renders_nilai_index_with_dual_table_and_card_layouts(): void
    {
        $mapel = Mapel::create([
            'kode_mapel' => 'IF301',
            'nama_mapel' => 'Rekayasa Perangkat Lunak',
            'kkm' => 75,
            'sks' => 3,
            'semester' => 3,
        ]);

        Nilai::create([
            'id_siswa' => $this->siswa->id,
            'id_mapel' => $mapel->id,
            'id_guru' => $this->guru->id,
            'nilai_tugas' => 85,
            'nilai_uts' => 80,
            'nilai_uas' => 90,
            'nilai_akhir' => 85.5,
            'predikat' => 'A',
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2025/2026',
        ]);

        $response = $this->actingAs($this->admin)->get(route('web.nilai.index'));
        $response->assertOk();

        // 1. Content
        $response->assertSee('Nilai Akademik &amp; E-KHS Mahasiswa', false);
        $response->assertSee('Rekayasa Perangkat Lunak');
        $response->assertSee('Bintang Mahasiswa');

        // 2. Dual presentation classes (Desktop table + Mobile cards)
        $response->assertSee('hidden md:block', false);
        $response->assertSee('block md:hidden', false);
    }

    /** @test */
    public function it_renders_pembayaran_index_with_dual_table_and_card_layouts(): void
    {
        Pembayaran::create([
            'id_petugas' => $this->admin->id,
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => now()->toDateString(),
            'bulan_dibayar' => 'Oktober',
            'tahun_dibayar' => '2026',
            'id_spp' => $this->spp->id,
            'jumlah_bayar' => 3500000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('web.pembayaran.index'));
        $response->assertOk();

        // 1. Content
        $response->assertSee('Transaksi Pembayaran UKT');
        $response->assertSee('Bintang Mahasiswa');
        $response->assertSee('3.500.000');

        // 2. Dual presentation classes (Desktop table + Mobile cards)
        $response->assertSee('hidden md:block', false);
        $response->assertSee('block md:hidden', false);
    }
}
