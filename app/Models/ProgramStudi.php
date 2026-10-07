<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramStudi extends Model
{
    use HasFactory;

    protected $table = 'program_studis';

    protected $fillable = [
        'id_fakultas',
        'kode_prodi',
        'nama_prodi',
        'nama_prodi_en',
        'jenjang',
        'akreditasi',
        'kaprodi',
        'nip_kaprodi',
    ];

    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas');
    }

    public function kurikulums(): HasMany
    {
        return $this->hasMany(Kurikulum::class, 'id_prodi');
    }

    public function cpls(): HasMany
    {
        return $this->hasMany(Cpl::class, 'id_prodi');
    }

    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'id_prodi');
    }

    public function dosens(): HasMany
    {
        return $this->hasMany(Dosen::class, 'id_prodi');
    }
}
