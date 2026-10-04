<?php

namespace Tests\Feature\Web;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\Presensi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafeDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Kelas $kelas;
    protected Spp $spp;
    protected Guru $guru;
    protected Mapel $mapel;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->kelas = Kelas::create(['nama_kelas' => 'X RPL 1', 'kompetensi_keahlian' => 'RPL']);
        $this->spp = Spp::create(['tahun' => 2026, 'nominal' => 250000]);

        $this->guru = Guru::create([
            'nip' => '199001012020121001',
            'nama_guru' => 'Siti Nurhaliza, M.Pd.',
            'jenis_kelamin' => 'P',
        ]);

        $this->mapel = Mapel::create([
            'kode_mapel' => 'BIG-01',
            'nama_mapel' => 'Bahasa Inggris',
            'kelompok' => 'Wajib A',
            'kkm' => 75,
        ]);

        $this->siswa = Siswa::create([
            'nisn' => '0088888881',
            'nis' => '4001',
            'nama' => 'Dimas Anggara',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Yogyakarta',
            'no_telp' => '0855555555',
        ]);
    }

    public function test_cannot_delete_kelas_with_active_students(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('web.kelas.destroy', $this->kelas->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kelas', ['id' => $this->kelas->id]);
    }

    public function test_cannot_delete_spp_with_active_students(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('web.spp.destroy', $this->spp->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('spps', ['id' => $this->spp->id]);
    }

    public function test_cannot_delete_guru_with_active_schedule(): void
    {
        JadwalPelajaran::create([
            'id_kelas' => $this->kelas->id,
            'id_mapel' => $this->mapel->id,
            'id_guru' => $this->guru->id,
            'hari' => 'Senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('web.guru.destroy', $this->guru->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('gurus', ['id' => $this->guru->id]);
    }

    public function test_cannot_delete_mapel_with_active_schedule(): void
    {
        JadwalPelajaran::create([
            'id_kelas' => $this->kelas->id,
            'id_mapel' => $this->mapel->id,
            'id_guru' => $this->guru->id,
            'hari' => 'Selasa',
            'jam_mulai' => '09:30',
            'jam_selesai' => '11:00',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('web.mapel.destroy', $this->mapel->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('mapels', ['id' => $this->mapel->id]);
    }

    public function test_cannot_delete_siswa_with_payment_history(): void
    {
        Pembayaran::create([
            'id_petugas' => $this->admin->id,
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => now(),
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => '2026',
            'id_spp' => $this->spp->id,
            'jumlah_bayar' => 250000,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('web.siswa.destroy', $this->siswa->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('siswas', ['id' => $this->siswa->id]);
    }
}
