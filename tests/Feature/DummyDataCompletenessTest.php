<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\Siswa;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DummyDataCompletenessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_seeds_complete_dummy_data_with_zero_empty_essential_fields(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Siswa completeness
        $siswas = Siswa::all();
        $this->assertGreaterThan(0, $siswas->count());
        foreach ($siswas as $s) {
            $this->assertNotEmpty($s->nisn, "NISN siswa ID {$s->id} kosong");
            $this->assertNotEmpty($s->nis, "NIS siswa ID {$s->id} kosong");
            $this->assertNotEmpty($s->nama, "Nama siswa ID {$s->id} kosong");
            $this->assertNotNull($s->id_kelas, "Kelas siswa ID {$s->id} kosong");
            $this->assertNotEmpty($s->alamat, "Alamat siswa ID {$s->id} kosong");
            $this->assertNotEmpty($s->no_telp, "No telp siswa ID {$s->id} kosong");
            $this->assertNotNull($s->id_spp, "Tarif SPP siswa ID {$s->id} kosong");
            $this->assertNotEmpty($s->nik, "NIK siswa ID {$s->id} kosong");
            $this->assertNotEmpty($s->nama_ibu_kandung, "Nama ibu kandung siswa ID {$s->id} kosong");
            $this->assertNotEmpty($s->no_hp_wali, "No HP wali siswa ID {$s->id} kosong");
            $this->assertNotNull($s->consent_pdp_at, "Consent PDP siswa ID {$s->id} kosong");
            $this->assertNotEmpty($s->consent_pdp_ip, "IP consent PDP siswa ID {$s->id} kosong");
        }

        // 2. Guru completeness
        $gurus = Guru::all();
        $this->assertGreaterThan(0, $gurus->count());
        foreach ($gurus as $g) {
            $this->assertNotEmpty($g->nip, "NIP guru ID {$g->id} kosong");
            $this->assertNotEmpty($g->nik, "NIK guru ID {$g->id} kosong");
            $this->assertNotEmpty($g->nama_guru, "Nama guru ID {$g->id} kosong");
            $this->assertNotEmpty($g->jenis_kelamin, "Gender guru ID {$g->id} kosong");
            $this->assertNotEmpty($g->no_telp, "No telp guru ID {$g->id} kosong");
            $this->assertNotEmpty($g->email, "Email guru ID {$g->id} kosong");
            $this->assertNotEmpty($g->alamat, "Alamat guru ID {$g->id} kosong");
        }

        // 3. Mapel completeness
        $mapels = Mapel::all();
        $this->assertGreaterThanOrEqual(10, $mapels->count());
        foreach ($mapels as $m) {
            $this->assertNotEmpty($m->kode_mapel, "Kode mapel ID {$m->id} kosong");
            $this->assertNotEmpty($m->nama_mapel, "Nama mapel ID {$m->id} kosong");
            $this->assertNotEmpty($m->kelompok, "Kelompok mapel ID {$m->id} kosong");
            $this->assertGreaterThan(0, $m->kkm, "KKM mapel ID {$m->id} kosong");
            $this->assertGreaterThan(0, $m->semester, "Semester mapel ID {$m->id} kosong");
        }

        // 4. Jadwal Pelajaran (All 6 days present)
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        foreach ($days as $day) {
            $count = JadwalPelajaran::where('hari', $day)->count();
            $this->assertGreaterThan(0, $count, "Jadwal untuk hari {$day} kosong!");
        }

        $jadwals = JadwalPelajaran::all();
        foreach ($jadwals as $j) {
            $this->assertNotEmpty($j->jam_mulai, "Jam mulai jadwal ID {$j->id} kosong");
            $this->assertNotEmpty($j->jam_selesai, "Jam selesai jadwal ID {$j->id} kosong");
            $this->assertNotEmpty($j->ruangan, "Ruangan jadwal ID {$j->id} kosong");
            $this->assertNotNull($j->guru, "Guru pada jadwal ID {$j->id} kosong");
            $this->assertNotNull($j->mapel, "Mapel pada jadwal ID {$j->id} kosong");
            $this->assertNotNull($j->kelas, "Kelas pada jadwal ID {$j->id} kosong");
        }

        // 5. Nilai completeness
        $nilais = Nilai::all();
        $this->assertGreaterThan(0, $nilais->count());
        foreach ($nilais as $n) {
            $this->assertNotNull($n->nilai_tugas, "Nilai tugas ID {$n->id} kosong");
            $this->assertNotNull($n->nilai_uts, "Nilai UTS ID {$n->id} kosong");
            $this->assertNotNull($n->nilai_uas, "Nilai UAS ID {$n->id} kosong");
            $this->assertNotNull($n->nilai_akhir, "Nilai akhir ID {$n->id} kosong");
            $this->assertNotEmpty($n->predikat, "Predikat ID {$n->id} kosong");
            $this->assertNotEmpty($n->catatan, "Catatan nilai ID {$n->id} kosong");
        }

        // 6. Pembayaran completeness
        $pembayarans = Pembayaran::all();
        $this->assertGreaterThan(0, $pembayarans->count());
        foreach ($pembayarans as $p) {
            $this->assertGreaterThan(0, $p->jumlah_bayar, "Jumlah bayar pembayaran ID {$p->id} kosong");
            $this->assertNotEmpty($p->bulan_dibayar, "Bulan bayar pembayaran ID {$p->id} kosong");
            $this->assertNotEmpty($p->tahun_dibayar, "Tahun bayar pembayaran ID {$p->id} kosong");
            $this->assertNotEmpty($p->tgl_bayar, "Tanggal bayar pembayaran ID {$p->id} kosong");
            $this->assertNotNull($p->siswa, "Siswa pada pembayaran ID {$p->id} kosong");
        }
    }
}
