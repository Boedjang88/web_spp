<?php

namespace Tests\Feature\Api;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BatchPembayaranTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Siswa $siswa;
    protected Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'petugas']);
        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);
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

    public function test_can_process_batch_pembayaran(): void
    {
        $payload = [
            'id_siswa' => $this->siswa->id,
            'bulan_list' => ['Juli', 'Agustus', 'September'],
            'tahun_dibayar' => 2025,
            'tgl_bayar' => '2025-07-15',
        ];

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/pembayaran/batch', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.total_transaksi', 3)
            ->assertJsonPath('data.total_rupiah', 900000);

        $this->assertDatabaseHas('pembayarans', ['bulan_dibayar' => 'Juli']);
        $this->assertDatabaseHas('pembayarans', ['bulan_dibayar' => 'Agustus']);
        $this->assertDatabaseHas('pembayarans', ['bulan_dibayar' => 'September']);
    }

    public function test_can_fetch_rekap_laporan_api(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/laporan/rekap');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'ringkasan' => [
                        'total_transaksi',
                        'total_pemasukan',
                        'formatted_total_pemasukan',
                    ],
                    'transaksi',
                ],
            ]);
    }

    public function test_can_access_interactive_api_docs(): void
    {
        $response = $this->get('/api/docs');
        $response->assertStatus(200)
            ->assertSee('Web SPP RESTful API Docs')
            ->assertSee('Live Response Console');
    }
}
