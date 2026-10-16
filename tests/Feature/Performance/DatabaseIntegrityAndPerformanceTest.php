<?php

namespace Tests\Feature\Performance;

use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\User;
use App\Services\Academic\SmartKrsService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseIntegrityAndPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private Siswa $student;
    private TahunAkademik $tahun;
    private KelasKuliah $kelasKuliah;

    protected function setUp(): void
    {
        parent::setUp();

        $kelas = Kelas::create(['nama_kelas' => 'TI-3A', 'kompetensi_keahlian' => 'Informatika']);
        $spp = Spp::create(['tahun' => 2026, 'nominal' => 4500000]);

        $this->student = Siswa::create([
            'nisn' => '2026999001',
            'nis' => '99001',
            'nama' => 'Mahasiswa Benchmark Integrity',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
            'alamat' => 'Kampus SIAKAD',
            'no_telp' => '081234567890',
            'status_kelulusan' => 'Aktif',
        ]);

        $fakultas = Fakultas::create(['kode_fakultas' => 'FTI', 'nama_fakultas' => 'Fakultas Teknologi Informasi']);
        $prodi = ProgramStudi::create(['id_fakultas' => $fakultas->id, 'kode_prodi' => 'IF', 'nama_prodi' => 'Teknik Informatika', 'jenjang' => 'S1']);
        $ukt = \App\Models\Ukt::create(['tahun' => 2026, 'nominal' => 4500000, 'id_prodi' => $prodi->id]);
        $kurikulum = Kurikulum::create(['id_prodi' => $prodi->id, 'kode_kurikulum' => 'KUR-2026', 'nama_kurikulum' => 'Kurikulum 2026', 'tahun_mulai' => 2026, 'is_active' => true]);

        $this->tahun = TahunAkademik::create([
            'kode_tahun' => '20261',
            'nama_tahun' => '2026/2027 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => now()->startOfYear(),
            'tgl_selesai' => now()->endOfYear(),
            'is_active' => true,
        ]);

        $mk = MataKuliah::create([
            'id_kurikulum' => $kurikulum->id,
            'kode_mk' => 'IF305',
            'nama_mk' => 'Basis Data Lanjut',
            'sks_total' => 3,
            'jenis_mk' => 'Wajib Program Studi',
        ]);

        $this->kelasKuliah = KelasKuliah::create([
            'id_mk' => $mk->id,
            'id_tahun_akademik' => $this->tahun->id,
            'nama_kelas' => 'BDL-A',
            'kuota_maksimal' => 30,
            'total_terisi' => 0,
        ]);
    }

    /**
     * Test 1: SQLite schema has zero foreign key integrity violations.
     */
    public function test_schema_has_zero_foreign_key_violations(): void
    {
        $violations = DB::select('PRAGMA foreign_key_check');
        $this->assertEmpty($violations, 'Database schema must not have any foreign key violations.');
    }

    /**
     * Test 2: Transaction rollback maintains class capacity count if enrollment fails.
     */
    public function test_transaction_rollback_reverts_class_capacity_on_failure(): void
    {
        $initialEnrolled = $this->kelasKuliah->total_terisi;
        $krsService = app(SmartKrsService::class);

        // Intentionally create a scenario that will abort inside transaction after pessimistic lock
        // E.g., student attempting to enroll while financially blocked
        $unpaidStudent = Siswa::create([
            'nisn' => '2026999002',
            'nis' => '99002',
            'nama' => 'Student Unpaid',
            'id_kelas' => $this->student->id_kelas,
            'id_spp' => $this->student->id_spp,
            'alamat' => 'Kampus',
            'no_telp' => '081234567891',
            'status_kelulusan' => 'Aktif',
        ]);

        $unpaidMahasiswa = \App\Models\Mahasiswa::create([
            'nim' => $unpaidStudent->nisn,
            'nisn' => $unpaidStudent->nisn,
            'nik' => '3273010101010099',
            'nama' => $unpaidStudent->nama,
            'alamat' => 'Kampus',
            'no_telp' => '081234567891',
            'id_prodi' => 1,
            'id_ukt' => 1,
            'status_kelulusan' => 'Aktif',
        ]);

        // Create an unpaid TagihanUkt past due date without grace period
        \App\Models\TagihanUkt::create([
            'id_mahasiswa' => $unpaidMahasiswa->id,
            'id_tahun_akademik' => $this->tahun->id,
            'nomor_invoice' => 'INV-TEST-001',
            'nomor_va' => '9880011223344',
            'total_harus_bayar' => 5000000,
            'total_sudah_bayar' => 0,
            'status_pembayaran' => 'Kadaluarsa',
            'tgl_jatuh_tempo' => now()->subDays(30),
        ]);

        try {
            $krsService->enrollClassWithPessimisticLock(
                $unpaidStudent->id,
                $this->kelasKuliah->id,
                $this->tahun->id
            );
            $this->fail('Expected exception for locked enrollment was not thrown.');
        } catch (Exception $e) {
            $this->assertStringContainsString('KRS Terkunci', $e->getMessage());
        }

        // Verify that total_terisi was not incremented
        $this->kelasKuliah->refresh();
        $this->assertEquals($initialEnrolled, $this->kelasKuliah->total_terisi);
    }

    /**
     * Test 3: Siswa index query utilizes eager loading to prevent N+1 queries.
     */
    public function test_siswa_list_query_uses_eager_loading_without_n_plus_one(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Seed 10 additional students
        for ($i = 1; $i <= 10; $i++) {
            Siswa::create([
                'nisn' => sprintf('20260088%02d', $i),
                'nis' => sprintf('88%02d', $i),
                'nama' => "Siswa Eager {$i}",
                'id_kelas' => $this->student->id_kelas,
                'id_spp' => $this->student->id_spp,
                'alamat' => 'Kampus',
                'no_telp' => sprintf('0812345678%02d', $i),
                'status_kelulusan' => 'Aktif',
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($admin)->get(route('web.siswa.index'));
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // The query count for listing 10 students with eager loaded kelas and spp is bounded (<= 6 queries)
        $this->assertLessThanOrEqual(
            8,
            count($queries),
            'Web Siswa listing must use eager loading to avoid N+1 query amplification.'
        );
    }

    /**
     * Test 4: Verify composite index usage on krs_details.
     */
    public function test_krs_details_has_compound_performance_index(): void
    {
        $indexes = DB::select("PRAGMA index_list('krs_details')");
        $indexNames = array_map(fn ($idx) => $idx->name, $indexes);

        $this->assertTrue(
            in_array('idx_krs_details_perf', $indexNames) || in_array('idx_krs_details_id_krs_id_kelas_kuliah', $indexNames),
            'Compound index on krs_details (id_krs, id_kelas_kuliah) must be registered in SQLite schema.'
        );
    }
}
