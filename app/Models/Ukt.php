<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ukt extends Model
{
    use HasFactory;

    protected $table = 'ukts';

    protected $fillable = [
        'id_prodi',
        'tahun',
        'kelompok_ukt',
        'nominal',
        'biaya_praktikum',
        'biaya_kemahasiswaan',
        'deskripsi',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'biaya_praktikum' => 'decimal:2',
        'biaya_kemahasiswaan' => 'decimal:2',
        'tahun' => 'integer',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'id_prodi');
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'id_ukt');
    }

    public function getTotalBiayaAttribute(): float
    {
        return (float) $this->nominal + (float) $this->biaya_praktikum + (float) $this->biaya_kemahasiswaan;
    }
}
