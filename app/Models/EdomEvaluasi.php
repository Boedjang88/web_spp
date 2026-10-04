<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdomEvaluasi extends Model
{
    use HasFactory;

    protected $table = 'edom_evaluasis';

    protected $fillable = [
        'id_krs_detail',
        'id_guru',
        'id_siswa',
        'skor_rata_rata',
        'kritik_saran',
    ];

    protected $casts = [
        'skor_rata_rata' => 'decimal:2',
    ];

    public function krsDetail(): BelongsTo
    {
        return $this->belongsTo(KrsDetail::class, 'id_krs_detail');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EdomEvaluasiItem::class, 'id_edom_evaluasi');
    }
}
