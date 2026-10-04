<?php

namespace Tests\Feature\Web;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Kelas $kelas;
    protected Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);
        $this->spp = Spp::create(['tahun' => 2025, 'nominal' => 300000]);
    }

    public function test_guest_cannot_access_dashboard_or_crud(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/web/kelas');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200)
            ->assertSee('Sistem Akademik');
    }

    public function test_can_create_kelas_via_web(): void
    {
        $response = $this->actingAs($this->user)->post('/web/kelas', [
            'nama_kelas' => 'XII TKJ 2',
            'kompetensi_keahlian' => 'TKJ',
        ]);

        $response->assertRedirect(route('web.kelas.index'));
        $this->assertDatabaseHas('kelas', ['nama_kelas' => 'XII TKJ 2']);
    }

    public function test_can_create_siswa_via_web(): void
    {
        $response = $this->actingAs($this->user)->post('/web/siswa', [
            'nisn' => '0051234567',
            'nis' => '2122001',
            'nama' => 'Ahmad Fauzi',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Jl. Merdeka No. 45',
            'no_telp' => '081234567890',
            'id_spp' => $this->spp->id,
        ]);

        $response->assertRedirect(route('web.siswa.index'));
        $this->assertDatabaseHas('siswas', ['nisn' => '0051234567']);
    }

    public function test_can_record_pembayaran_and_view_receipt_via_web(): void
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

        $response = $this->actingAs($this->user)->post('/web/pembayaran', [
            'id_siswa' => $siswa->id,
            'tgl_bayar' => '2025-07-10',
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => 2025,
        ]);

        $pembayaran = Pembayaran::first();
        $this->assertNotNull($pembayaran);
        $response->assertRedirect(route('web.pembayaran.show', $pembayaran->id));

        // Test printable receipt page
        $receiptResponse = $this->actingAs($this->user)->get(route('web.pembayaran.cetak', $pembayaran->id));
        $receiptResponse->assertStatus(200)
            ->assertSee('BUKTI PEMBAYARAN SPP')
            ->assertSee('Ahmad Fauzi');
    }
}
