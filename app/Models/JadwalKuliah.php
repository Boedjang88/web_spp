<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalKuliah extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kuliahs';

    protected $fillable = [
        'id_kelas_kuliah',
        'id_ruangan',
        'id_guru',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function kelasKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}
