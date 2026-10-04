<?php

namespace Tests\Feature;

use App\Models\BankMitra;
use App\Models\FinancialClearance;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\ReconciliationLog;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TagihanVa;
use App\Models\TahunAkademik;
use App\Models\TransaksiH2h;
use App\Models\User;
use App\Services\Academic\SmartKrsService;
use App\Services\Finance\H2hBillingService;
use App\Services\Integration\CircuitBreaker;
use App\Services\Monitoring\SystemHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class H2hWebhookConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected Siswa $siswa;
    protected BankMitra $bank;
    protected TagihanVa $tagihan;
    protected TahunAkademik $tahunAkademik;
    protected Fakultas $fakultas;
    protected ProgramStudi $prodi;
    protected Kurikulum $kurikulum;

    protected function setUp(): void
    {
        parent::setUp();

        $kelas = Kelas::create(['nama_kelas' => 'IF-2026', 'kompetensi_keahlian' => 'Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 5000000]);

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
            'nama_kurikulum' => 'Kurikulum 2026',
            'tahun_mulai' => 2026,
            'is_active' => true,
        ]);

        $this->siswa = Siswa::create([
            'nisn' => '0098765432',
            'nis' => '98765',
            'nama' => 'Budi Prakoso Hardening',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Jl. Boulevard ITB No. 10',
            'no_telp' => '081234567890',
            'nik' => '3273010101900001',
            'nama_ibu_kandung' => 'Siti Rahmawati',
            'no_hp_wali' => '081987654321',
        ]);

        $this->studentUser = User::create([
            'name' => 'Budi Prakoso Hardening',
            'email' => 'budi.pdp@test.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'id_siswa' => $this->siswa->id,
            'consent_pdp_at' => null, // Un-consented initially
        ]);

        $this->bank = BankMitra::create([
            'kode_bank' => 'BNI',
            'nama_bank' => 'Bank Negara Indonesia',
            'prefix_va' => '98800',
            'secret_key' => 'bni_secret_hash_2026',
            'endpoint_webhook' => 'https://api.bni.co.id/h2h',
            'is_active' => true,
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(6)->toDateString(),
            'is_active' => true,
        ]);

        $billingService = app(H2hBillingService::class);
        $this->tagihan = $billingService->generateSemesterInvoice(
            $this->siswa->id,
            $this->tahunAkademik->id,
            $this->bank->id,
            [['nama_item' => 'UKT Semester Ganjil', 'nominal' => 5000000]]
        );
    }

    /**
     * Test 1: UU PDP Sensitive Data Database Encryption & Consent Middleware
     */
    public function test_uu_pdp_encryption_and_consent_middleware(): void
    {
        // 1. Verify Encrypted Cast in DB
        $freshSiswa = Siswa::find($this->siswa->id);
        $this->assertEquals('3273010101900001', $freshSiswa->nik);
        $this->assertEquals('Siti Rahmawati', $freshSiswa->nama_ibu_kandung);
        $this->assertEquals('081987654321', $freshSiswa->no_hp_wali);

        // Raw database column is encrypted and does not contain plain text
        $rawRow = \Illuminate\Support\Facades\DB::table('siswas')->where('id', $this->siswa->id)->first();
        $this->assertNotEquals('3273010101900001', $rawRow->nik);
        $this->assertNotEquals('Siti Rahmawati', $rawRow->nama_ibu_kandung);

        // 2. Un-consented user is redirected to PDP consent screen
        $response = $this->actingAs($this->studentUser)->get('/dashboard');
        $response->assertRedirect(route('pdp.consent.show'));

        // 3. Un-consented user signing consent
        $consentResponse = $this->actingAs($this->studentUser)->post(route('pdp.consent.store'), [
            'agree_pdp' => '1',
        ]);
        $consentResponse->assertRedirect(route('dashboard'));

        $this->studentUser->refresh();
        $this->assertNotNull($this->studentUser->consent_pdp_at);

        // 4. Now user can access protected dashboard
        $dashboardResponse = $this->actingAs($this->studentUser)->get('/dashboard');
        $dashboardResponse->assertStatus(200);
    }

    /**
     * Test 2: Anti-Replay Financial Webhook & Idempotency Layer
     */
    public function test_h2h_webhook_idempotency_and_replay_protection(): void
    {
        $signature = hash_hmac('sha256', $this->tagihan->nomor_va . '|BNI-TRX-IDEMP-2026-001|5000000', $this->bank->secret_key);

        $payload = [
            'nomor_transaksi_bank' => 'BNI-TRX-IDEMP-2026-001',
            'nomor_va' => $this->tagihan->nomor_va,
            'jumlah_bayar' => 5000000,
            'kode_bank' => 'BNI',
            'channel_bayar' => 'ATM_BNI',
            'signature' => $signature,
        ];

        // First Post: Processed normally, updates VA and creates Clearance
        $response1 = $this->postJson('/api/h2h/webhook', $payload, [
            'Idempotency-Key' => 'KEY-UNIQUE-UUID-12345',
        ]);

        $response1->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'SUCCESS')
            ->assertJsonPath('data.krs_unlocked', true);

        $this->tagihan->refresh();
        $this->assertEquals('Lunas', $this->tagihan->status_pembayaran);

        $clearance = FinancialClearance::where('id_siswa', $this->siswa->id)->first();
        $this->assertNotNull($clearance);
        $this->assertTrue($clearance->is_krs_unlocked);

        // Second Post with Identical Idempotency-Key (Replay Attack / Duplicate Retry)
        $response2 = $this->postJson('/api/h2h/webhook', $payload, [
            'Idempotency-Key' => 'KEY-UNIQUE-UUID-12345',
        ]);

        $response2->assertStatus(200);
        $response2->assertHeader('X-Idempotent-Replay', 'true');
        $this->assertEquals($response1->json(), $response2->json());

        // Ensure only ONE transaction record was created in the database
        $trxCount = TransaksiH2h::where('nomor_transaksi_bank', 'BNI-TRX-IDEMP-2026-001')->count();
        $this->assertEquals(1, $trxCount);
    }

    /**
     * Test 3: SFTP Bank Settlement Reconciliation Engine
     */
    public function test_sftp_reconciliation_command_detects_discrepancies(): void
    {
        // 1. Setup local transaction in database
        TransaksiH2h::create([
            'id_tagihan_va' => $this->tagihan->id,
            'nomor_transaksi_bank' => 'BNI-RECON-OK-01',
            'kode_bank' => 'BNI',
            'nomor_va' => $this->tagihan->nomor_va,
            'jumlah_dibayar' => 5000000,
            'waktu_transaksi_bank' => now(),
            'channel_bayar' => 'M-BANKING',
            'status_callback' => 'SUCCESS',
        ]);

        // 2. Prepare mock SFTP file with 1 matching, 1 amount mismatch, 1 missing locally
        $csvContent = "TX_ID,VA_NUMBER,AMOUNT,STATUS,SETTLED_AT\n"
            . "BNI-RECON-OK-01,{$this->tagihan->nomor_va},5000000,SETTLED,2026-10-04 10:00:00\n"
            . "BNI-RECON-MISMATCH-02,{$this->tagihan->nomor_va},4500000,SETTLED,2026-10-04 11:00:00\n"
            . "BNI-RECON-MISSING-03,{$this->tagihan->nomor_va},5000000,SETTLED,2026-10-04 12:00:00\n";

        $mockSftpPath = storage_path('app/mock_sftp/bni_settlement_' . date('Ymd') . '.csv');
        File::ensureDirectoryExists(dirname($mockSftpPath));
        File::put($mockSftpPath, $csvContent);

        // 3. Run reconciliation command
        $exitCode = Artisan::call('reconcile:bank-h2h', [
            '--bank' => 'BNI',
            '--date' => date('Y-m-d'),
            '--file' => $mockSftpPath,
        ]);

        $this->assertEquals(0, $exitCode);

        // 4. Verify ReconciliationLog created
        $reconLog = ReconciliationLog::where('bank_code', 'BNI')->latest()->first();
        $this->assertNotNull($reconLog);
        $this->assertEquals(3, $reconLog->total_bank_records);
        $this->assertEquals(1, $reconLog->total_matched_records);
        $this->assertEquals(2, $reconLog->total_discrepancies);
        $this->assertEquals('DISCREPANCY_FOUND', $reconLog->status);

        // Clean up mock file
        File::delete($mockSftpPath);
    }

    /**
     * Test 4: Circuit Breaker for Resilient External Integrations
     */
    public function test_circuit_breaker_trips_open_and_fallback(): void
    {
        CircuitBreaker::reset('test_government_feeder');
        $this->assertEquals(CircuitBreaker::STATE_CLOSED, CircuitBreaker::getState('test_government_feeder'));

        // Cause 5 consecutive failures
        for ($i = 1; $i <= 5; $i++) {
            $res = CircuitBreaker::call(
                'test_government_feeder',
                function () {
                    throw new \Exception("PDDIKTI Feeder 504 Gateway Timeout");
                },
                function (\Throwable $e) {
                    return 'FALLBACK_TRIGGERED';
                },
                5,
                300
            );
            $this->assertEquals('FALLBACK_TRIGGERED', $res);
        }

        // Breaker should now be OPEN
        $this->assertEquals(CircuitBreaker::STATE_OPEN, CircuitBreaker::getState('test_government_feeder'));

        // 6th call should immediately trigger fallback without executing action
        $executed = false;
        $res6 = CircuitBreaker::call(
            'test_government_feeder',
            function () use (&$executed) {
                $executed = true;
                return 'SUCCESS';
            },
            function (\Throwable $e) {
                return 'CIRCUIT_OPEN_FAST_FALLBACK';
            }
        );

        $this->assertFalse($executed);
        $this->assertEquals('CIRCUIT_OPEN_FAST_FALLBACK', $res6);

        CircuitBreaker::reset('test_government_feeder');
    }

    /**
     * Test 5: Concurrency Protection & Throttle on Smart KRS Enrollment
     */
    public function test_smart_krs_enrollment_with_throttle(): void
    {
        // Unlock student financial clearance
        FinancialClearance::create([
            'id_siswa' => $this->siswa->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'is_krs_unlocked' => true,
            'unlocked_at' => now(),
        ]);

        $mk = MataKuliah::create([
            'id_kurikulum' => $this->kurikulum->id,
            'kode_mk' => 'IF301',
            'nama_mk' => 'Sistem Terdistribusi & Cloud',
            'sks_total' => 3,
        ]);

        $kelas = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'IF-A',
            'kuota_maksimal' => 40,
            'total_terisi' => 0,
            'status' => 'Buka',
        ]);

        $krsService = app(SmartKrsService::class);

        $detail = $krsService->attemptEnrollmentWithThrottle(
            $this->siswa->id,
            $kelas->id,
            $this->tahunAkademik->id
        );

        $this->assertNotNull($detail);
        $this->assertEquals($kelas->id, $detail->id_kelas_kuliah);

        $kelas->refresh();
        $this->assertEquals(1, $kelas->total_terisi);
    }

    /**
     * Test 6: Observability & Health Diagnostic Endpoint
     */
    public function test_observability_system_health_endpoint(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'environment',
                'php_version',
                'laravel_version',
                'services' => [
                    'database' => ['status', 'latency_ms', 'connection'],
                    'cache' => ['status', 'latency_ms', 'driver'],
                    'queue' => ['status', 'driver'],
                ],
                'system_metrics' => [
                    'memory' => ['current_mb', 'peak_mb', 'limit'],
                    'disk' => ['total_gb', 'used_gb', 'free_gb', 'used_percentage'],
                ],
            ]);

        $this->assertEquals('HEALTHY', $response->json('status'));
    }
}
