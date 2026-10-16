<?php

namespace Tests\Feature\Academic;

use App\Models\BapPerkuliahan;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\Mapel;
use App\Models\MataKuliah;
use App\Models\MataKuliahPrasyarat;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\Academic\SmartKrsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartKrsInvariantsTest extends TestCase
{
    use RefreshDatabase;

    protected Fakultas $fakultas;
    protected ProgramStudi $prodi;
    protected Kurikulum $kurikulum;
    protected TahunAkademik $tahunAkademik;
    protected Kelas $kelas;
    protected Spp $spp;
    protected Guru $guru;
    protected Dosen $dosen;
    protected Mapel $mapel;
    protected User $adminUser;
    protected User $dosenUser;
    protected Siswa $siswa;
    protected Mahasiswa $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakultas = Fakultas::create([
            'kode_fakultas' => 'FTI',
            'nama_fakultas' => 'Fakultas Teknologi Informasi',
        ]);

        $this->prodi = ProgramStudi::create([
            'id_fakultas' => $this->fakultas->id,
            'kode_prodi' => 'IF',
            'nama_prodi' => 'Informatika',
        ]);

        $this->kurikulum = Kurikulum::create([
            'id_prodi' => $this->prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE 2026',
            'tahun_mulai' => 2026,
            'is_active' => true,
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(6)->toDateString(),
            'tgl_krs_mulai' => now()->subDay()->toDateString(),
            'tgl_krs_selesai' => now()->addMonth()->toDateString(),
            'tgl_nilai_mulai' => now()->toDateString(),
            'tgl_nilai_selesai' => now()->addMonths(6)->toDateString(),
            'is_active' => true,
        ]);

        $this->kelas = Kelas::create(['nama_kelas' => 'IF-3A', 'kompetensi_keahlian' => 'RPL']);
        $this->spp = Spp::create(['tahun' => 2026, 'nominal' => 500000]);

        $this->guru = Guru::create([
            'nip' => '198501012010011001',
            'nama_guru' => 'Dr. Budi Santoso, M.T.',
            'jenis_kelamin' => 'L',
        ]);

        $this->dosen = Dosen::create([
            'nip' => '198501012010011001',
            'nidn' => '0001018501',
            'nama_dosen' => 'Dr. Budi Santoso, M.T.',
            'id_prodi' => $this->prodi->id,
        ]);

        $this->mapel = Mapel::create([
            'kode_mapel' => 'IF101',
            'nama_mapel' => 'Algoritma Pemrograman',
            'sks' => 3,
            'semester' => 'Ganjil',
            'kkm' => 75,
        ]);

        $this->adminUser = User::create([
            'name' => 'Administrator',
            'email' => 'admin_test@siakad.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'superadmin',
            'is_active' => true,
            'consent_pdp_at' => now(),
        ]);

        $this->dosenUser = User::create([
            'name' => 'Dr. Budi Santoso, M.T.',
            'email' => 'dosen_budi@siakad.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'dosen',
            'id_guru' => $this->guru->id,
            'id_dosen' => $this->dosen->id,
            'is_active' => true,
            'consent_pdp_at' => now(),
        ]);

        $this->siswa = Siswa::create([
            'nisn' => '109240940001',
            'nis' => '109240940001',
            'nama' => 'Muhammad Fauzan',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Jl. Kampus No. 1, Bandung',
            'no_telp' => '08123456789',
            'status_kelulusan' => 'Aktif',
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'nim' => '109240940001',
            'nisn' => '109240940001',
            'nama' => 'Muhammad Fauzan',
            'id_prodi' => $this->prodi->id,
            'id_dosen_pa' => $this->dosen->id,
            'status_kelulusan' => 'Aktif',
        ]);
    }

    public function test_user_dual_domain_linkage_and_auto_bridging(): void
    {
        $uniqueSuffix = time() . rand(100, 999);
        $nim = '999' . $uniqueSuffix;

        $siswa = Siswa::create([
            'nisn' => $nim,
            'nis' => $nim,
            'nama' => 'Student Test ' . $uniqueSuffix,
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Jl. Mahasiswa No. 10, Bandung',
            'no_telp' => '08198765432',
            'status_kelulusan' => 'Aktif',
        ]);

        $mahasiswa = Mahasiswa::create([
            'nim' => $nim,
            'nisn' => $nim,
            'nama' => 'Student Test ' . $uniqueSuffix,
            'id_prodi' => $this->prodi->id,
            'status_kelulusan' => 'Aktif',
        ]);

        // Create user with only id_siswa
        $user = User::create([
            'name' => 'Student Test ' . $uniqueSuffix,
            'email' => "student_{$uniqueSuffix}@univ.ac.id",
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
            'id_siswa' => $siswa->id,
            'is_active' => true,
        ]);

        // Booted hook should auto-bridge id_mahasiswa
        $this->assertEquals($mahasiswa->id, $user->id_mahasiswa);
        $this->assertEquals($siswa->id, $user->student_id);
        $this->assertEquals($siswa->id, $user->siswa->id);
        $this->assertEquals($mahasiswa->id, $user->mahasiswa->id);
    }

    public function test_smart_krs_dynamic_sks_cap_matrix(): void
    {
        $service = app(SmartKrsService::class);

        // SN-Dikti bracket verification
        $this->assertEquals(24, $service->calculateMaxSksCap(4.00));
        $this->assertEquals(24, $service->calculateMaxSksCap(3.00));
        $this->assertEquals(22, $service->calculateMaxSksCap(2.99));
        $this->assertEquals(22, $service->calculateMaxSksCap(2.50));
        $this->assertEquals(20, $service->calculateMaxSksCap(2.49));
        $this->assertEquals(20, $service->calculateMaxSksCap(2.00));
        $this->assertEquals(18, $service->calculateMaxSksCap(1.99));
        $this->assertEquals(18, $service->calculateMaxSksCap(0.00));
    }

    public function test_smart_krs_prerequisite_with_grade_threshold(): void
    {
        $service = app(SmartKrsService::class);

        $mkDasar = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'MK-TEST-01',
            'nama_mk' => 'Algoritma Dasar',
            'sks_total' => 3,
        ]);

        $mkLanjut = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'MK-TEST-02',
            'nama_mk' => 'Struktur Data Lanjut',
            'sks_total' => 3,
        ]);

        // Set prerequisite: MK-TEST-02 requires MK-TEST-01 with min grade C
        $prereq = MataKuliahPrasyarat::create([
            'id_mk' => $mkLanjut->id,
            'id_mk_prasyarat' => $mkDasar->id,
            'nilai_minimum' => 'C',
        ]);

        // 1. Without prior enrollment -> fail
        [$passedNoEnr, $reasonNoEnr] = $service->checkPrerequisites($this->siswa->id, $mkLanjut->id);
        $this->assertFalse($passedNoEnr);
        $this->assertStringContainsString('belum memenuhi mata kuliah prasyarat', $reasonNoEnr);

        // 2. Prior enrollment with grade D (bobot 1.0) -> fail because min is C (2.0)
        $pastKrs = Krs::create([
            'id_siswa' => $this->siswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'max_sks_diizinkan' => 24,
            'total_sks_diambil' => 3,
            'status_krs' => 'Disetujui',
        ]);

        $pastClass = KelasKuliah::create([
            'id_mk' => $mkDasar->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'ALGO-TEST-A',
            'kuota_maksimal' => 30,
            'total_terisi' => 1,
        ]);

        $detail = KrsDetail::create([
            'id_krs' => $pastKrs->id,
            'id_kelas_kuliah' => $pastClass->id,
            'status_ambil' => 'Baru',
            'nilai_akhir_huruf' => 'D',
            'bobot_mutu' => 1.00,
            'is_lulus' => true,
            'is_published' => true,
        ]);

        [$passedLow, $reasonLow] = $service->checkPrerequisites($this->siswa->id, $mkLanjut->id);
        $this->assertFalse($passedLow);
        $this->assertStringContainsString('belum memenuhi batas minimal (C)', $reasonLow);

        // 3. Prior enrollment with grade B (bobot 3.0) -> pass
        $detail->update([
            'nilai_akhir_huruf' => 'B',
            'bobot_mutu' => 3.00,
            'is_lulus' => true,
            'is_published' => true,
        ]);

        [$passedOk, $reasonOk] = $service->checkPrerequisites($this->siswa->id, $mkLanjut->id);
        $this->assertTrue($passedOk);
        $this->assertNull($reasonOk);
    }

    public function test_schedule_clash_detection_in_web_jadwal_controller(): void
    {
        // 1. Create initial schedule: Senin 08:00 - 10:00
        $initial = JadwalPelajaran::create([
            'id_kelas' => $this->kelas->id,
            'id_mapel' => $this->mapel->id,
            'id_guru' => $this->guru->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'ruangan' => 'R-LAB-TEST-01',
        ]);

        // 2. Attempt to create overlapping schedule with same teacher: Senin 09:00 - 11:00
        $otherKelas = Kelas::create(['nama_kelas' => 'IF-3B', 'kompetensi_keahlian' => 'RPL']);

        $response = $this->actingAs($this->adminUser)
            ->from('/web/jadwal/create')
            ->post(route('web.jadwal.store'), [
                'id_kelas' => $otherKelas->id,
                'id_mapel' => $this->mapel->id,
                'id_guru' => $this->guru->id,
                'hari' => 'Senin',
                'jam_mulai' => '09:00',
                'jam_selesai' => '11:00',
                'ruangan' => 'R-LAB-DIFFERENT',
            ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Bentrok Jadwal', session('error'));
    }

    public function test_semester_grading_window_and_deadline_lock(): void
    {
        $taClosed = TahunAkademik::create([
            'kode_tahun' => 'TEST-CLOSED-' . time(),
            'nama_tahun' => '2024/2025',
            'semester' => 'Genap',
            'tgl_mulai' => now()->subMonths(6)->toDateString(),
            'tgl_selesai' => now()->subMonths(2)->toDateString(),
            'tgl_nilai_mulai' => now()->subMonths(4)->toDateString(),
            'tgl_nilai_selesai' => now()->subMonths(1)->toDateString(), // closed 1 month ago
            'is_active' => false,
        ]);

        $this->assertFalse($taClosed->isGradingOpen());

        // Test with active semester where deadline has passed
        $taActiveExpired = TahunAkademik::create([
            'kode_tahun' => 'TEST-EXPIRED-' . time(),
            'nama_tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->subMonths(3)->toDateString(),
            'tgl_selesai' => now()->addMonths(1)->toDateString(),
            'tgl_nilai_mulai' => now()->subMonths(2)->toDateString(),
            'tgl_nilai_selesai' => now()->subDay()->toDateString(), // expired yesterday
            'is_active' => true,
        ]);

        $this->assertFalse($taActiveExpired->isGradingOpen());
    }

    public function test_dosen_krs_approval_atomic_transaction_and_authorization(): void
    {
        $krs = Krs::create([
            'id_siswa' => $this->siswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'max_sks_diizinkan' => 24,
            'total_sks_diambil' => 6,
            'status_krs' => 'Diajukan',
            'id_dosen_wali' => $this->guru->id,
        ]);

        // Authorized approval by assigned advisor
        $response = $this->actingAs($this->dosenUser)
            ->from(route('siakad.dosen.krs.index'))
            ->post(route('siakad.dosen.krs.approve', $krs->id), [
                'catatan_pembimbing' => 'KRS disetujui untuk semester ini.',
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('Disetujui', $krs->fresh()->status_krs);

        // Re-approving already approved KRS returns info
        $repeatResponse = $this->actingAs($this->dosenUser)
            ->from(route('siakad.dosen.krs.index'))
            ->post(route('siakad.dosen.krs.approve', $krs->id));
        $repeatResponse->assertSessionHas('info');

        // Test unauthorized lecturer gets 403
        $otherGuru = Guru::create([
            'nip' => '199001012015011002',
            'nama_guru' => 'Dosen Lain, M.Kom.',
            'jenis_kelamin' => 'L',
        ]);

        $otherLecturer = User::create([
            'name' => 'Other Lecturer',
            'email' => 'other_' . time() . '@siakad.ac.id',
            'password' => bcrypt('password123'),
            'role' => 'dosen',
            'id_guru' => $otherGuru->id,
            'is_active' => true,
            'consent_pdp_at' => now(),
        ]);

        $unauthorizedResponse = $this->actingAs($otherLecturer)->post(route('siakad.dosen.krs.reject', $krs->id), [
            'catatan_pembimbing' => 'Tolak',
        ]);
        $unauthorizedResponse->assertStatus(403);
    }

    public function test_dosen_bap_16_meetings_enforcement_before_finalization(): void
    {
        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'MK-BAP-01',
            'nama_mk' => 'Pemrograman Web Lanjut',
            'sks_total' => 3,
        ]);

        $kelas = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_dosen' => $this->dosen->id,
            'id_guru' => $this->guru->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'BAP-TEST-CLASS',
            'kuota_maksimal' => 30,
            'total_terisi' => 5,
        ]);

        // Populate 15 BAP meetings (less than 16)
        for ($i = 1; $i <= 15; $i++) {
            BapPerkuliahan::create([
                'id_kelas_kuliah' => $kelas->id,
                'id_guru' => $this->guru->id,
                'pertemuan_ke' => $i,
                'tanggal_pelaksanaan' => now()->subDays(16 - $i)->toDateString(),
                'jam_mulai_real' => '08:00',
                'jam_selesai_real' => '10:00',
                'materi_pembahasan' => "Materi Perkuliahan Pertemuan #{$i}",
                'total_mahasiswa_hadir' => 25,
                'status_verifikasi' => 'Tersubmit',
            ]);
        }

        $this->assertFalse($kelas->hasCompletedAllMeetings());

        // Attempting to finalize course with 15 meetings must fail
        $response = $this->actingAs($this->dosenUser)->post(route('siakad.dosen.bap.finalize', $kelas->id));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('minimal 16 pertemuan', session('error'));

        // Add 16th meeting
        BapPerkuliahan::create([
            'id_kelas_kuliah' => $kelas->id,
            'id_guru' => $this->guru->id,
            'pertemuan_ke' => 16,
            'tanggal_pelaksanaan' => now()->toDateString(),
            'jam_mulai_real' => '08:00',
            'jam_selesai_real' => '10:00',
            'materi_pembahasan' => 'Ujian Akhir Semester (UAS)',
            'total_mahasiswa_hadir' => 25,
            'status_verifikasi' => 'Tersubmit',
        ]);

        $this->assertTrue($kelas->hasCompletedAllMeetings());

        // Finalize course with 16 meetings must succeed
        $successResponse = $this->actingAs($this->dosenUser)->post(route('siakad.dosen.bap.finalize', $kelas->id));
        $successResponse->assertSessionHas('success');
    }
}
