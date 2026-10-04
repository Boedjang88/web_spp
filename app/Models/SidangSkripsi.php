<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SidangSkripsi extends Model
{
    use HasFactory;

    protected $table = 'sidang_skripsis';

    protected $fillable = [
        'id_tugas_akhir',
        'id_ruangan',
        'jenis_sidang',
        'waktu_sidang',
        'nilai_rata_rata',
        'nilai_huruf',
        'hasil_keputusan',
        'catatan_revisi_sidang',
        'batas_waktu_revisi',
    ];

    protected $casts = [
        'waktu_sidang' => 'datetime',
        'nilai_rata_rata' => 'decimal:2',
        'batas_waktu_revisi' => 'date',
    ];

    public function tugasAkhir(): BelongsTo
    {
        return $this->belongsTo(TugasAkhir::class, 'id_tugas_akhir');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(PenilaianSidang::class, 'id_sidang');
    }
}
