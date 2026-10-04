<?php

namespace Tests\Feature\Web;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuratTagihanWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_surat_tagihan(): void
    {
        $response = $this->get('/web/siswa/1/surat-tagihan');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_official_surat_tagihan(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 250000]);
        $user = User::factory()->create();

        $siswa = Siswa::create([
            'nisn' => '0051234567',
            'nis' => '2122001',
            'nama' => 'Siti Nurhaliza',
            'id_kelas' => $kelas->id,
            'alamat' => 'Jl. Mawar No. 12',
            'no_telp' => '081234567890',
            'id_spp' => $spp->id,
        ]);

        $response = $this->actingAs($user)->get("/web/siswa/{$siswa->id}/surat-tagihan");

        $response->assertStatus(200)
            ->assertSee('SMK MERDEKA BELAJAR')
            ->assertSee('Tagihan Iuran SPP')
            ->assertSee('Siti Nurhaliza')
            ->assertSee('0051234567')
            ->assertSee('Drs. H. Hendra Wijaya, M.Pd');
    }
}
