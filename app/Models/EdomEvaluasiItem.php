<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EdomEvaluasiItem extends Model
{
    use HasFactory;

    protected $table = 'edom_evaluasi_items';

    protected $fillable = [
        'id_edom_evaluasi',
        'id_edom_pertanyaan',
        'skor_nilai',
    ];

    public function evaluasi(): BelongsTo
    {
        return $this->belongsTo(EdomEvaluasi::class, 'id_edom_evaluasi');
    }

    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(EdomPertanyaan::class, 'id_edom_pertanyaan');
    }
}
