<?php

namespace Tests\Unit;

use App\Models\Dosen;
use App\Models\EdomEvaluasi;
use App\Models\EdomPertanyaan;
use App\Models\EmployerFeedback;
use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\TracerStudy;
use App\Services\Analytics\TracerStudyService;
use App\Services\Evaluation\EdomEvaluationService;
use App\Services\Security\TranskripVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportingServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_edom_evaluation_submission_and_index_calculation(): void
    {
        $fakultas = Fakultas::create(['kode_fakultas' => 'FTI', 'nama_fakultas' => 'Fakultas Teknologi Informasi']);
        $prodi = ProgramStudi::create(['id_fakultas' => $fakultas->id, 'kode_prodi' => 'IF', 'nama_prodi' => 'Informatika', 'jenjang' => 'S1']);
        $kelas = Kelas::create(['nama_kelas' => 'IF-4A', 'kompetensi_keahlian' => 'TIF']);
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 500000]);
        $siswa = Siswa::create([
            'nama' => 'Rina Wijaya',
            'nisn' => '20250011',
            'nis' => '1001',
            'alamat' => 'Jl. Dago No. 10, Bandung',
            'no_telp' => '081234567891',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
        ]);
        $dosen = Dosen::create([
            'nidn' => '0015058001',
            'nama_dosen' => 'Dr. Eko Prasetyo, M.T.',
            'jenis_kelamin' => 'L',
            'email' => 'eko.prasetyo@univ.ac.id',
            'no_hp' => '081234567800',
        ]);
        $tahun = TahunAkademik::create([
            'kode_tahun' => '20251',
            'nama_tahun' => '2025/2026 Ganjil',
            'semester' => 'Ganjil',
            'tgl_mulai' => '2025-09-01',
            'tgl_selesai' => '2026-01-31',
            'is_aktif' => true,
        ]);
        $kurikulum = Kurikulum::create([
            'id_prodi' => $prodi->id,
            'nama_kurikulum' => 'Kurikulum OBE 2024',
            'tahun_mulai' => 2024,
            'is_aktif' => true,
        ]);
        $mk = MataKuliah::create([
            'kode_mk' => 'IF101',
            'nama_mk' => 'Pemrograman Lanjut',
            'sks_total' => 3,
            'semester' => 1,
            'id_kurikulum' => $kurikulum->id,
        ]);
        $kelasKuliah = KelasKuliah::create([
            'nama_kelas' => 'IF-4A',
            'id_tahun_akademik' => $tahun->id,
            'id_mk' => $mk->id,
            'id_dosen' => $dosen->id,
        ]);
        $krs = Krs::create(['id_siswa' => $siswa->id, 'id_tahun_akademik' => $tahun->id, 'status' => 'APPROVED']);
        $krsDetail = KrsDetail::create(['id_krs' => $krs->id, 'id_kelas_kuliah' => $kelasKuliah->id]);

        $p1 = EdomPertanyaan::create(['teks_pertanyaan' => 'Penguasaan Materi', 'kategori' => 'Pedagogik']);
        $p2 = EdomPertanyaan::create(['teks_pertanyaan' => 'Kedisiplinan Hadir', 'kategori' => 'Profesional']);

        $edomService = new EdomEvaluationService();
        $evaluasi = $edomService->submitEvaluation($krsDetail->id, [
            $p1->id => 5,
            $p2->id => 4,
        ], 'Dosen sangat baik');

        $this->assertInstanceOf(EdomEvaluasi::class, $evaluasi);
        $this->assertEquals(4.5, $evaluasi->skor_rata_rata);

        $index = $edomService->calculateLecturerEdomIndex($dosen->id, $tahun->id);
        $this->assertEquals(1, $index['total_responden']);
        $this->assertEquals(4.5, $index['indeks_edom']);
        $this->assertEquals('SANGAT BAIK (A)', $index['kategori']);
    }

    public function test_tracer_study_accreditation_summary_computation(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'SI-4A', 'kompetensi_keahlian' => 'SI']);
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 500000]);
        $siswa = Siswa::create([
            'nama' => 'Andi Lulusan',
            'nisn' => '20210099',
            'nis' => '1099',
            'alamat' => 'Jl. Asia Afrika No. 50, Bandung',
            'no_telp' => '081234567892',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
        ]);

        $tracer = TracerStudy::create([
            'id_siswa' => $siswa->id,
            'tahun_lulus' => 2025,
            'status_alumni' => 'Bekerja',
            'nama_instansi_kerja' => 'PT Tech Utama',
            'jabatan_posisi' => 'Software Engineer',
            'masa_tunggu_bulan' => 2,
            'gaji_pertama' => 8500000,
            'keselarasan_bidang' => 'Sangat Selaras',
        ]);

        EmployerFeedback::create([
            'id_tracer_study' => $tracer->id,
            'access_token' => 'TOK-TEST-123456',
            'nama_penilai_atasan' => 'Bapak Manager',
            'jabatan_penilai' => 'Manager HRD',
            'email_perusahaan' => 'hrd@techutama.com',
            'nama_perusahaan' => 'PT Tech Utama',
            'skor_integritas_etika' => 5,
            'skor_keahlian_bidang' => 5,
            'skor_bahasa_asing' => 4,
            'skor_penggunaan_ti' => 5,
            'skor_komunikasi' => 4,
            'skor_kerjasama_tim' => 5,
            'skor_pengembangan_diri' => 5,
            'saran_kurikulum' => 'Sangat memuaskan',
        ]);

        $tracerService = new TracerStudyService();
        $summary = $tracerService->computeAccreditationTracerSummary(null, 2025);

        $this->assertEquals(1, $summary['total_responden']);
        $this->assertEquals(100.0, $summary['persentase_bekerja']);
        $this->assertEquals(2.0, $summary['rata_waktu_tunggu_bulan']);
        $this->assertEquals(8500000, $summary['rata_gaji_pertama']);
        $this->assertEquals(4.71, $summary['indeks_kepuasan_pengguna_lulusan']);
    }

    public function test_transkrip_verification_signature_and_qr_payload(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'IF-1A', 'kompetensi_keahlian' => 'IF']);
        $spp = Spp::create(['tahun' => 2025, 'nominal' => 500000]);
        $siswa = Siswa::create([
            'nama' => 'Dewi Sartika',
            'nisn' => '20259900',
            'nis' => '9900',
            'alamat' => 'Jl. Sunda No. 12, Bandung',
            'no_telp' => '081234567893',
            'id_kelas' => $kelas->id,
            'id_spp' => $spp->id,
        ]);

        $verifierService = new TranskripVerificationService();
        $payload = $verifierService->generateDocumentVerificationPayload($siswa, 'TRANSKRIP');

        $this->assertEquals('TRANSKRIP', $payload['document_type']);
        $this->assertEquals('Dewi Sartika', $payload['student_name']);
        $this->assertNotEmpty($payload['hmac_signature']);
        $this->assertStringContainsString('SIAKAD-OFFICIAL-DOC', $payload['qr_data']);
    }
}
