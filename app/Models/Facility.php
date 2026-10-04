<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    protected $table = 'facilities';

    protected $fillable = [
        'nama_fasilitas',
        'kode_fasilitas',
        'kategori',
        'id_ruangan',
        'kapasitas',
        'status_fasilitas',
        'deskripsi',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class, 'id_facility');
    }
}
