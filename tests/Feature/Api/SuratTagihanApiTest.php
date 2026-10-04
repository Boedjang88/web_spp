<?php

namespace Tests\Feature\Api;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SuratTagihanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_cannot_fetch_surat_tagihan(): void
    {
        $response = $this->getJson('/api/siswa/1/surat-tagihan');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_fetch_surat_tagihan_data(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'X TKJ 1', 'kompetensi_keahlian' => 'TKJ']);
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 300000]);
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $siswa = Siswa::create([
            'nisn' => '0057778899',
            'nis' => '2122002',
            'nama' => 'Bambang Sudrajat',
            'id_kelas' => $kelas->id,
            'alamat' => 'Jl. Kenanga No. 5',
            'no_telp' => '081298765432',
            'id_spp' => $spp->id,
        ]);

        $response = $this->getJson("/api/siswa/{$siswa->id}/surat-tagihan");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.siswa.nama', 'Bambang Sudrajat')
            ->assertJsonPath('data.siswa.nisn', '0057778899')
            ->assertJsonPath('data.institusi.nama', 'SMK Merdeka Belajar')
            ->assertJsonPath('data.tarif_spp_bulanan', 300000);
    }
}
