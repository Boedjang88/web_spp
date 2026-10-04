<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangans';

    protected $fillable = [
        'id_gedung',
        'kode_ruangan',
        'nama_ruangan',
        'kapasitas',
        'jenis_ruangan',
        'latitude',
        'longitude',
        'radius_meter',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'id_gedung');
    }

    public function jadwalKuliahs(): HasMany
    {
        return $this->hasMany(JadwalKuliah::class, 'id_ruangan');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(BookingFasilitas::class, 'id_ruangan');
    }
}
