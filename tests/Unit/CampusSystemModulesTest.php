<?php

namespace Tests\Unit;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\TugasAkhir;
use App\Models\User;
use App\Services\Academic\KknService;
use App\Services\Analytics\StudentProfileAnalyticsService;
use App\Services\Thesis\DbptaWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampusSystemModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_kkn_registration_dpl_assignment_and_grading(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'IF-3A', 'kompetensi_keahlian' => 'IF']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 500000]);
        $siswa = Siswa::create([
            'nama' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'nisn' => '109240940090',
            'nis' => '109240940090',
            'alamat' => 'Mustikajaya, Bekasi',
            'no_telp' => '085811362629',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
        ]);
        $dosen = Dosen::create([
            'nidn' => '0022110401',
            'nama_dosen' => 'Dr. Ahmad Fauzi, M.Kom.',
            'jenis_kelamin' => 'L',
            'email' => 'ahmad.fauzi@stmik.ac.id',
            'no_hp' => '081234567899',
        ]);
        $tahun = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => '2026-09-01',
            'tgl_selesai' => '2027-01-31',
            'is_aktif' => true,
        ]);

        $kknService = new KknService();
        $kkn = $kknService->registerKkn($siswa, $tahun->id, 'Kelompok 5', 'Desa Ciamis', 'Ciamis', 'Ciamis');
        $this->assertEquals('SUBMITTED', $kkn->status_pendaftaran);

        $kknApproved = $kknService->assignDpl($kkn->id, $dosen->id);
        $this->assertEquals('APPROVED', $kknApproved->status_pendaftaran);

        $kknGraded = $kknService->gradeKkn($kkn->id, 88.5);
        $this->assertEquals('COMPLETED', $kknGraded->status_pendaftaran);
        $this->assertEquals('A', $kknGraded->nilai_huruf);
    }

    public function test_dbpta_workflow_stages(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'IF-4B', 'kompetensi_keahlian' => 'IF']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 500000]);
        $siswa = Siswa::create([
            'nama' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'nisn' => '109240940090',
            'nis' => '109240940090',
            'alamat' => 'Bekasi',
            'no_telp' => '085811362629',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
        ]);
        $dosen = Dosen::create([
            'nidn' => '0011223344',
            'nama_dosen' => 'Dr. Rina Supriyati, M.T.',
            'jenis_kelamin' => 'P',
            'email' => 'rina@stmik.ac.id',
            'no_hp' => '081234567811',
        ]);

        $dbptaService = new DbptaWorkflowService();

        // 1. Stage Proposal
        $ta = $dbptaService->submitProposal($siswa, 'Rancang Bangun Sistem Informasi Cloud SIAP 5.6', 'Abstrak Skripsi');
        $this->assertEquals('Pengajuan Proposal', $ta->status_skripsi);

        // 2. Stage Bimbingan
        $log = $dbptaService->addBimbinganLog($ta->id, $dosen->id, 'Revisi Bab 1 dan 2 disetujui');
        $this->assertEquals('Disetujui', $log->status_acc);

        // 3. Stage Sidang Yudisium
        $sidang = $dbptaService->registerSidangYudisium($ta->id);
        $this->assertEquals('Belum Sidang', $sidang->hasil_keputusan);

        // 4. Stage Pemberkasan
        $taFinal = $dbptaService->submitPemberkasanFinal($ta->id, 'uploads/skripsi_final.pdf');
        $this->assertEquals('Lulus Yudisium', $taFinal->status_skripsi);
    }

    public function test_student_profile_timeline_and_access_analytics(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'IF-4B', 'kompetensi_keahlian' => 'IF']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 500000]);
        $siswa = Siswa::create([
            'nama' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'nisn' => '109240940090',
            'nis' => '109240940090',
            'alamat' => 'Mustikajaya, Bekasi',
            'no_telp' => '085811362629',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
        ]);
        $user = User::create([
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'email' => 'muhammadluayyi89@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'id_siswa' => $siswa->id,
        ]);

        $analyticsService = new StudentProfileAnalyticsService();
        $timeline = $analyticsService->getStudentActivityTimeline($siswa);
        $accessLogs = $analyticsService->getUserAccessLogAnalytics($user);

        $this->assertIsArray($timeline);
        $this->assertArrayHasKey('total_login', $accessLogs);
        $this->assertEquals('114.10.114.29', $accessLogs['last_access']['ip']);
    }
}
