<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MataKuliahPrasyarat extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah_prasyarats';

    protected $fillable = [
        'id_mk',
        'id_mk_prasyarat',
        'nilai_minimum',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mk');
    }

    public function mataKuliahPrasyarat(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mk_prasyarat');
    }
}
