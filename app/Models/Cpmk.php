<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cpmk extends Model
{
    use HasFactory;

    protected $table = 'cpmks';

    protected $fillable = [
        'id_mk',
        'id_cpl',
        'kode_cpmk',
        'deskripsi_cpmk_id',
        'deskripsi_cpmk_en',
        'bobot_persentase',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mk');
    }

    public function cpl(): BelongsTo
    {
        return $this->belongsTo(Cpl::class, 'id_cpl');
    }

    public function nilaiObes(): HasMany
    {
        return $this->hasMany(NilaiObe::class, 'id_cpmk');
    }
}
