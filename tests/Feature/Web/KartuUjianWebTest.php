<?php

namespace Tests\Feature\Web;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KartuUjianWebTest extends TestCase
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

        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);
        $this->spp = Spp::create(['tahun' => 2025, 'nominal' => 300000]);

        $this->siswa1 = Siswa::create([
            'nisn' => '0011111111',
            'nis' => '1001',
            'nama' => 'Ahmad Fauzi',
            'id_kelas' => $kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Bandung',
            'no_telp' => '0811111111',
        ]);

        $this->siswa2 = Siswa::create([
            'nisn' => '0022222222',
            'nis' => '1002',
            'nama' => 'Budi Pratama',
            'id_kelas' => $kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Jakarta',
            'no_telp' => '0822222222',
        ]);

        $this->userSiswa1 = User::factory()->create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@test.com',
            'role' => 'siswa',
            'id_siswa' => $this->siswa1->id,
            'is_active' => true,
        ]);

        $this->userSiswa2 = User::factory()->create([
            'name' => 'Budi Pratama',
            'email' => 'budi@test.com',
            'role' => 'siswa',
            'id_siswa' => $this->siswa2->id,
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_siswa_can_view_own_kartu_ujian(): void
    {
        $response = $this->actingAs($this->userSiswa1)->get(route('web.siswa.kartuUjian', $this->siswa1->id));

        $response->assertStatus(200)
            ->assertSee('Ahmad Fauzi')
            ->assertSee('0011111111');
    }

    public function test_siswa_cannot_view_other_student_kartu_ujian(): void
    {
        $response = $this->actingAs($this->userSiswa1)->get(route('web.siswa.kartuUjian', $this->siswa2->id));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_any_student_kartu_ujian(): void
    {
        $response = $this->actingAs($this->admin)->get(route('web.siswa.kartuUjian', $this->siswa1->id));
        $response->assertStatus(200)
            ->assertSee('Ahmad Fauzi');

        $response = $this->actingAs($this->admin)->get(route('web.siswa.kartuUjian', $this->siswa2->id));
        $response->assertStatus(200)
            ->assertSee('Budi Pratama');
    }

    public function test_kartu_ujian_shows_paid_clearance_when_all_months_paid(): void
    {
        $monthsFirstHalf = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $monthsSecondHalf = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];

        foreach ($monthsFirstHalf as $bulan) {
            Pembayaran::create([
                'id_petugas' => $this->admin->id,
                'id_siswa' => $this->siswa1->id,
                'tgl_bayar' => now(),
                'bulan_dibayar' => $bulan,
                'tahun_dibayar' => '2025',
                'id_spp' => $this->spp->id,
                'jumlah_bayar' => 300000,
            ]);
        }

        foreach ($monthsSecondHalf as $bulan) {
            Pembayaran::create([
                'id_petugas' => $this->admin->id,
                'id_siswa' => $this->siswa1->id,
                'tgl_bayar' => now(),
                'bulan_dibayar' => $bulan,
                'tahun_dibayar' => '2026',
                'id_spp' => $this->spp->id,
                'jumlah_bayar' => 300000,
            ]);
        }

        $response = $this->actingAs($this->userSiswa1)->get(route('web.siswa.kartuUjian', $this->siswa1->id));

        $response->assertStatus(200)
            ->assertSee('STATUS: LUNAS')
            ->assertSee('Lunas Administrasi (Bebas Tanggungan)')
            ->assertSee('Cetak Kartu Ujian (PDF)');
    }

    public function test_kartu_ujian_shows_arrears_warning_when_spp_unpaid(): void
    {
        $response = $this->actingAs($this->userSiswa2)->get(route('web.siswa.kartuUjian', $this->siswa2->id));

        $response->assertStatus(200)
            ->assertSee('Syarat Keuangan Belum Terpenuhi')
            ->assertSee('Kartu Ujian Belum Dapat Dicetak')
            ->assertSee('Total Tunggakan:');
    }
}
