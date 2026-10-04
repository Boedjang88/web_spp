<?php

namespace Tests\Feature\Web;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicWebTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Kelas $kelas;
    private Spp $spp;
    private Siswa $siswa;
    private Guru $guru;
    private Mapel $mapel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);
        $this->spp = Spp::create(['tahun' => 2025, 'nominal' => 300000]);
        
        $this->siswa = Siswa::create([
            'nisn' => '0051234567',
            'nis' => '2122001',
            'nama' => 'Ahmad Fauzi',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Jl. Merdeka No. 45',
            'no_telp' => '081234567890',
            'id_spp' => $this->spp->id,
        ]);

        $this->guru = Guru::create([
            'nip' => '198501152010011002',
            'nama_guru' => 'Budi Santoso, S.Kom',
            'jenis_kelamin' => 'L',
        ]);

        $this->mapel = Mapel::create([
            'kode_mapel' => 'RPL-01',
            'nama_mapel' => 'Pemrograman Web',
            'kelompok' => 'Kejuruan',
            'kkm' => 75,
        ]);
    }

    public function test_guest_cannot_access_academic_modules(): void
    {
        $this->get('/web/guru')->assertRedirect('/login');
        $this->get('/web/mapel')->assertRedirect('/login');
        $this->get('/web/jadwal')->assertRedirect('/login');
        $this->get('/web/nilai')->assertRedirect('/login');
        $this->get('/web/presensi')->assertRedirect('/login');
    }

    public function test_admin_can_view_guru_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/web/guru');
        $response->assertStatus(200)
            ->assertSee('Data Dosen')
            ->assertSee('Budi Santoso, S.Kom');
    }

    public function test_admin_can_create_new_guru(): void
    {
        $response = $this->actingAs($this->admin)->post('/web/guru', [
            'nip' => '198807222015022001',
            'nama_guru' => 'Sri Wahyuni, M.Pd',
            'jenis_kelamin' => 'P',
            'no_telp' => '081299887766',
        ]);

        $response->assertRedirect('/web/guru');
        $this->assertDatabaseHas('gurus', ['nama_guru' => 'Sri Wahyuni, M.Pd']);
    }

    public function test_admin_can_create_new_mapel(): void
    {
        $response = $this->actingAs($this->admin)->post('/web/mapel', [
            'kode_mapel' => 'DB-01',
            'nama_mapel' => 'Basis Data Relasional',
            'kelompok' => 'Kejuruan',
            'kkm' => 75,
        ]);

        $response->assertRedirect('/web/mapel');
        $this->assertDatabaseHas('mapels', ['kode_mapel' => 'DB-01']);
    }

    public function test_admin_can_create_and_view_jadwal_pelajaran(): void
    {
        $response = $this->actingAs($this->admin)->post('/web/jadwal', [
            'id_kelas' => $this->kelas->id,
            'id_mapel' => $this->mapel->id,
            'id_guru' => $this->guru->id,
            'hari' => 'Senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:30',
            'ruangan' => 'Lab RPL 1',
        ]);

        $response->assertRedirect('/web/jadwal');
        $this->assertDatabaseHas('jadwal_pelajarans', ['hari' => 'Senin']);

        $viewResponse = $this->actingAs($this->admin)->get('/web/jadwal');
        $viewResponse->assertStatus(200)
            ->assertSee('Pemrograman Web')
            ->assertSee('XII RPL 1');
    }

    public function test_admin_can_input_nilai_and_calculate_grade(): void
    {
        $response = $this->actingAs($this->admin)->post('/web/nilai', [
            'id_siswa' => $this->siswa->id,
            'id_mapel' => $this->mapel->id,
            'id_guru' => $this->guru->id,
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2025/2026',
            'nilai_tugas' => 90,
            'nilai_uts' => 85,
            'nilai_uas' => 95,
            'catatan' => 'Sangat aktif.',
        ]);

        $response->assertRedirect('/web/nilai');

        // 90*0.3 + 85*0.3 + 95*0.4 = 27 + 25.5 + 38 = 90.5 -> Predikat A
        $this->assertDatabaseHas('nilais', [
            'id_siswa' => $this->siswa->id,
            'predikat' => 'A',
        ]);
    }

    public function test_admin_can_view_official_e_rapor(): void
    {
        Nilai::create([
            'id_siswa' => $this->siswa->id,
            'id_mapel' => $this->mapel->id,
            'id_guru' => $this->guru->id,
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2025/2026',
            'nilai_tugas' => 90,
            'nilai_uts' => 85,
            'nilai_uas' => 95,
            'nilai_akhir' => 90.5,
            'predikat' => 'A',
        ]);

        $response = $this->actingAs($this->admin)->get("/web/nilai/rapor/{$this->siswa->id}");

        $response->assertStatus(200)
            ->assertSee('UNIVERSITAS SIAKAD ENTERPRISE')
            ->assertSee('KARTU HASIL STUDI')
            ->assertSee('Ahmad Fauzi')
            ->assertSee('Pemrograman Web')
            ->assertSee('90.5');
    }

    public function test_admin_can_record_batch_presensi(): void
    {
        $response = $this->actingAs($this->admin)->post('/web/presensi/batch', [
            'id_kelas' => $this->kelas->id,
            'tanggal' => now()->toDateString(),
            'presensi' => [
                [
                    'id_siswa' => $this->siswa->id,
                    'status' => 'Hadir',
                    'keterangan' => 'Tepat waktu',
                ]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('presensis', [
            'id_siswa' => $this->siswa->id,
            'status' => 'Hadir',
        ]);
    }
}
