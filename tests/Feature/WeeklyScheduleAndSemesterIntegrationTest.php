<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyScheduleAndSemesterIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        TahunAkademik::create([
            'kode_tahun' => '20251',
            'nama_tahun' => '2025/2026',
            'tahun_akademik' => '2025/2026',
            'semester' => 'Ganjil',
            'tgl_mulai' => '2025-09-01',
            'tgl_selesai' => '2026-01-31',
            'is_active' => true,
        ]);
    }

    public function test_mahasiswa_can_access_weekly_schedule_grid()
    {
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 5000000]);
        $kelas = Kelas::create(['nama_kelas' => 'TI-3A', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $siswa = Siswa::create([
            'nis' => '109240940090',
            'nama' => 'Budi Mahasiswa',
            'nama_lengkap' => 'Budi Mahasiswa',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'nisn' => '1234567890',
            'alamat' => 'Jl. Merdeka 10',
            'no_telp' => '08123456789',
        ]);

        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'id_siswa' => $siswa->id,
        ]);

        $guru = Guru::create(['nama_guru' => 'Dr. Aris']);
        $mapel = Mapel::create(['nama_mapel' => 'Pemrograman Web', 'kode_mapel' => 'PW101', 'semester' => 3]);

        JadwalPelajaran::create([
            'id_kelas' => $kelas->id,
            'id_mapel' => $mapel->id,
            'id_guru' => $guru->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:40',
            'ruangan' => 'Lab 1',
        ]);

        $response = $this->actingAs($user)->get('/web/jadwal');

        $response->assertStatus(200);
        $response->assertSee('Jadwal Perkuliahan');
        $response->assertSee('Pemrograman Web');
        $response->assertSee('Dr. Aris');
        $response->assertSee('Grid Mingguan');
    }

    public function test_dosen_can_access_weekly_schedule_grid()
    {
        $guru = Guru::create(['nama_guru' => 'Prof. Dian']);
        $user = User::factory()->create([
            'role' => 'dosen',
            'id_guru' => $guru->id,
        ]);

        $kelas = Kelas::create(['nama_kelas' => 'TI-3B', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $mapel = Mapel::create(['nama_mapel' => 'Basis Data', 'kode_mapel' => 'BD102', 'semester' => 3]);

        JadwalPelajaran::create([
            'id_kelas' => $kelas->id,
            'id_mapel' => $mapel->id,
            'id_guru' => $guru->id,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:40',
            'ruangan' => 'Lab 2',
        ]);

        $response = $this->actingAs($user)->get('/web/jadwal');

        $response->assertStatus(200);
        $response->assertSee('Basis Data');
        $response->assertSee('TI-3B');
    }

    public function test_admin_can_filter_weekly_schedule_by_semester()
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $kelas = Kelas::create(['nama_kelas' => 'TI-1A', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $guru = Guru::create(['nama_guru' => 'Dosen Test']);

        $mapelSem1 = Mapel::create(['nama_mapel' => 'Algoritma Pemrograman', 'kode_mapel' => 'AP101', 'semester' => 1]);
        $mapelSem2 = Mapel::create(['nama_mapel' => 'Struktur Data', 'kode_mapel' => 'SD102', 'semester' => 2]);

        JadwalPelajaran::create([
            'id_kelas' => $kelas->id,
            'id_mapel' => $mapelSem1->id,
            'id_guru' => $guru->id,
            'hari' => 'Rabu',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:40',
            'ruangan' => 'R-101',
        ]);

        JadwalPelajaran::create([
            'id_kelas' => $kelas->id,
            'id_mapel' => $mapelSem2->id,
            'id_guru' => $guru->id,
            'hari' => 'Kamis',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:40',
            'ruangan' => 'R-102',
        ]);

        $response = $this->actingAs($user)->get('/web/jadwal?semester=1');

        $response->assertStatus(200);
        $response->assertSee('Algoritma Pemrograman');
        $response->assertDontSee('Struktur Data');
    }
}
