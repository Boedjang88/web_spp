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

    public function getBabAttribute(): string
    {
        if (preg_match('/^\[(.*?)\]/', (string) $this->catatan_kemajuan_mahasiswa, $matches)) {
            return $matches[1];
        }
        return 'Bimbingan';
    }

    public function getMateriBimbinganAttribute(): string
    {
        $text = (string) $this->catatan_kemajuan_mahasiswa;
        return trim(preg_replace('/^\[(.*?)\]\s*/', '', $text)) ?: $text;
    }

    public function getCatatanDosenAttribute(): ?string
    {
        return $this->arahan_dosen_pembimbing;
    }
}
