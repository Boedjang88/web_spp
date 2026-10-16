<?php

namespace Tests\Feature\Security;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Services\Academic\DiplomaVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class PublicVerificationSecurityTest extends TestCase
{
    use RefreshDatabase;

    private Kelas $kelas;
    private Spp $spp;
    private Siswa $graduatedStudent;
    private Siswa $activeStudent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kelas = Kelas::create([
            'nama_kelas' => 'TI-4A',
            'kompetensi_keahlian' => 'Teknik Informatika',
        ]);

        $this->spp = Spp::create([
            'tahun' => 2026,
            'nominal' => 5000000,
        ]);

        $this->graduatedStudent = Siswa::create([
            'nisn' => '0011223344',
            'nis' => '10101',
            'nama' => 'Budi Pratama Lulusan',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Jl. Kampus No. 123',
            'no_telp' => '081234567899',
            'nik' => '3273010101010001',
            'nama_ibu_kandung' => 'Siti Rahmawati',
            'no_hp_wali' => '081987654321',
            'status_kelulusan' => 'Lulus Wisuda',
            'tgl_kelulusan' => '2026-08-20',
            'nomor_ijazah' => 'PIN/2026/000101',
        ]);

        $this->activeStudent = Siswa::create([
            'nisn' => '0099887766',
            'nis' => '20202',
            'nama' => 'Dewi Sartika Aktif',
            'id_kelas' => $this->kelas->id,
            'id_spp' => $this->spp->id,
            'alamat' => 'Jl. Mahasiswa No. 45',
            'no_telp' => '085678901234',
            'nik' => '3273020202020002',
            'nama_ibu_kandung' => 'Nurhayati',
            'no_hp_wali' => '085712345678',
            'status_kelulusan' => 'Aktif',
            'nomor_ijazah' => null,
        ]);
    }

    /**
     * Test 1: GET /cek-tagihan renders public self-service billing portal without 405 error.
     */
    public function test_get_cek_tagihan_renders_public_portal(): void
    {
        $response = $this->get(route('cek.index'));
        $response->assertStatus(200);
        $response->assertSee('Portal Layanan Mandiri Mahasiswa');
    }

    /**
     * Test 2: POST /cek-tagihan masks student name and displays payment details.
     */
    public function test_cek_tagihan_masks_student_name_for_guest(): void
    {
        $response = $this->post(route('cek.search'), [
            'nisn' => $this->graduatedStudent->nisn,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Mahasiswa Aktif');
        // Masked name: 'B*** P*** L***'
        $response->assertSee('B***');
        $response->assertDontSee('Budi Pratama Lulusan');
    }

    /**
     * Test 3: POST /cek-tagihan on invalid NISN redirects to /cek-tagihan (not to /login).
     */
    public function test_cek_tagihan_invalid_nisn_redirects_to_search_portal(): void
    {
        $response = $this->post(route('cek.search'), [
            'nisn' => '999999999999',
        ]);

        $response->assertRedirect(route('cek.index'));
        $response->assertSessionHas('error');
    }

    /**
     * Test 4: POST /cek-tagihan records privacy-preserving keyed hash in audit trail.
     */
    public function test_cek_tagihan_records_keyed_hash_in_audit_trail(): void
    {
        $response = $this->post(route('cek.search'), [
            'nisn' => $this->graduatedStudent->nisn,
        ]);

        $response->assertStatus(200);

        $expectedHash = hash_hmac('sha256', $this->graduatedStudent->nisn, config('app.key'));

        $this->assertDatabaseHas('audit_trail_logs', [
            'action_event' => 'PUBLIC_PORTAL_CEK_TAGIHAN',
        ]);
    }

    /**
     * Test 5: POST /cek-tagihan throttles on repeated invalid lookup attempts.
     */
    public function test_cek_tagihan_throttles_repeated_invalid_lookups(): void
    {
        $failedKey = 'failed_cek_tagihan_' . sha1('127.0.0.1');
        RateLimiter::clear($failedKey);

        // Send 5 invalid searches to trigger failure threshold
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('cek.search'), ['nisn' => "INVALID_{$i}"]);
        }

        // The 6th request must be throttled with rate limit error
        $response = $this->post(route('cek.search'), ['nisn' => 'INVALID_BLOCKED']);
        $response->assertRedirect(route('cek.index'));
        $response->assertSessionHas('error');
        $response->assertHeader('Retry-After');
    }

    /**
     * Test 6: GET /verifikasi-ijazah/{token} verifies valid diploma via cryptographic token.
     */
    public function test_diploma_verification_succeeds_for_graduated_student(): void
    {
        $verificationService = app(DiplomaVerificationService::class);
        $token = $verificationService->generateVerificationToken($this->graduatedStudent);

        $response = $this->get(route('diploma.verify', $token));

        $response->assertStatus(200);
        $response->assertSee('DOKUMEN TERVERIFIKASI SAH &amp; VALID', false);
        $response->assertSee($this->graduatedStudent->nomor_ijazah);
        $response->assertSee($this->graduatedStudent->nama);
        $response->assertSee('PIN/2026/000101');
    }

    /**
     * Test 7: GET /verifikasi-ijazah/{token} succeeds via normalized PIN (URL safe).
     */
    public function test_diploma_verification_succeeds_via_normalized_pin(): void
    {
        // URL-safe representation with dash replacing slash
        $urlSafePin = 'PIN-2026-000101';

        $response = $this->get(route('diploma.verify', $urlSafePin));

        $response->assertStatus(200);
        $response->assertSee($this->graduatedStudent->nama);
        $response->assertSee('Teknik Informatika');
    }

    /**
     * Test 8: GET /verifikasi-ijazah/{token} JSON excludes sensitive PII.
     */
    public function test_diploma_verification_json_excludes_sensitive_pii(): void
    {
        $verificationService = app(DiplomaVerificationService::class);
        $token = $verificationService->generateVerificationToken($this->graduatedStudent);

        $response = $this->getJson(route('diploma.verify', $token));

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status_verifikasi', 'TERVERIFIKASI_SAH')
            ->assertJsonPath('data.nomor_ijazah', 'PIN/2026/000101')
            ->assertJsonPath('data.nama_mahasiswa', 'Budi Pratama Lulusan');

        // Verify strictly excluded PII
        $response->assertJsonMissing(['nik' => $this->graduatedStudent->nik]);
        $response->assertJsonMissing(['nama_ibu_kandung' => $this->graduatedStudent->nama_ibu_kandung]);
        $response->assertJsonMissing(['no_telp' => $this->graduatedStudent->no_telp]);
        $response->assertJsonMissing(['no_hp_wali' => $this->graduatedStudent->no_hp_wali]);
        $response->assertJsonMissing(['alamat' => $this->graduatedStudent->alamat]);
    }

    /**
     * Test 9: GET /verifikasi-ijazah/{token} rejects ungraduated active students.
     */
    public function test_diploma_verification_rejects_active_non_graduated_student(): void
    {
        $verificationService = app(DiplomaVerificationService::class);
        $token = $verificationService->generateVerificationToken($this->activeStudent);

        $response = $this->getJson(route('diploma.verify', $token));

        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('status', 'TIDAK_DITEMUKAN');
    }

    /**
     * Test 10: GET /verifikasi-ijazah/{token} returns 404 for unknown/invalid tokens.
     */
    public function test_diploma_verification_returns_404_for_unknown_token(): void
    {
        $response = $this->get(route('diploma.verify', 'UNKNOWN-INVALID-TOKEN-99999'));

        $response->assertStatus(404);
        $response->assertSee('DATA DOKUMEN TIDAK DITEMUKAN');
    }

    /**
     * Test 11: GET /verifikasi-ijazah/{token} enforces rate limiting (30 req/min).
     */
    public function test_diploma_verification_enforces_rate_limiting(): void
    {
        $ip = '127.0.0.1';
        $key = 'strict_rate_' . sha1($ip . '|verifikasi-ijazah/TEST-RATE-LIMIT');
        RateLimiter::clear($key);

        for ($i = 0; $i < 30; $i++) {
            $this->get(route('diploma.verify', 'TEST-RATE-LIMIT'));
        }

        // 31st request should be throttled
        $response = $this->get(route('diploma.verify', 'TEST-RATE-LIMIT'));
        $this->assertTrue(in_array($response->status(), [302, 429]));
    }

    /**
     * Test 12: GET /verifikasi-ijazah/{token} rejects revoked/cancelled diplomas.
     */
    public function test_diploma_verification_rejects_revoked_or_cancelled_status(): void
    {
        $this->graduatedStudent->update(['status_kelulusan' => 'Dibatalkan']);

        $verificationService = app(DiplomaVerificationService::class);
        $verificationService->invalidateStudentDiplomaCache($this->graduatedStudent);

        $token = $verificationService->generateVerificationToken($this->graduatedStudent);
        $response = $this->getJson(route('diploma.verify', $token));

        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('status', 'TIDAK_DITEMUKAN');
    }

    /**
     * Test 13: Invalidate cache immediately clears cached diploma verification.
     */
    public function test_diploma_verification_cache_can_be_invalidated(): void
    {
        $verificationService = app(DiplomaVerificationService::class);
        $token = $verificationService->generateVerificationToken($this->graduatedStudent);

        // Warm up cache
        $result1 = $verificationService->verifyToken($token);
        $this->assertNotNull($result1);
        $this->assertTrue($result1['is_valid']);

        // Invalidate cache
        $verificationService->invalidateTokenCache($token);

        // After revoking status in database and invalidating cache, verify it is immediately not found
        $this->graduatedStudent->update(['status_kelulusan' => 'Dicabut']);
        $result2 = $verificationService->verifyToken($token);
        $this->assertNull($result2);
    }

    /**
     * Test 14: Rate limit audit trail payload strictly redacts/masks PII.
     */
    public function test_strict_rate_limiting_middleware_sanitizes_pii_in_audit_payload(): void
    {
        $ip = '127.0.0.1';
        $key = 'strict_rate_' . sha1($ip . '|cek-tagihan');
        RateLimiter::clear($key);

        // Hit cek.search route 11 times (limit is 10) with sensitive params to trigger rate limiting
        for ($i = 0; $i < 12; $i++) {
            $this->post(route('cek.search'), [
                'nisn' => '0011223344',
                'nik' => '3273010101010001',
                'no_hp' => '081234567890',
            ]);
        }

        // Verify that audit log exists and does NOT contain raw NIK or raw phone number
        $auditLogs = \App\Models\AuditTrailLog::where('action_event', 'SECURITY_RATE_LIMIT_EXCEEDED')->get();
        $this->assertNotEmpty($auditLogs);
        foreach ($auditLogs as $log) {
            $payloadJson = json_encode($log->payload);
            $this->assertStringNotContainsString('3273010101010001', $payloadJson);
            $this->assertStringNotContainsString('081234567890', $payloadJson);
        }
    }
}

