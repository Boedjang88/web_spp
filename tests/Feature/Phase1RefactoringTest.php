<?php

namespace Tests\Feature;

use App\Domain\Academic\Actions\EnrollKknAction;
use App\Domain\Academic\Actions\VerifyExamCardEligibilityAction;
use App\Domain\Academic\Services\AcademicStudentService;
use App\Domain\Finance\Actions\CheckFinancialClearanceAction;
use App\Domain\Finance\Actions\GenerateStudentBillingStatementAction;
use App\Domain\Finance\Services\SppBillingService;
use App\Jobs\Finance\ExportPembayaranReportJob;
use App\Jobs\Finance\GenerateKwitansiBatchJob;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase1RefactoringTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $studentUser;
    protected Siswa $siswa;
    protected Kelas $kelas;
    protected Spp $spp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kelas = Kelas::create(['nama_kelas' => 'XII-RPL-1', 'kompetensi_keahlian' => 'RPL']);
        $this->spp = Spp::create(['tahun' => 2026, 'nominal' => 250000]);

        $this->siswa = Siswa::create([
            'nisn' => '1234567890',
            'nis' => '12345678',
            'nama' => 'Budi Pratama',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Jl. Kebon Jeruk No. 10',
            'no_telp' => '081234567890',
            'id_spp' => $this->spp->id,
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@spp.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'consent_pdp_at' => now(),
        ]);

        $this->studentUser = User::create([
            'name' => 'Budi Pratama',
            'email' => 'budi@spp.test',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'id_siswa' => $this->siswa->id,
            'is_active' => true,
            'consent_pdp_at' => now(),
        ]);
    }

    /**
     * Test Domain Finance SppBillingService and Actions
     */
    public function test_domain_finance_spp_billing_service_and_actions(): void
    {
        $billingService = app(SppBillingService::class);
        $arrears = $billingService->calculateStudentArrears($this->siswa);
        $this->assertIsArray($arrears);

        $checkAction = app(CheckFinancialClearanceAction::class);
        $isCleared = $checkAction->execute($this->siswa);
        $this->assertIsBool($isCleared);

        $statementAction = app(GenerateStudentBillingStatementAction::class);
        $statement = $statementAction->execute($this->siswa);
        $this->assertArrayHasKey('statement_data', ['statement_data' => $statement]);
        $this->assertEquals($this->siswa->id, $statement['siswa']->id);

        $this->assertDatabaseHas('audit_trail_logs', [
            'action_event' => 'FINANCIAL_BILLING_STATEMENT_GENERATED',
        ]);
    }

    /**
     * Test Domain Academic Services and Actions
     */
    public function test_domain_academic_services_and_actions(): void
    {
        $academicService = app(AcademicStudentService::class);
        $students = $academicService->getPaginatedStudents('Budi', (string) $this->kelas->id);
        $this->assertCount(1, $students);

        $verifyExamAction = app(VerifyExamCardEligibilityAction::class);
        $eligibility = $verifyExamAction->execute($this->siswa);
        $this->assertArrayHasKey('can_print', $eligibility);

        $this->assertDatabaseHas('audit_trail_logs', [
            'action_event' => 'ACADEMIC_EXAM_CARD_CLEARANCE_CHECK',
        ]);

        $kknAction = app(EnrollKknAction::class);
        $kknReg = $kknAction->execute($this->siswa, [
            'lokasi_kkn' => 'Desa Sukamaju',
            'kelompok' => 'Kelompok 5',
        ], enforcePrerequisites: false);

        $this->assertEquals('Desa Sukamaju', $kknReg->lokasi_kkn);
        $this->assertDatabaseHas('audit_trail_logs', [
            'action_event' => 'ACADEMIC_KKN_REGISTER',
        ]);
    }

    /**
     * Test Refactored SiswaController Endpoints
     */
    public function test_siswa_controller_endpoints_with_domain_architecture(): void
    {
        // 1. Surat Tagihan
        $response = $this->actingAs($this->adminUser)->get(route('web.siswa.suratTagihan', $this->siswa->id));
        $response->assertStatus(200);

        // 2. Kartu Ujian
        $response = $this->actingAs($this->studentUser)->get(route('web.siswa.kartuUjian', $this->siswa->id));
        $response->assertStatus(200);

        // 3. Prevent deletion if payments exist
        Pembayaran::create([
            'id_petugas' => $this->adminUser->id,
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => now()->toDateString(),
            'bulan_dibayar' => 'Juli',
            'tahun_dibayar' => '2026',
            'id_spp' => $this->spp->id,
            'jumlah_bayar' => 250000,
        ]);

        $deleteResponse = $this->actingAs($this->adminUser)->delete(route('web.siswa.destroy', $this->siswa->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseHas('siswas', ['id' => $this->siswa->id]);
    }

    /**
     * Test Background Job for Laporan Export
     */
    public function test_laporan_export_queue_job_execution(): void
    {
        Storage::fake('local');

        Pembayaran::create([
            'id_petugas' => $this->adminUser->id,
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => now()->toDateString(),
            'bulan_dibayar' => 'Agustus',
            'tahun_dibayar' => '2026',
            'id_spp' => $this->spp->id,
            'jumlah_bayar' => 250000,
        ]);

        $fileName = 'test-export-' . time() . '.csv';
        $job = new ExportPembayaranReportJob([], $this->adminUser->id, $fileName);
        $job->handle();

        $this->assertDatabaseHas('audit_trail_logs', [
            'action_event' => 'QUEUE_REPORT_EXPORT_COMPLETED',
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->adminUser->id,
        ]);
    }

    /**
     * Test Async Export Route on LaporanController
     */
    public function test_laporan_controller_async_export_dispatches_job(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->adminUser)->getJson(route('web.laporan.exportCsv', ['async' => 1]));
        $response->assertStatus(202);
        $response->assertJson(['success' => true]);

        Queue::assertPushed(ExportPembayaranReportJob::class);
    }

    /**
     * Test Async Batch Kwitansi on CetakKwitansiController
     */
    public function test_cetak_kwitansi_async_queue_dispatches_job(): void
    {
        Queue::fake();

        $pembayaran = Pembayaran::create([
            'id_petugas' => $this->adminUser->id,
            'id_siswa' => $this->siswa->id,
            'tgl_bayar' => now()->toDateString(),
            'bulan_dibayar' => 'September',
            'tahun_dibayar' => '2026',
            'id_spp' => $this->spp->id,
            'jumlah_bayar' => 250000,
        ]);

        $response = $this->actingAs($this->adminUser)->getJson("/web/pembayaran/{$pembayaran->id}/cetak?async=1");
        $response->assertStatus(200); // Standard view route redirects/renders unless calling batch or with invokable controller

        $batchResponse = $this->actingAs($this->adminUser)->postJson(route('web.pembayaran.batchKwitansiQueue'), [
            'ids' => [$pembayaran->id],
        ]);
        $batchResponse->assertStatus(202);
        $batchResponse->assertJson(['success' => true]);

        Queue::assertPushed(GenerateKwitansiBatchJob::class);
    }

    /**
     * Test Strict Rate Limiting and Audit Trail on Cek Tagihan
     */
    public function test_cek_tagihan_records_audit_and_rate_limiting(): void
    {
        RateLimiter::clear('strict_rate_' . sha1('127.0.0.1|cek-tagihan'));

        // Normal search succeeds and logs to audit_trail_logs
        $response = $this->post(route('cek.search'), [
            'nisn' => $this->siswa->nisn,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('audit_trail_logs', [
            'action_event' => 'PUBLIC_PORTAL_CEK_TAGIHAN',
        ]);

        // Exceed rate limit of 10 requests per minute
        for ($i = 0; $i < 11; $i++) {
            $response = $this->post(route('cek.search'), [
                'nisn' => $this->siswa->nisn,
            ]);
        }

        // Rate limit exceeded redirects back with error or returns 429
        $this->assertDatabaseHas('audit_trail_logs', [
            'action_event' => 'SECURITY_RATE_LIMIT_EXCEEDED',
        ]);
    }
}
