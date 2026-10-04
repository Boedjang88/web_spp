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

    public function sidangs(): HasMany
    {
        return $this->hasMany(SidangSkripsi::class, 'id_tugas_akhir');
    }
}
