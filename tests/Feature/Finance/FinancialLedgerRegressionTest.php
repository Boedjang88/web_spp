<?php

namespace Tests\Feature\Finance;

use App\Domain\Finance\Services\SppBillingService;
use App\Jobs\SendWhatsAppNotificationJob;
use App\Models\ActivityLog;
use App\Models\BankMitra;
use App\Models\Kelas;
use App\Models\PembayaranUkt;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\Finance\H2hBillingService;
use App\Services\Messaging\WhatsAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FinancialLedgerRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected Siswa $siswa;
    protected Kelas $kelas;
    protected Spp $spp;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kelas = Kelas::create([
            'nama_kelas' => 'XII RPL 1',
            'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak',
        ]);

        $this->spp = Spp::create([
            'tahun' => 2026,
            'nominal' => 500000,
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'consent_pdp_at' => now(),
        ]);

        $this->siswa = Siswa::create([
            'nisn' => '0012345678',
            'nis' => '12345',
            'nama' => 'Student Financial Test',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Jl. Kampus Merdeka',
            'no_telp' => '',
            'consent_pdp_at' => now(),
        ]);
    }

    /**
     * TEST 1: WhatsApp notification skip when student phone is missing.
     */
    public function test_whatsapp_notification_skips_when_phone_is_missing(): void
    {
        Queue::fake();

        $service = app(WhatsAppNotificationService::class);

        // Student has empty phone
        $this->assertEmpty($this->siswa->no_telp);

        $service->sendUktPaymentReminder($this->siswa, 5000000, '2026-12-31');
        $service->sendEwsRiskAlert($this->siswa, 'AKADEMIK', 'IPK Rendah');

        // Verify NO jobs were dispatched to 081234567890 or anywhere
        Queue::assertNothingPushed();
    }

    /**
     * TEST 2: WhatsApp notification dispatches to actual student phone number.
     */
    public function test_whatsapp_notification_dispatches_to_actual_student_phone(): void
    {
        Queue::fake();

        $this->siswa->update(['no_telp' => '089876543210']);

        $service = app(WhatsAppNotificationService::class);
        $service->sendUktPaymentReminder($this->siswa, 5000000, '2026-12-31');

        Queue::assertPushed(SendWhatsAppNotificationJob::class, function ($job) {
            return $job->phoneNumber === '089876543210' && $job->notificationType === 'UKT_REMINDER';
        });
    }

    /**
     * TEST 3: terbilang handles 0, negative amounts, and billions gracefully without errors.
     */
    public function test_terbilang_handles_zero_negative_and_large_numbers(): void
    {
        // Test PembayaranUkt static terbilang
        $this->assertEquals('nol', PembayaranUkt::terbilang(0));
        $this->assertEquals('minus lima puluh ribu', PembayaranUkt::terbilang(-50000));
        $this->assertEquals('seratus lima puluh ribu', PembayaranUkt::terbilang(150000));
        $this->assertEquals('satu juta lima ratus ribu', PembayaranUkt::terbilang(1500000));
        $this->assertEquals('dua milyar lima ratus juta', PembayaranUkt::terbilang(2500000000));
    }

    /**
     * TEST 4: SppBillingService floor clamps balances and harmonizes billing keys.
     */
    public function test_spp_billing_service_floor_clamps_and_harmonizes_keys(): void
    {
        $service = app(SppBillingService::class);
        $statement = $service->getBillingStatementData($this->siswa);

        $this->assertArrayHasKey('total_tunggakan', $statement);
        $this->assertArrayHasKey('total_rupiah', $statement);
        $this->assertArrayHasKey('bulan_menunggak', $statement);
        $this->assertArrayHasKey('list_bulan', $statement);

        $this->assertGreaterThanOrEqual(0, $statement['total_tunggakan']);
        $this->assertEquals($statement['total_tunggakan'], $statement['total_rupiah']);
        $this->assertEquals($statement['bulan_menunggak'], $statement['list_bulan']);
    }

    /**
     * TEST 5: H2H webhook rejects replay attacks with timestamp drift > 300s.
     */
    public function test_h2h_webhook_rejects_timestamp_drift_replay_attacks(): void
    {
        $bank = BankMitra::create([
            'kode_bank' => 'BSI',
            'nama_bank' => 'Bank Syariah Indonesia',
            'prefix_va' => '99881',
            'secret_key' => 'bsi_secret_2026',
            'is_active' => true,
        ]);

        $tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(6)->toDateString(),
            'is_active' => true,
        ]);

        $billingService = app(H2hBillingService::class);
        $tagihan = $billingService->generateSemesterInvoice(
            $this->siswa->id,
            $tahunAkademik->id,
            $bank->id,
            [['nama_item' => 'UKT Semester Ganjil', 'nominal' => 3000000]]
        );

        $oldTimestamp = now()->subMinutes(10)->timestamp; // 600s drift (exceeds 300s)
        $signature = hash_hmac('sha256', $tagihan->nomor_va . '|BSI-REPLAY-001|3000000', $bank->secret_key);

        $payload = [
            'nomor_transaksi_bank' => 'BSI-REPLAY-001',
            'nomor_va' => $tagihan->nomor_va,
            'jumlah_bayar' => 3000000,
            'kode_bank' => 'BSI',
            'channel_bayar' => 'MOBILE_BANKING',
            'signature' => $signature,
        ];

        // Send with old timestamp in header
        $response = $this->postJson('/api/h2h/webhook', $payload, [
            'X-Timestamp' => (string) $oldTimestamp,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        // Verify ActivityLog recorded the replay attack
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'H2H_PAYMENT_REPLAY_ATTACK',
        ]);
    }

    /**
     * TEST 6: H2H webhook accepts valid timestamp and creates structured audit log.
     */
    public function test_h2h_webhook_accepts_valid_timestamp_and_records_audit(): void
    {
        $bank = BankMitra::create([
            'kode_bank' => 'MANDIRI',
            'nama_bank' => 'Bank Mandiri',
            'prefix_va' => '88990',
            'secret_key' => 'mandiri_secret_2026',
            'is_active' => true,
        ]);

        $tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addMonths(6)->toDateString(),
            'is_active' => true,
        ]);

        $billingService = app(H2hBillingService::class);
        $tagihan = $billingService->generateSemesterInvoice(
            $this->siswa->id,
            $tahunAkademik->id,
            $bank->id,
            [['nama_item' => 'UKT Semester Ganjil', 'nominal' => 4500000]]
        );

        $freshTimestamp = now()->timestamp;
        $signature = hash_hmac('sha256', $tagihan->nomor_va . '|MND-VALID-001|4500000', $bank->secret_key);

        $payload = [
            'nomor_transaksi_bank' => 'MND-VALID-001',
            'nomor_va' => $tagihan->nomor_va,
            'jumlah_bayar' => 4500000,
            'kode_bank' => 'MANDIRI',
            'channel_bayar' => 'ATM_MANDIRI',
            'signature' => $signature,
        ];

        $response = $this->postJson('/api/h2h/webhook', $payload, [
            'X-Timestamp' => (string) $freshTimestamp,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'SUCCESS');

        // Verify ActivityLog recorded the processed payment
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'H2H_PAYMENT_PROCESSED',
        ]);
    }
}
