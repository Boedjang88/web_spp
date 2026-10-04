<?php

namespace Tests\Feature\Api;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_lookup_student_by_valid_nisn(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 250000]);
        $user = User::factory()->create();

        $siswa = Siswa::create([
            'nisn' => '0051234567',
            'nis' => '2122001',
            'nama' => 'Budi Santoso',
            'id_kelas' => $kelas->id,
            'alamat' => 'Jl. Merdeka No. 10',
            'no_telp' => '081234567890',
            'id_spp' => $spp->id,
        ]);

        Pembayaran::create([
            'id_petugas' => $user->id,
            'id_siswa' => $siswa->id,
            'tgl_bayar' => '2025-07-10',
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => '2025',
            'id_spp' => $spp->id,
            'jumlah_bayar' => 250000,
        ]);

        $response = $this->getJson('/api/portal/siswa/0051234567');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.siswa.nama', 'Budi Santoso')
            ->assertJsonPath('data.siswa.nisn', '0051234567')
            ->assertJsonPath('data.ringkasan_keuangan.total_terbayar_rupiah', 250000)
            ->assertJsonPath('data.ringkasan_keuangan.total_transaksi', 1);
    }

    public function test_public_lookup_with_invalid_nisn_returns_404(): void
    {
        $response = $this->getJson('/api/portal/siswa/9999999999');

        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Data mahasiswa dengan NIM / NISN tersebut tidak ditemukan.');
    }
}
