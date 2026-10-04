<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cpl extends Model
{
    use HasFactory;

    protected $table = 'cpls';

    protected $fillable = [
        'id_prodi',
        'kode_cpl',
        'aspek',
        'deskripsi_cpl_id',
        'deskripsi_cpl_en',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'id_prodi');
    }

    public function cpmks(): HasMany
    {
        return $this->hasMany(Cpmk::class, 'id_cpl');
    }
}
