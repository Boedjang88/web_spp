<?php

namespace Tests\Feature\Web;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Siswa $siswa;
    protected Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
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

        Pembayaran::create([
            'id_petugas' => $this->user->id,
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => '2025-07-10',
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => '2025',
            'id_spp' => $this->spp->id,
            'jumlah_bayar' => 300000,
        ]);
    }

    public function test_can_view_laporan_index(): void
    {
        $response = $this->actingAs($this->user)->get('/web/laporan');
        $response->assertStatus(200)
            ->assertSee('Laporan Keuangan SPP')
            ->assertSee('Ahmad Fauzi');
    }

    public function test_can_view_printable_laporan(): void
    {
        $response = $this->actingAs($this->user)->get('/web/laporan/cetak');
        $response->assertStatus(200)
            ->assertSeeText('LAPORAN REKAPITULASI PEMBAYARAN SPP')
            ->assertSeeText('Ahmad Fauzi');
    }

    public function test_can_export_laporan_csv(): void
    {
        $response = $this->actingAs($this->user)->get('/web/laporan/export-csv');
        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }
}
