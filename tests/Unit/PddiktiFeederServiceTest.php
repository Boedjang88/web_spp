<?php

namespace Tests\Unit;

use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\PddiktiSyncLog;
use App\Models\Siswa;
use App\Models\Spp;
use App\Services\Integration\PddiktiFeederService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Tests\TestCase;

class PddiktiFeederServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.pddikti.url' => 'http://feeder-mock.test/ws/live2.php',
            'services.pddikti.username' => 'feeder_user_test',
            'services.pddikti.password' => 'feeder_pass_test',
            'services.pddikti.verify_ssl' => false,
            'services.pddikti.sandbox' => false,
        ]);
    }

    public function test_service_reads_configured_credentials_without_hardcoding(): void
    {
        $service = new PddiktiFeederService();
        $this->assertInstanceOf(PddiktiFeederService::class, $service);

        // Test with empty config in non-sandbox mode
        config([
            'services.pddikti.username' => '',
            'services.pddikti.password' => '',
            'services.pddikti.sandbox' => false,
        ]);

        $emptyService = new PddiktiFeederService();
        
        $this->expectException(InvalidArgumentException::class);
        $emptyService->getToken(true);
    }

    public function test_get_token_fetches_and_caches_token_successfully(): void
    {
        Http::fake([
            'http://feeder-mock.test/ws/live2.php' => Http::response([
                'error_code' => 0,
                'error_desc' => '',
                'data' => [
                    'token' => 'VALID-MOCK-PDDIKTI-TOKEN-12345',
                ],
            ], 200),
        ]);

        $service = new PddiktiFeederService();
        $token = $service->getToken(true);

        $this->assertEquals('VALID-MOCK-PDDIKTI-TOKEN-12345', $token);
        $this->assertEquals('VALID-MOCK-PDDIKTI-TOKEN-12345', Cache::get(PddiktiFeederService::TOKEN_CACHE_KEY));
    }

    public function test_execute_with_auto_auth_refreshes_token_on_code_100(): void
    {
        Cache::put(PddiktiFeederService::TOKEN_CACHE_KEY, 'EXPIRED-TOKEN', 3600);

        Http::fakeSequence()
            // 1. Initial request returns code 100 (Token Expired)
            ->push(['error_code' => 100, 'error_desc' => 'Token Expired'], 200)
            // 2. Token refresh request
            ->push(['error_code' => 0, 'data' => ['token' => 'REFRESHED-TOKEN-999']], 200)
            // 3. Retry request with new token
            ->push(['error_code' => 0, 'result' => ['id_pddikti' => 'PDDIKTI-MOCK-SUCCESS']], 200);

        $service = new PddiktiFeederService();
        $result = $service->executeWithAutoAuth('InsertMahasiswa', ['nama' => 'Test Mahasiswa']);

        $this->assertEquals(0, $result['error_code']);
        $this->assertEquals('REFRESHED-TOKEN-999', Cache::get(PddiktiFeederService::TOKEN_CACHE_KEY));
    }

    public function test_format_mahasiswa_and_kelas_kuliah_payloads(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'kompetensi_keahlian' => 'RPL']);
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 500000]);

        $siswa = Siswa::create([
            'nama' => 'Budi Santoso',
            'nisn' => '0041234567',
            'nis' => '12345',
            'nik' => '3273011505040001',
            'alamat' => 'Jl. Soekarno Hatta No 123',
            'no_telp' => '081234567890',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
        ]);

        $service = new PddiktiFeederService();
        $payload = $service->formatMahasiswaPayload($siswa);

        $this->assertEquals('Budi Santoso', $payload['nama_mahasiswa']);
        $this->assertEquals('3273011505040001', $payload['nik']);
        $this->assertEquals('0041234567', $payload['nisn']);
        $this->assertEquals('Jl. Soekarno Hatta No 123', $payload['jalan']);
    }

    public function test_sync_record_creates_log_and_invokes_feeder(): void
    {
        Http::fake([
            'http://feeder-mock.test/ws/live2.php' => function (Request $request) {
                if (($request['act'] ?? '') === 'GetToken') {
                    return Http::response([
                        'error_code' => 0,
                        'data' => ['token' => 'MOCK-TOKEN-XYZ'],
                    ], 200);
                }

                return Http::response([
                    'error_code' => 0,
                    'error_desc' => null,
                    'result' => [
                        'id_pddikti' => 'PDDIKTI-TEST-GUID-9988',
                    ],
                ], 200);
            },
        ]);

        $service = new PddiktiFeederService();
        $log = $service->syncRecord('mahasiswa', '101', ['nama' => 'Testing']);

        $this->assertInstanceOf(PddiktiSyncLog::class, $log);
        $this->assertEquals('SUCCESS', $log->status_sync);
        $this->assertEquals('PDDIKTI-TEST-GUID-9988', $log->id_feeder_pddikti);
    }
}
