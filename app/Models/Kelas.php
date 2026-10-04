<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    
    // Izinkan kolom ini diisi oleh Form Filament
    protected $fillable = [
        'nama_kelas',
        'kompetensi_keahlian'
    ];

    public function siswas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Siswa::class, 'id_kelas');
    }
}