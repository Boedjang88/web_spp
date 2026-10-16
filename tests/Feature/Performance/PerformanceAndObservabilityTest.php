<?php

namespace Tests\Feature\Performance;

use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PresensiMahasiswa;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceAndObservabilityTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected Mahasiswa $mahasiswa;
    protected Siswa $siswa;
    protected Kelas $kelas;
    protected KelasKuliah $kelasKuliah;
    protected Spp $spp;
    protected TahunAkademik $tahunAkademik;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FTI',
            'nama_fakultas' => 'Fakultas Teknologi Informasi',
        ]);

        $prodi = ProgramStudi::create([
            'id_fakultas' => $fakultas->id,
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
        ]);

        $this->tahunAkademik = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->startOfYear(),
            'tgl_selesai' => now()->endOfYear(),
            'is_active' => true,
        ]);

        $this->kelas = Kelas::create([
            'nama_kelas' => 'TI-1A',
            'kompetensi_keahlian' => 'Teknik Informatika',
        ]);

        $this->spp = Spp::create([
            'tahun' => 2026,
            'nominal' => 3500000,
        ]);

        $kurikulum = \App\Models\Kurikulum::create([
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum 2026',
            'tahun_mulai' => 2026,
            'total_sks_lulus' => 144,
            'is_active' => true,
        ]);

        $mk = MataKuliah::create([
            'id_kurikulum' => $kurikulum->id,
            'kode_mk' => 'TI101',
            'nama_mk' => 'Algoritma & Pemrograman',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $this->kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahunAkademik->id,
            'nama_kelas' => 'TI-1A',
            'kuota_maksimal' => 40,
            'total_terisi' => 1,
        ]);

        $this->mahasiswa = Mahasiswa::create([
            'id_prodi' => $prodi->id,
            'nim' => '2026001099',
            'nama' => 'Dimas Pratama',
            'angkatan' => 2026,
            'status' => 'aktif',
        ]);

        $this->siswa = Siswa::create([
            'nisn' => '0099887766',
            'nis' => '2026001099',
            'nama' => 'Dimas Pratama',
            'id_kelas' => $this->kelas->id,
            'alamat' => 'Jl. Kampus 99',
            'no_telp' => '081299887766',
            'id_spp' => $this->spp->id,
        ]);

        $this->studentUser = User::factory()->create([
            'name' => 'Dimas Pratama',
            'role' => 'mahasiswa',
            'id_siswa' => $this->siswa->id,
        ]);
    }

    /**
     * Test 1: Attendance Telemetry Archival (Scrubbing GPS Data & Dry Run)
     */
    public function test_attendance_telemetry_archival_command_scrubs_old_records_safely(): void
    {
        // Create stale attendance record with active GPS coordinates (older than 200 days)
        $oldDate = Carbon::now()->subDays(200);

        $record = PresensiMahasiswa::create([
            'id_kelas_kuliah' => $this->kelasKuliah->id,
            'id_mahasiswa' => $this->mahasiswa->id,
            'status' => 'Hadir',
            'waktu_hadir' => $oldDate,
            'latitude' => -6.2000000,
            'longitude' => 106.8166667,
            'device_fingerprint' => 'BROWSER_SHA256_FINGERPRINT_SAMPLE',
        ]);

        // 1. Dry run execution: Record must remain untouched
        $this->artisan('attendance:archive-telemetry', ['--dry-run' => true])
            ->assertSuccessful();

        $record->refresh();
        $this->assertEquals(-6.2000000, (float) $record->latitude);
        $this->assertEquals(106.8166667, (float) $record->longitude);
        $this->assertNotNull($record->device_fingerprint);

        // 2. Real execution: Coordinates scrubbed to 0.0 and fingerprint nulled
        $this->artisan('attendance:archive-telemetry', ['--days' => 180])
            ->assertSuccessful();

        $record->refresh();
        $this->assertEquals(0.0, (float) $record->latitude);
        $this->assertEquals(0.0, (float) $record->longitude);
        $this->assertNull($record->device_fingerprint);
        // Academic presence history remains intact
        $this->assertEquals('Hadir', $record->status);
    }

    /**
     * Test 2: Public Payment Lookup Query Count Bounds
     */
    public function test_public_cek_tagihan_query_count_is_strictly_bounded(): void
    {
        DB::enableQueryLog();

        $response = $this->get(route('cek.index'));
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Landing page query count must be bounded to <= 3 queries
        $this->assertLessThanOrEqual(3, count($queries), "Public cek tagihan landing exceeded query budget.");
    }

    /**
     * Test 3: Authenticated Student Dashboard Latency Benchmark
     */
    public function test_student_dashboard_response_time_benchmark(): void
    {
        $startTime = microtime(true);

        $response = $this->actingAs($this->studentUser)->get('/dashboard');
        $response->assertStatus(200);

        $executionTimeMs = (microtime(true) - $startTime) * 1000;

        // Dashboard render must complete under 1500ms in testing environment
        $this->assertLessThan(1500, $executionTimeMs, "Student dashboard exceeded latency threshold.");
    }
}
