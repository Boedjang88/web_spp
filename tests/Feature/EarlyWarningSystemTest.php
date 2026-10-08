<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EarlyWarningSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_baak_user_can_access_ews_dashboard()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('siakad.baak.ews.index'));

        $response->assertStatus(200);
        $response->assertViewIs('siakad.baak.ews-index');
    }

    public function test_baak_user_can_trigger_ews_scan()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $kelas = Kelas::create(['nama_kelas' => 'IF-2026', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 5000000]);

        Siswa::create([
            'nisn' => '1234567890',
            'nis' => '12345',
            'nama' => 'Mahasiswa Test EWS',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Pendidikan No. 12',
            'no_telp' => '08123456789',
            'status_kelulusan' => 'Aktif',
        ]);

        $response = $this->actingAs($user)->post(route('siakad.baak.ews.scan'));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
