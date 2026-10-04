<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fakultas extends Model
{
    use HasFactory;

    protected $table = 'fakultas';

    protected $fillable = [
        'kode_fakultas',
        'nama_fakultas',
        'nama_fakultas_en',
        'dekan',
        'nip_dekan',
    ];

    public function programStudis(): HasMany
    {
        return $this->hasMany(ProgramStudi::class, 'id_fakultas');
    }
}
