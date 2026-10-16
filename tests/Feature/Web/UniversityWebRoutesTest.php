<?php

namespace Tests\Feature\Web;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniversityWebRoutesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Kelas $kelas;
    private Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->kelas = Kelas::create(['nama_kelas' => 'TI-3A', 'kompetensi_keahlian' => 'Teknik Informatika']);
        $this->spp = Spp::create(['tahun' => 2026, 'nominal' => 3500000]);
    }

    public function test_admin_can_access_mahasiswa_routes(): void
    {
        // Index
        $response = $this->actingAs($this->admin)->get(route('web.mahasiswa.index'));
        $response->assertStatus(200);

        // Create page
        $response = $this->actingAs($this->admin)->get(route('web.mahasiswa.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->admin)->post(route('web.mahasiswa.store'), [
            'nisn' => '1234567890',
            'nis' => 'NIM12345',
            'nama' => 'Budi Mahasiswa',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Kampus Terpadu',
            'no_telp' => '081234567890',
            'id_spp' => $this->spp->id,
        ]);
        $response->assertRedirect(route('web.mahasiswa.index'));
        $this->assertDatabaseHas('siswas', ['nisn' => '1234567890']);

        $mhs = Siswa::where('nisn', '1234567890')->first();

        // Show
        $response = $this->actingAs($this->admin)->get(route('web.mahasiswa.show', $mhs->id));
        $response->assertStatus(200);

        // Edit
        $response = $this->actingAs($this->admin)->get(route('web.mahasiswa.edit', $mhs->id));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->admin)->put(route('web.mahasiswa.update', $mhs->id), [
            'nisn' => '1234567890',
            'nis' => 'NIM12345',
            'nama' => 'Budi Mahasiswa Updated',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Kampus Terpadu',
            'no_telp' => '081234567890',
            'id_spp' => $this->spp->id,
        ]);
        $response->assertRedirect(route('web.mahasiswa.index'));
        $this->assertDatabaseHas('siswas', ['nama' => 'Budi Mahasiswa Updated']);

        // Surat tagihan
        $response = $this->actingAs($this->admin)->get(route('web.mahasiswa.suratTagihan', $mhs->id));
        $response->assertStatus(200);

        // Kartu ujian
        $response = $this->actingAs($this->admin)->get(route('web.mahasiswa.kartuUjian', $mhs->id));
        $response->assertStatus(200);
    }

    public function test_admin_can_access_dosen_routes(): void
    {
        // Index
        $response = $this->actingAs($this->admin)->get(route('web.dosen.index'));
        $response->assertStatus(200);

        // Create page
        $response = $this->actingAs($this->admin)->get(route('web.dosen.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->admin)->post(route('web.dosen.store'), [
            'nip' => '198001012005011001',
            'nama_guru' => 'Dr. Hendra, M.Kom.',
            'jenis_kelamin' => 'L',
            'no_telp' => '081298765432',
            'email' => 'hendra@univ.ac.id',
            'alamat' => 'Kota Pendidikan',
        ]);
        $response->assertRedirect(route('web.dosen.index'));
        $this->assertDatabaseHas('gurus', ['nip' => '198001012005011001']);

        $dosen = Guru::where('nip', '198001012005011001')->first();

        // Edit
        $response = $this->actingAs($this->admin)->get(route('web.dosen.edit', $dosen->id));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->admin)->put(route('web.dosen.update', $dosen->id), [
            'nip' => '198001012005011001',
            'nama_guru' => 'Prof. Dr. Hendra, M.Kom.',
            'jenis_kelamin' => 'L',
            'no_telp' => '081298765432',
            'email' => 'hendra@univ.ac.id',
            'alamat' => 'Kota Pendidikan',
        ]);
        $response->assertRedirect(route('web.dosen.index'));
        $this->assertDatabaseHas('gurus', ['nama_guru' => 'Prof. Dr. Hendra, M.Kom.']);
    }

    public function test_admin_can_access_matakuliah_routes(): void
    {
        // Index
        $response = $this->actingAs($this->admin)->get(route('web.matakuliah.index'));
        $response->assertStatus(200);

        // Create page
        $response = $this->actingAs($this->admin)->get(route('web.matakuliah.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->admin)->post(route('web.matakuliah.store'), [
            'kode_mapel' => 'MK-101',
            'nama_mapel' => 'Algoritma & Pemrograman',
            'kelompok' => 'Kejuruan',
            'kkm' => 70,
        ]);
        $response->assertRedirect(route('web.matakuliah.index'));
        $this->assertDatabaseHas('mapels', ['kode_mapel' => 'MK-101']);

        $mk = Mapel::where('kode_mapel', 'MK-101')->first();

        // Edit
        $response = $this->actingAs($this->admin)->get(route('web.matakuliah.edit', $mk->id));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->admin)->put(route('web.matakuliah.update', $mk->id), [
            'kode_mapel' => 'MK-101',
            'nama_mapel' => 'Algoritma Lanjut',
            'kelompok' => 'Kejuruan',
            'kkm' => 75,
        ]);
        $response->assertRedirect(route('web.matakuliah.index'));
        $this->assertDatabaseHas('mapels', ['nama_mapel' => 'Algoritma Lanjut']);
    }

    public function test_admin_can_access_ukt_routes(): void
    {
        // Index
        $response = $this->actingAs($this->admin)->get(route('web.ukt.index'));
        $response->assertStatus(200);

        // Create page
        $response = $this->actingAs($this->admin)->get(route('web.ukt.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->admin)->post(route('web.ukt.store'), [
            'tahun' => 2027,
            'nominal' => 4500000,
        ]);
        $response->assertRedirect(route('web.ukt.index'));
        $this->assertDatabaseHas('spps', ['tahun' => 2027]);

        $ukt = Spp::where('tahun', 2027)->first();

        // Edit
        $response = $this->actingAs($this->admin)->get(route('web.ukt.edit', $ukt->id));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->admin)->put(route('web.ukt.update', $ukt->id), [
            'tahun' => 2027,
            'nominal' => 4800000,
        ]);
        $response->assertRedirect(route('web.ukt.index'));
        $this->assertDatabaseHas('spps', ['nominal' => 4800000]);
    }

    public function test_can_print_khs_via_university_route(): void
    {
        $siswa = Siswa::create([
            'nisn' => '9998887776',
            'nis' => 'NIM999',
            'nama' => 'Mahasiswa KHS',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Kampus',
            'no_telp' => '08111222333',
            'id_spp' => $this->spp->id,
        ]);

        $guru = Guru::create([
            'nip' => '19876543210',
            'nama_guru' => 'Dosen Pengampu',
            'jenis_kelamin' => 'L',
        ]);

        $mapel = Mapel::create([
            'kode_mapel' => 'KHS-01',
            'nama_mapel' => 'Basis Data',
            'kelompok' => 'Kejuruan',
            'kkm' => 70,
        ]);

        Nilai::create([
            'id_siswa' => $siswa->id,
            'id_guru' => $guru->id,
            'id_mapel' => $mapel->id,
            'semester' => 'Ganjil',
            'tahun_ajaran' => '2026/2027',
            'nilai_tugas' => 85,
            'nilai_uts' => 80,
            'nilai_uas' => 90,
            'nilai_akhir' => 85,
            'predikat' => 'A',
        ]);

        $response = $this->actingAs($this->admin)->get(route('web.nilai.khs', $siswa->id));
        $response->assertStatus(200);
        $response->assertSee('KARTU HASIL STUDI');
    }
}
