<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiUxShellAndNavigationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $lecturer;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $spp = Spp::create(['tahun' => 2026, 'nominal' => 3500000]);
        $kelas = Kelas::create(['nama_kelas' => 'TI-3A', 'kompetensi_keahlian' => 'Teknik Informatika']);

        $this->admin = User::factory()->create([
            'name' => 'Admin Akademik',
            'email' => 'admin@siakad.test',
            'role' => 'admin',
        ]);

        $this->lecturer = User::factory()->create([
            'name' => 'Dr. Suhartono, M.Kom',
            'email' => 'dosen@siakad.test',
            'role' => 'dosen',
        ]);
        Guru::create([
            'id_user' => $this->lecturer->id,
            'nip' => '198001012005011001',
            'nama_guru' => 'Dr. Suhartono, M.Kom',
            'jenis_kelamin' => 'L',
            'no_telp' => '08120000001',
            'email' => 'dosen@siakad.test',
            'alamat' => 'Kampus',
        ]);

        $this->student = User::factory()->create([
            'name' => 'Ahmad Mahasiswa',
            'email' => 'mhs@siakad.test',
            'role' => 'mahasiswa',
        ]);
        Siswa::create([
            'id_user' => $this->student->id,
            'nisn' => '2026110001',
            'nis' => 'NIM2026110001',
            'nama' => 'Ahmad Mahasiswa',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'no_telp' => '08120000002',
            'alamat' => 'Kampus',
        ]);
    }

    /** @test */
    public function student_sees_grouped_task_oriented_navigation_and_no_floating_dock(): void
    {
        $response = $this->actingAs($this->student)->get(route('dashboard'));
        $response->assertOk();

        // 1. Branding
        $response->assertSee('SIAKAD Enterprise');

        // 2. Task-oriented grouping (Phase 3)
        $response->assertSee('Utama');
        $response->assertSee('Akademik &amp; Studi', false);
        $response->assertSee('Layanan &amp; Keuangan', false);
        $response->assertSee('Tingkat Akhir &amp; DBPTA', false);

        // 3. Key student destinations preserved
        $response->assertSee('Smart KRS');
        $response->assertSee('Presensi Kuliah');
        $response->assertSee('Nilai &amp; Transkrip', false);
        $response->assertSee('Pembayaran UKT / SPP');
        $response->assertSee('e-Surat Akademik');
        $response->assertSee('Proposal Skripsi');
        $response->assertSee('Bimbingan Tugas Akhir');
        $response->assertSee('Sidang &amp; Yudisium', false);
        $response->assertSee('Pemberkasan Ijazah');
        $response->assertSee('Kuliah Kerja Nyata (KKN)');
        $response->assertSee('Pendaftaran Wisuda');

        // 4. Mobile bottom floating dock obstruction is gone
        $response->assertDontSee('<!-- Mobile Bottom App Dock Navigation Bar -->', false);
        $response->assertDontSee('shadow-floating p-1.5 flex items-center justify-around', false);
        $response->assertDontSee('pb-24 md:pb-0', false);

        // 5. Drawer and header controls exist
        $response->assertSee('toggleMobileMenu');
        $response->assertSee('id="mobileSidebar"', false);
    }

    /** @test */
    public function lecturer_sees_teaching_and_academic_sections(): void
    {
        $response = $this->actingAs($this->lecturer)->get(route('dashboard'));
        $response->assertOk();

        // 1. Sections
        $response->assertSee('Perkuliahan &amp; Pengajaran', false);
        $response->assertSee('Bimbingan &amp; Evaluasi', false);

        // 2. Lecturer destinations
        $response->assertSee('Jadwal Mengajar');
        $response->assertSee('Presensi Kelas');
        $response->assertSee('Input Nilai Mahasiswa');
        $response->assertSee('Persetujuan KRS PA');
        $response->assertSee('Digital BAP Perkuliahan');

        // 3. Does not see student-only links
        $response->assertDontSee('Tingkat Akhir &amp; DBPTA', false);
    }

    /** @test */
    public function admin_sees_management_and_governance_sections(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertOk();

        // 1. Admin groupings
        $response->assertSee('Pengguna &amp; Hak Akses', false);
        $response->assertSee('Akademik &amp; Perkuliahan', false);
        $response->assertSee('Keuangan &amp; UKT', false);
        $response->assertSee('Sistem &amp; Keamanan', false);

        // 2. Admin destinations
        $response->assertSee('Manajemen User');
        $response->assertSee('Dosen &amp; Pendidik', false);
        $response->assertSee('Data Mahasiswa');
        $response->assertSee('Mata Kuliah');
        $response->assertSee('Kelas Perkuliahan');
        $response->assertSee('Transaksi Pembayaran');
        $response->assertSee('Tarif UKT / SPP');
        $response->assertSee('Laporan Keuangan');
        $response->assertSee('Audit Trail Forensik');
        $response->assertSee('Interactive API Console');
    }

    /** @test */
    public function layout_renders_flash_alert_component(): void
    {
        $response = $this->actingAs($this->student)
            ->withSession(['success' => 'KRS berhasil disimpan secara permanen.'])
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('KRS berhasil disimpan secara permanen.');
        $response->assertSee('role="alert"', false);
        $response->assertSee('aria-label="Tutup notifikasi"', false);
    }
}
