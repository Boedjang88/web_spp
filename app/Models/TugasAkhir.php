<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TugasAkhir extends Model
{
    use HasFactory;

    protected $table = 'tugas_akhirs';

    protected $fillable = [
        'id_siswa',
        'judul_skripsi',
        'judul_skripsi_en',
        'abstrak_id',
        'abstrak_en',
        'skor_plagiarisme_persen',
        'file_proposal_path',
        'file_naskah_akhir_path',
        'status_skripsi',
        'tgl_lulus_sidang',
        'judul',
        'abstrak',
        'status_persetujuan',
        'file_revisi_path',
    ];

    protected $casts = [
        'skor_plagiarisme_persen' => 'decimal:2',
        'tgl_lulus_sidang' => 'date',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function pembimbings(): HasMany
    {
        return $this->hasMany(PembimbingSkripsi::class, 'id_tugas_akhir');
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(LogbookBimbingan::class, 'id_tugas_akhir');
    }

    public function getJudulAttribute()
    {
        return $this->judul_skripsi;
    }

    public function setJudulAttribute($value)
    {
        $this->attributes['judul_skripsi'] = $value;
    }

    public function getAbstrakAttribute()
    {
        return $this->abstrak_id;
    }

    public function setAbstrakAttribute($value)
    {
        $this->attributes['abstrak_id'] = $value;
    }

    public function getStatusPersetujuanAttribute()
    {
        return $this->status_skripsi ?? 'Pengajuan Proposal';
    }

    public function setStatusPersetujuanAttribute($value)
    {
        $this->attributes['status_skripsi'] = $value;
    }

    public function getFileRevisiPathAttribute()
    {
        return $this->file_naskah_akhir_path;
    }

    public function setFileRevisiPathAttribute($value)
    {
        $this->attributes['file_naskah_akhir_path'] = $value;
    }

    public function sidangs(): HasMany
    {
        return $this->hasMany(SidangSkripsi::class, 'id_tugas_akhir');
    }
}
