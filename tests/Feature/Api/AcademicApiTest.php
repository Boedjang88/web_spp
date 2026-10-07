<?php

namespace Tests\Feature\Api;

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
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AcademicApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Kelas $kelas;
    private Spp $spp;
    private Siswa $siswa;
    private Guru $guru;
    private Mapel $mapel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        $this->kelas = Kelas::create(['nama_kelas' => 'IF-3A', 'kompetensi_keahlian' => 'RPL']);
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

    public function test_can_fetch_guru_api(): void
    {
        $response = $this->getJson('/api/guru');
        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_can_create_guru_api(): void
    {
        $response = $this->postJson('/api/guru', [
            'nip' => '199012052018011003',
            'nama_guru' => 'Hendra Pratama, S.Kom',
            'jenis_kelamin' => 'L',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.nama_guru', 'Hendra Pratama, S.Kom');
    }

    public function test_can_create_mapel_api(): void
    {
        $response = $this->postJson('/api/mapel', [
            'kode_mapel' => 'MAT-01',
            'nama_mapel' => 'Matematika Terapan',
            'kelompok' => 'Umum',
            'kkm' => 70,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.kode_mapel', 'MAT-01');
    }

    public function test_can_create_jadwal_api(): void
    {
        $response = $this->postJson('/api/jadwal', [
            'id_kelas' => $this->kelas->id,
            'id_mapel' => $this->mapel->id,
            'id_guru' => $this->guru->id,
            'hari' => 'Senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:30',
            'ruangan' => 'Lab RPL',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.hari', 'Senin');
    }

    public function test_can_input_nilai_and_fetch_rapor_api(): void
    {
        $this->postJson('/api/nilai', [
            'id_siswa' => $this->siswa->id,
            'id_mapel' => $this->mapel->id,
            'id_guru' => $this->guru->id,
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2025/2026',
            'nilai_tugas' => 90,
            'nilai_uts' => 90,
            'nilai_uas' => 90,
        ])->assertStatus(201);

        $raporResponse = $this->getJson("/api/nilai/rapor/{$this->siswa->id}?semester=Ganjil&tahun_ajaran=2025/2026");

        $raporResponse->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.siswa.nama', 'Ahmad Fauzi')
            ->assertJsonPath('data.ringkasan_akademik.total_mapel', 1)
            ->assertJsonPath('data.ringkasan_akademik.rata_rata_nilai', 90);
    }

    public function test_can_record_batch_presensi_api(): void
    {
        $response = $this->postJson('/api/presensi/batch', [
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

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_siswa', 1);
    }
}
