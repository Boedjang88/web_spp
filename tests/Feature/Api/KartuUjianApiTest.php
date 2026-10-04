<?php

namespace Tests\Feature\Api;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KartuUjianApiTest extends TestCase
{
    use RefreshDatabase;

    protected Siswa $siswa1;
    protected Siswa $siswa2;
    protected User $userSiswa1;
    protected User $userSiswa2;
    protected User $admin;
    protected Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();

        $kelas = Kelas::create(['nama_kelas' => 'XII TKJ 1', 'kompetensi_keahlian' => 'TKJ']);
        $this->spp = Spp::create(['tahun' => 2025, 'nominal' => 350000]);

        $this->siswa1 = Siswa::create([
            'nisn' => '0099999991',
            'nis' => '3001',
            'nama' => 'Rian Maulana',
            'id_kelas' => $kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Surabaya',
            'no_telp' => '0833333333',
        ]);

        $this->siswa2 = Siswa::create([
            'nisn' => '0099999992',
            'nis' => '3002',
            'nama' => 'Dinda Kirana',
            'id_kelas' => $kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Malang',
            'no_telp' => '0844444444',
        ]);

        $this->userSiswa1 = User::factory()->create([
            'name' => 'Rian Maulana',
            'email' => 'rian@test.com',
            'role' => 'siswa',
            'id_siswa' => $this->siswa1->id,
            'is_active' => true,
        ]);

        $this->userSiswa2 = User::factory()->create([
            'name' => 'Dinda Kirana',
            'email' => 'dinda@test.com',
            'role' => 'siswa',
            'id_siswa' => $this->siswa2->id,
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin API',
            'email' => 'adminapi@test.com',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_siswa_can_fetch_own_kartu_ujian_api(): void
    {
        Sanctum::actingAs($this->userSiswa1);

        $response = $this->getJson("/api/siswa/{$this->siswa1->id}/kartu-ujian");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.siswa.nama', 'Rian Maulana')
            ->assertJsonPath('data.is_eligible', false);
    }

    public function test_siswa_cannot_fetch_other_student_kartu_ujian_api(): void
    {
        Sanctum::actingAs($this->userSiswa1);

        $response = $this->getJson("/api/siswa/{$this->siswa2->id}/kartu-ujian");

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_admin_can_fetch_any_student_kartu_ujian_api(): void
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/siswa/{$this->siswa1->id}/kartu-ujian");
        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $response = $this->getJson("/api/siswa/{$this->siswa2->id}/kartu-ujian");
        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
