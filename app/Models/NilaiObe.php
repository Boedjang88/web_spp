<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiObe extends Model
{
    use HasFactory;

    protected $table = 'nilai_obes';

    protected $fillable = [
        'id_krs_detail',
        'id_cpmk',
        'skor_pencapaian',
        'is_terpenuhi',
    ];

    protected $casts = [
        'skor_pencapaian' => 'decimal:2',
        'is_terpenuhi' => 'boolean',
    ];

    public function krsDetail(): BelongsTo
    {
        return $this->belongsTo(KrsDetail::class, 'id_krs_detail');
    }

    public function cpmk(): BelongsTo
    {
        return $this->belongsTo(Cpmk::class, 'id_cpmk');
    }
}
