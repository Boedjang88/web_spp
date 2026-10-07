<?php

namespace Tests\Feature\Api;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Kelas $kelas;
    protected Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->kelas = Kelas::create(['nama_kelas' => 'IF-3A', 'kompetensi_keahlian' => 'RPL']);
        $this->spp = Spp::create(['tahun' => 2025, 'nominal' => 300000]);
    }

    public function test_can_create_siswa(): void
    {
        $payload = [
            'nisn' => '0051234567',
            'nis' => '2122001',
            'nama' => 'Ahmad Fauzi',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Jl. Merdeka No. 45',
            'no_telp' => '081234567890',
            'id_spp' => $this->spp->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/siswa', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.nama', 'Ahmad Fauzi')
            ->assertJsonPath('data.nisn', '0051234567');

        $this->assertDatabaseHas('siswas', ['nisn' => '0051234567']);
    }

    public function test_can_get_siswa_tunggakan_info(): void
    {
        $siswa = Siswa::create([
            'nisn' => '0051234567',
            'nis' => '2122001',
            'nama' => 'Ahmad Fauzi',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Jl. Merdeka No. 45',
            'no_telp' => '081234567890',
            'id_spp' => $this->spp->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/siswa/{$siswa->id}/tunggakan");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'siswa' => ['id', 'nisn', 'nama', 'kelas', 'nominal_spp'],
                    'tunggakan' => ['total_bulan', 'total_rupiah', 'list_bulan'],
                ],
            ]);
    }
}
