<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookBimbingan extends Model
{
    use HasFactory;

    protected $table = 'logbook_bimbingans';

    protected $fillable = [
        'id_tugas_akhir',
        'id_guru',
        'tanggal_bimbingan',
        'catatan_kemajuan_mahasiswa',
        'arahan_dosen_pembimbing',
        'file_lampiran_draft',
        'status_acc',
        'tgl_disetujui',
    ];

    protected $casts = [
        'tanggal_bimbingan' => 'date',
        'tgl_disetujui' => 'datetime',
    ];

    public function tugasAkhir(): BelongsTo
    {
        return $this->belongsTo(TugasAkhir::class, 'id_tugas_akhir');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}
