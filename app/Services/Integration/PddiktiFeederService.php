<?php

namespace App\Services\Integration;

use App\Models\KelasKuliah;
use App\Models\PddiktiSyncLog;
use App\Models\Siswa;
use Exception;

class PddiktiFeederService
{
    /**
     * Format internal Student record to PDDIKTI Feeder Mahasiswa payload
     */
    public function formatMahasiswaPayload(Siswa $siswa): array
    {
        return [
            'nama_mahasiswa' => $siswa->nama,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2004-05-15',
            'id_agama' => 1, // Islam
            'nik' => '3273' . str_pad($siswa->id, 12, '0', STR_PAD_LEFT),
            'nisn' => $siswa->nisn,
            'kewarganegaraan' => 'ID',
            'jalan' => $siswa->alamat,
            'handphone' => $siswa->no_telp,
            'id_prodi' => '62201', // Standard PDDIKTI Prodi Code
        ];
    }

    /**
     * Format internal Class & Course record to PDDIKTI Feeder KelasKuliah payload
     */
    public function formatKelasKuliahPayload(KelasKuliah $kelas): array
    {
        return [
            'id_prodi' => '62201',
            'id_semester' => $kelas->tahunAkademik?->kode_tahun ?? '20251',
            'id_matkul' => $kelas->mataKuliah?->kode_mk,
            'nama_kelas_kuliah' => $kelas->nama_kelas,
            'sks' => $kelas->mataKuliah?->sks_total ?? 2,
            'kuota' => $kelas->kuota_maksimal,
        ];
    }

    /**
     * Dispatch sync request to PDDIKTI WebService / Feeder API Mock
     */
    public function syncRecord(string $tipeEntitas, string $idLokal, array $payload): PddiktiSyncLog
    {
        $log = PddiktiSyncLog::create([
            'tipe_entitas' => $tipeEntitas,
            'id_entitas_lokal' => $idLokal,
            'status_sync' => 'PENDING',
            'payload_terkirim' => $payload,
        ]);

        try {
            // Simulated WebService Call (WS Feeder Sandbox)
            $feederId = 'PDDIKTI-WS-' . strtoupper(substr(md5(json_encode($payload)), 0, 16));
            
            $log->update([
                'id_feeder_pddikti' => $feederId,
                'status_sync' => 'SUCCESS',
                'response_feeder' => [
                    'error_code' => 0,
                    'error_desc' => null,
                    'result' => ['id_pddikti' => $feederId],
                ],
                'synced_at' => now(),
            ]);
        } catch (Exception $e) {
            $log->update([
                'status_sync' => 'FAILED',
                'pesan_error' => $e->getMessage(),
            ]);
        }

        return $log;
    }
}
