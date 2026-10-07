<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beasiswa extends Model
{
    use HasFactory;

    protected $table = 'beasiswas';

    protected $fillable = [
        'nama_beasiswa',
        'penyelenggara',
        'jenis_cakupan',
        'persentase_potongan',
        'nominal_potongan',
        'kuota',
        'is_active',
    ];

    protected $casts = [
        'persentase_potongan' => 'decimal:2',
        'nominal_potongan' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(PendaftaranBeasiswa::class, 'id_beasiswa');
    }
}
