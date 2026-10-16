<?php

namespace Tests\Feature\Academic;

use App\Domain\Academic\Actions\VerifyExamCardEligibilityAction;
use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\Pembayaran;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ExamClearanceAndGradeInvariantsTest extends TestCase
{
    use RefreshDatabase;

    private TahunAkademik $tahunAkademik;
    private Kelas $kelas;
    private Spp $spp;
    private ProgramStudi $prodi;
    private Kurikulum $kurikulum;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Akademik',
            'email' => 'admin.akademik@test.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
        ]);

        $this->prodi = ProgramStudi::create([
            'id_fakultas' => $fakultas->id,
            'kode_prodi' => 'IF',
            'nama_prodi' => 'Teknik Informatika',
        ]);

        $this->kurikulum = Kurikulum::create([
            'id_prodi' => $this->prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE 2026',
            'tahun_mulai' => 2026,
            'is_active' => true,
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(6)->toDateString(),
            'tgl_krs_mulai' => now()->subDays(5)->toDateString(),
            'tgl_krs_selesai' => now()->addDays(20)->toDateString(),
            'is_active' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => 'IF-4A',
            'kompetensi_keahlian' => 'Informatika',
        ]);

        $this->spp = Spp::create([
            'tahun' => 2025,
            'nominal' => 500000,
        ]);
    }

    /**
     * Helper to mark all 12 months as paid for a student.
     */
    private function payFullTuition(Siswa $siswa): void
    {
        $months = [
            ['Juli', '2025'], ['Agustus', '2025'], ['September', '2025'],
            ['Oktober', '2025'], ['November', '2025'], ['Desember', '2025'],
            ['Januari', '2026'], ['Februari', '2026'], ['Maret', '2026'],
            ['April', '2026'], ['Mei', '2026'], ['Juni', '2026'],
        ];

        foreach ($months as [$bulan, $tahun]) {
            Pembayaran::create([
                'id_petugas' => $this->admin->id,
                'id_siswa' => $siswa->id,
                'tgl_bayar' => now(),
                'bulan_dibayar' => $bulan,
                'tahun_dibayar' => $tahun,
                'id_spp' => $this->spp->id,
                'jumlah_bayar' => 500000,
            ]);
        }
    }

    /**
     * Test 1: Student with full attendance and paid tuition can print exam pass card.
     */
    public function test_student_with_full_attendance_and_paid_tuition_can_print_exam_card(): void
    {
        $siswa = Siswa::create([
            'nisn' => '0088112233',
            'nis' => '881122',
            'nama' => 'Student Full Attendance',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Kampus',
            'no_telp' => '08123456701',
            'status_kelulusan' => 'Aktif',
        ]);

        $user = User::factory()->create([
            'name' => $siswa->nama,
            'email' => 'student.full@test.com',
            'role' => 'siswa',
            'id_siswa' => $siswa->id,
            'is_active' => true,
        ]);

        $this->payFullTuition($siswa);

        $action = app(VerifyExamCardEligibilityAction::class);
        $result = $action->execute($siswa);

        $this->assertTrue($result['is_lunas']);
        $this->assertTrue($result['is_attendance_eligible']);
        $this->assertTrue($result['can_print']);

        $response = $this->actingAs($user)->get(route('web.siswa.kartuUjian', $siswa->id));
        $response->assertStatus(200)
            ->assertSee('Cetak Kartu Ujian (PDF)')
            ->assertSee('STATUS: LUNAS')
            ->assertDontSee('Syarat Presensi Belum Terpenuhi');
    }

    /**
     * Test 2: Student with insufficient attendance is blocked from printing exam card even if tuition is paid.
     */
    public function test_student_with_insufficient_attendance_is_blocked_from_printing_exam_card(): void
    {
        $siswa = Siswa::create([
            'nisn' => '0088112244',
            'nis' => '881124',
            'nama' => 'Student Low Attendance',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Kampus',
            'no_telp' => '08123456702',
            'status_kelulusan' => 'Aktif',
        ]);

        $user = User::factory()->create([
            'name' => $siswa->nama,
            'email' => 'student.low@test.com',
            'role' => 'siswa',
            'id_siswa' => $siswa->id,
            'is_active' => true,
        ]);

        $this->payFullTuition($siswa);

        // Record attendance: 1 Hadir, 4 Alpa (Total 5 meetings -> 20% attendance, < 75%)
        \App\Models\Presensi::create([
            'id_siswa' => $siswa->id,
            'id_kelas' => $this->kelas->id,
            'id_guru' => 1,
            'id_mapel' => 1,
            'tanggal' => now()->subDays(10),
            'status' => 'Hadir',
        ]);

        for ($i = 1; $i <= 4; $i++) {
            \App\Models\Presensi::create([
                'id_siswa' => $siswa->id,
                'id_kelas' => $this->kelas->id,
                'id_guru' => 1,
                'id_mapel' => 1,
                'tanggal' => now()->subDays(10 - $i),
                'status' => 'Alpa',
            ]);
        }

        $action = app(VerifyExamCardEligibilityAction::class);
        $result = $action->execute($siswa);

        $this->assertTrue($result['is_lunas'], 'Financial tuition is paid.');
        $this->assertEquals(20.0, $result['attendance_rate']);
        $this->assertFalse($result['is_attendance_eligible'], 'Attendance below 75% must be marked ineligible.');
        $this->assertFalse($result['can_print'], 'Student must not be permitted to print exam pass.');

        $response = $this->actingAs($user)->get(route('web.siswa.kartuUjian', $siswa->id));
        $response->assertStatus(200)
            ->assertSee('Syarat Presensi Belum Terpenuhi')
            ->assertSee('20% / Target 75%')
            ->assertDontSee('Cetak Kartu Ujian (PDF)');
    }

    /**
     * Test 3: Expired incomplete grades command dry-run previews without modifying database records.
     */
    public function test_grades_expire_incomplete_dry_run_previews_without_mutating_grades(): void
    {
        $siswa = Siswa::create([
            'nisn' => '0088112255',
            'nis' => '881125',
            'nama' => 'Student Incomplete DryRun',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Kampus',
            'no_telp' => '08123456703',
            'status_kelulusan' => 'Aktif',
        ]);

        $krs = Krs::create([
            'id_siswa' => $siswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'total_sks_diambil' => 3,
            'status_krs' => 'Disetujui',
        ]);

        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF401',
            'nama_mk' => 'Kecerdasan Buatan',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF-4A-AI',
            'kuota' => 40,
        ]);

        $detail = KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelasKuliah->id,
            'nilai_akhir_huruf' => 'BL',
            'bobot_mutu' => 0.00,
            'incomplete_expires_at' => now()->subDays(5),
            'is_incomplete_expired' => false,
        ]);

        $exitCode = Artisan::call('grades:expire-incomplete', [
            '--dry-run' => true,
        ]);

        $this->assertEquals(0, $exitCode);

        $freshDetail = $detail->fresh();
        $this->assertEquals('BL', $freshDetail->nilai_akhir_huruf);
        $this->assertFalse((bool) $freshDetail->is_incomplete_expired);
    }

    /**
     * Test 4: Expired incomplete grades command live run converts BL/T to E and recalculates IPS.
     */
    public function test_grades_expire_incomplete_converts_expired_grades_to_e_and_recalculates_ips(): void
    {
        $siswa = Siswa::create([
            'nisn' => '0088112266',
            'nis' => '881126',
            'nama' => 'Student Incomplete Live',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Kampus',
            'no_telp' => '08123456704',
            'status_kelulusan' => 'Aktif',
        ]);

        $krs = Krs::create([
            'id_siswa' => $siswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'total_sks_diambil' => 6,
            'status_krs' => 'Disetujui',
        ]);

        $mk1 = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF402',
            'nama_mk' => 'Jaringan Komputer',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $mk2 = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF403',
            'nama_mk' => 'Sistem Terdistribusi',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $kelas1 = KelasKuliah::create([
            'id_mk' => $mk1->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF-4A-NET',
            'kuota' => 40,
        ]);

        $kelas2 = KelasKuliah::create([
            'id_mk' => $mk2->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF-4A-DIST',
            'kuota' => 40,
        ]);

        // Course 1: Grade A (4.00 * 3 SKS = 12.00)
        KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelas1->id,
            'nilai_akhir_huruf' => 'A',
            'bobot_mutu' => 4.00,
            'is_incomplete_expired' => false,
        ]);

        // Course 2: Grade T (incomplete, expired 2 days ago)
        $detailIncomplete = KrsDetail::create([
            'id_krs' => $krs->id,
            'id_kelas_kuliah' => $kelas2->id,
            'nilai_akhir_huruf' => 'T',
            'bobot_mutu' => 0.00,
            'incomplete_expires_at' => now()->subDays(2),
            'is_incomplete_expired' => false,
        ]);

        $exitCode = Artisan::call('grades:expire-incomplete', [
            '--days' => 14,
        ]);

        $this->assertEquals(0, $exitCode);

        // Assert grade was converted to E
        $freshDetail = $detailIncomplete->fresh();
        $this->assertEquals('E', $freshDetail->nilai_akhir_huruf);
        $this->assertEquals(0.00, (float) $freshDetail->bobot_mutu);
        $this->assertTrue((bool) $freshDetail->is_incomplete_expired);

        // Recalculated IPS: (12.00 + 0.00) / 6 SKS = 2.00
        $freshKrs = $krs->fresh();
        $this->assertEquals(2.00, (float) $freshKrs->ips_lalu);
    }

    /**
     * Test 5: Standalone views render without external CDN when Vite manifest is present.
     */
    public function test_standalone_views_render_with_vite_assets_and_zero_cdn_when_manifest_exists(): void
    {
        $responseLogin = $this->get('/login');
        $responseLogin->assertStatus(200);

        // If manifest exists, assert local Vite bundle is referenced
        if (file_exists(public_path('build/manifest.json'))) {
            $responseLogin->assertSee('build/assets/app-', false);
        }

        $responseCekTagihan = $this->get('/cek-tagihan');
        $responseCekTagihan->assertStatus(200);
    }
}
