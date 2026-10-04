<?php

namespace App\Services\Academic;

use App\Models\MbkmKonversi;
use App\Models\MbkmKonversiDetail;
use App\Services\Audit\AuditTrailService;
use Exception;
use Illuminate\Support\Facades\DB;

class MbkmConversionService
{
    public function __construct(
        protected AuditTrailService $auditService
    ) {}

    /**
     * Map 1 External MBKM Activity (e.g., Magang Industri 20 SKS) into N Internal Courses
     *
     * @param array $activityData ['id_siswa', 'nama_program_eksternal', 'mitra_mbkm', 'total_sks_diakui']
     * @param array $courseMappings Array of ['id_matakuliah', 'sks_diakui', 'nilai_angka', 'nilai_huruf', 'bobot_mutu']
     * @return MbkmKonversi
     * @throws Exception
     */
    public function convertSingleActivityToMultipleCourses(array $activityData, array $courseMappings): MbkmKonversi
    {
        if (empty($courseMappings)) {
            throw new Exception('Konversi MBKM gagal: Minimal 1 mata kuliah internal harus dipetakan.');
        }

        return DB::transaction(function () use ($activityData, $courseMappings) {
            // 1. Calculate Aggregate SKS Total
            $totalConvertedSks = array_reduce($courseMappings, function ($carry, $item) {
                return $carry + ($item['sks_diakui'] ?? 0);
            }, 0);

            // 2. Create Header Record (1 MBKM Activity)
            $header = MbkmKonversi::create([
                'id_siswa' => $activityData['id_siswa'],
                'id_mk' => $courseMappings[0]['id_matakuliah'], // Primary mapped course
                'nama_program_eksternal' => $activityData['nama_program_eksternal'],
                'mitra_mbkm' => $activityData['mitra_mbkm'],
                'sks_diakui' => $totalConvertedSks,
                'nilai_angka' => $courseMappings[0]['nilai_angka'] ?? 90.00,
                'nilai_huruf' => $courseMappings[0]['nilai_huruf'] ?? 'A',
                'status_verifikasi' => 'APPROVED',
                'pejabat_pengesah' => auth()->user()?->name ?? 'Dekan / Kaprodi',
                'tgl_pengesahan' => now(),
            ]);

            // 3. Create N Detail Entries (1-to-N Course Conversion)
            foreach ($courseMappings as $mapping) {
                MbkmKonversiDetail::create([
                    'id_mbkm_konversi' => $header->id,
                    'id_matakuliah' => $mapping['id_matakuliah'],
                    'sks_diakui' => $mapping['sks_diakui'],
                    'nilai_angka_konversi' => $mapping['nilai_angka'] ?? 90.00,
                    'nilai_huruf_konversi' => $mapping['nilai_huruf'] ?? 'A',
                    'bobot_mutu' => $mapping['bobot_mutu'] ?? 4.00,
                    'catatan_dosen_pa' => $mapping['catatan'] ?? 'Disetujui Konversi MBKM 20 SKS',
                ]);
            }

            // 4. Record Audit Log
            $this->auditService->record(
                auth()->id() ?? 1,
                'MBKM_1TO_N_CONVERSION_SUCCESS',
                [
                    'id_siswa' => $activityData['id_siswa'],
                    'id_mbkm_konversi' => $header->id,
                    'total_courses_mapped' => count($courseMappings),
                    'total_sks_diakui' => $totalConvertedSks,
                ],
                null,
                $header->load('details')->toArray()
            );

            return $header;
        });
    }
}
