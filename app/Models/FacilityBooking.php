<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityBooking extends Model
{
    protected $table = 'facility_bookings';

    protected $fillable = [
        'id_facility',
        'id_pemohon',
        'tujuan_penggunaan',
        'tanggal_pinjam',
        'jam_mulai',
        'jam_selesai',
        'status_booking',
        'catatan_persetujuan',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'id_facility');
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pemohon');
    }
}
