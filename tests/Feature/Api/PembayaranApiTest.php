<?php

namespace Tests\Feature\Api;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembayaranApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Siswa $siswa;
    protected Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'petugas']);
        $kelas = Kelas::create(['nama_kelas' => 'IF-3A', 'kompetensi_keahlian' => 'RPL']);
        $this->spp = Spp::create(['tahun' => 2025, 'nominal' => 300000]);
        $this->siswa = Siswa::create([
            'nisn' => '0051234567',
            'nis' => '2122001',
            'nama' => 'Ahmad Fauzi',
            'id_kelas' => $kelas->id,
            'alamat' => 'Jl. Merdeka No. 45',
            'no_telp' => '081234567890',
            'id_spp' => $this->spp->id,
        ]);
    }

    public function test_can_record_pembayaran(): void
    {
        $payload = [
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => '2025-07-10',
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => 2025,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/pembayaran', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.bulan_dibayar', 'Juli')
            ->assertJsonPath('data.jumlah_bayar', 300000);

        $this->assertDatabaseHas('pembayarans', [
            'id_siswa' => $this->siswa->id,
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => '2025',
        ]);
    }

    public function test_cannot_record_duplicate_pembayaran_for_same_period(): void
    {
        Pembayaran::create([
            'id_petugas' => $this->user->id,
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => '2025-07-10',
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => '2025',
            'id_spp' => $this->spp->id,
            'jumlah_bayar' => $this->spp->nominal,
        ]);

        $payload = [
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => '2025-07-12',
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => 2025,
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/pembayaran', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_can_get_kwitansi_json(): void
    {
        $pembayaran = Pembayaran::create([
            'id_petugas' => $this->user->id,
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => '2025-07-10',
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => '2025',
            'id_spp' => $this->spp->id,
            'jumlah_bayar' => 300000,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->getJson("/api/pembayaran/{$pembayaran->id}/kwitansi");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'LUNAS')
            ->assertJsonPath('data.rincian.jumlah_bayar', 300000);
    }
}
