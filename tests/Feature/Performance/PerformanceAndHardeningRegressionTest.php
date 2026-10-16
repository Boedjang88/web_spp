<?php

namespace Tests\Feature\Performance;

use App\Jobs\AnalyzeStudentDropoutRisk;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PerformanceAndHardeningRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    /**
     * Test E.4: /api/health masks internal server topology in production without auth
     */
    public function test_health_check_endpoint_hardened_in_production(): void
    {
        config(['app.health_check_key' => 'secret-super-cluster-key-2026']);

        // 1. Simulate production environment without key
        $this->app->detectEnvironment(fn () => 'production');

        $response = $this->getJson('/api/health');
        $response->assertStatus(200)
            ->assertExactJson([
                'status' => 'UP',
                'timestamp' => $response->json('timestamp'),
            ]);

        $this->assertArrayNotHasKey('php_version', $response->json());
        $this->assertArrayNotHasKey('laravel_version', $response->json());
        $this->assertArrayNotHasKey('services', $response->json());
        $this->assertArrayNotHasKey('system_metrics', $response->json());

        // 2. Production request with valid X-Health-Key header
        $responseAuthorized = $this->withHeader('X-Health-Key', 'secret-super-cluster-key-2026')
            ->getJson('/api/health');

        $responseAuthorized->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'environment',
                'php_version',
                'laravel_version',
                'services',
                'system_metrics',
            ]);

        // 3. Reset environment back to testing
        $this->app->detectEnvironment(fn () => 'testing');
        $testEnvResponse = $this->getJson('/api/health');
        $testEnvResponse->assertStatus(200)
            ->assertJsonStructure(['status', 'php_version', 'services', 'system_metrics']);
    }

    /**
     * Test E.3: ews:scan dispatches chunked asynchronous queue jobs
     */
    public function test_ews_scan_dispatches_async_queue_jobs(): void
    {
        Queue::fake();

        $kelas = Kelas::create(['nama_kelas' => 'TI-2026', 'kompetensi_keahlian' => 'Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 3000000]);

        $siswa1 = Siswa::create([
            'nisn' => '1000000001',
            'nis' => '10001',
            'nama' => 'Student Queue 1',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'status_kelulusan' => 'Aktif',
            'alamat' => 'Kampus',
            'no_telp' => '081234567890',
        ]);

        $siswa2 = Siswa::create([
            'nisn' => '1000000002',
            'nis' => '10002',
            'nama' => 'Student Queue 2',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'status_kelulusan' => 'Aktif',
            'alamat' => 'Kampus',
            'no_telp' => '081234567891',
        ]);

        $this->artisan('ews:scan')
            ->assertSuccessful();

        Queue::assertPushed(AnalyzeStudentDropoutRisk::class, 2);
        Queue::assertPushed(AnalyzeStudentDropoutRisk::class, fn ($job) => $job->studentId === $siswa1->id);
        Queue::assertPushed(AnalyzeStudentDropoutRisk::class, fn ($job) => $job->studentId === $siswa2->id);
    }

    /**
     * Test E.5: Admin dashboard aggregates are cached and invalidated when payments occur
     */
    public function test_dashboard_aggregates_are_cached_and_invalidated_on_payment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kelas = Kelas::create(['nama_kelas' => 'SI-2026', 'kompetensi_keahlian' => 'Sistem Informasi']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 2500000]);

        $cacheKey = 'dashboard_admin_aggregates_' . now()->toDateString();
        $this->assertFalse(Cache::has($cacheKey));

        // 1. Visit dashboard as admin to warm up cache
        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        $this->assertTrue(Cache::has($cacheKey));
        $cached = Cache::get($cacheKey);
        $this->assertIsArray($cached);
        $this->assertArrayHasKey('totalSiswa', $cached);
        $this->assertArrayHasKey('totalPemasukan', $cached);

        // 2. Record new payment -> cache must be invalidated
        $siswa = Siswa::create([
            'nisn' => '1000000003',
            'nis' => '10003',
            'nama' => 'Student Cache Invalidate',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'status_kelulusan' => 'Aktif',
            'alamat' => 'Kampus',
            'no_telp' => '081234567892',
        ]);

        $pembayaran = Pembayaran::create([
            'id_petugas' => $admin->id,
            'id_siswa' => $siswa->id,
            'tgl_bayar' => now()->toDateString(),
            'bulan_dibayar' => 'Oktober',
            'tahun_dibayar' => (int) now()->year,
            'id_spp' => $spp->id,
            'jumlah_bayar' => 2500000,
        ]);

        $this->assertFalse(Cache::has($cacheKey), 'Dashboard cache must be invalidated upon creating Pembayaran');
    }

    /**
     * Test E.1: Unbounded Siswa queries in Pembayaran and Nilai are bounded to 100 max
     */
    public function test_student_query_in_pembayaran_and_nilai_is_bounded(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kelas = Kelas::create(['nama_kelas' => 'IF-BOUNDED', 'kompetensi_keahlian' => 'Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 2500000]);

        // Seed 105 students
        for ($i = 1; $i <= 105; $i++) {
            Siswa::create([
                'nisn' => sprintf('20000000%02d', $i),
                'nis' => sprintf('200%02d', $i),
                'nama' => sprintf('Student Bounded %03d', $i),
                'id_kelas' => $kelas->id,
                'id_spp' => $spp->id,
                'status_kelulusan' => 'Aktif',
                'alamat' => 'Kampus',
                'no_telp' => '081234567800',
            ]);
        }

        // 1. Check Pembayaran create view receives bounded students
        $response = $this->actingAs($admin)->get(route('web.pembayaran.create'));
        $response->assertStatus(200);
        $siswasInView = $response->viewData('siswas');
        $this->assertLessThanOrEqual(100, $siswasInView->count());

        // 2. Pre-selected student beyond the initial limit is prepended
        $lastStudent = Siswa::orderBy('id', 'desc')->first();
        $responsePreselected = $this->actingAs($admin)->get(route('web.pembayaran.create', ['id_siswa' => $lastStudent->id]));
        $responsePreselected->assertStatus(200);
        $siswasWithSelected = $responsePreselected->viewData('siswas');
        $this->assertTrue($siswasWithSelected->contains('id', $lastStudent->id));

        // 3. Nilai create view also bounds student count
        $responseNilai = $this->actingAs($admin)->get(route('web.nilai.create'));
        $responseNilai->assertStatus(200);
        $siswasInNilai = $responseNilai->viewData('siswas');
        $this->assertLessThanOrEqual(100, $siswasInNilai->count());
    }

    /**
     * Test E.2: Database foreign keys are indexed
     */
    public function test_database_foreign_keys_are_indexed(): void
    {
        $tables = ['pembayarans', 'siswas', 'nilais', 'jadwal_pelajarans', 'activity_logs'];

        foreach ($tables as $table) {
            $fks = DB::select("PRAGMA foreign_key_list({$table})");
            $indexes = DB::select("PRAGMA index_list({$table})");

            $indexedCols = [];
            foreach ($indexes as $idx) {
                $cols = DB::select("PRAGMA index_info({$idx->name})");
                if (!empty($cols)) {
                    $indexedCols[] = $cols[0]->name;
                }
            }

            foreach ($fks as $fk) {
                $this->assertContains(
                    $fk->from,
                    $indexedCols,
                    "Foreign key {$table}.{$fk->from} must have an active database index."
                );
            }
        }
    }
}
