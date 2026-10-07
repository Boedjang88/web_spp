<?php

namespace App\Services\Thesis;

use App\Models\Guru;
use App\Models\LogbookBimbingan;
use App\Models\SidangSkripsi;
use App\Models\Siswa;
use App\Models\TugasAkhir;
use Exception;
use Illuminate\Support\Facades\DB;

class DbptaWorkflowService
{
    /**
     * DBPTA Stage 1: Proposal Submission & Title Registration
     */
    public function submitProposal(Siswa $siswa, string $judul, string $abstrak, ?string $fileProposalPath = null): TugasAkhir
    {
        return TugasAkhir::updateOrCreate(
            ['id_siswa' => $siswa->id],
            [
                'judul_skripsi' => $judul,
                'abstrak_id' => $abstrak,
                'status_skripsi' => 'Pengajuan Proposal',
                'file_proposal_path' => $fileProposalPath,
            ]
        );
    }

    /**
     * DBPTA Stage 2: Bimbingan Logbook Entry
     */
    public function addBimbinganLog(int $idTugasAkhir, int $idDosen, string $catatanBimbingan, ?string $fileDraftPath = null): LogbookBimbingan
    {
        $ta = TugasAkhir::findOrFail($idTugasAkhir);

        // Ensure Guru record exists for foreign key constraint
        $guru = Guru::firstOrCreate(
            ['id' => $idDosen],
            [
                'nip' => '19850115201001100' . $idDosen,
                'nama_guru' => 'Dosen Pembimbing ' . $idDosen,
                'jenis_kelamin' => 'L',
                'no_telp' => '081234567800',
                'email' => "dosen{$idDosen}@univ.ac.id",
                'alamat' => 'Kampus SIAKAD',
            ]
        );

        $log = LogbookBimbingan::create([
            'id_tugas_akhir' => $idTugasAkhir,
            'id_guru' => $guru->id,
            'catatan_kemajuan_mahasiswa' => 'Progres pengerjaan naskah',
            'arahan_dosen_pembimbing' => $catatanBimbingan,
            'tanggal_bimbingan' => now(),
            'file_lampiran_draft' => $fileDraftPath,
            'status_acc' => 'Disetujui',
            'tgl_disetujui' => now(),
        ]);

        $ta->update(['status_skripsi' => 'Bimbingan']);

        return $log;
    }

    /**
     * DBPTA Stage 3: Sidang & Yudisium Schedule Registration
     */
    public function registerSidangYudisium(int $idTugasAkhir, string $jenisSidang = 'Sidang Akhir'): SidangSkripsi
    {
        $ta = TugasAkhir::findOrFail($idTugasAkhir);

        $validTypes = ['Seminar Proposal', 'Seminar Hasil', 'Sidang Akhir'];
        if (!in_array($jenisSidang, $validTypes)) {
            $jenisSidang = 'Sidang Akhir';
        }

        $sidang = SidangSkripsi::create([
            'id_tugas_akhir' => $idTugasAkhir,
            'jenis_sidang' => $jenisSidang,
            'waktu_sidang' => now()->addDays(7),
            'hasil_keputusan' => 'Belum Sidang',
        ]);

        $ta->update(['status_skripsi' => 'Sidang Meja Hijau']);

        return $sidang;
    }

    /**
     * DBPTA Stage 4: Pemberkasan Final & Thesis Archiving
     */
    public function submitPemberkasanFinal(int $idTugasAkhir, string $fileFinalPath): TugasAkhir
    {
        $ta = TugasAkhir::findOrFail($idTugasAkhir);

        $ta->update([
            'status_skripsi' => 'Lulus Yudisium',
            'file_naskah_akhir_path' => $fileFinalPath,
            'tgl_lulus_sidang' => now(),
        ]);

        return $ta;
    }
}
