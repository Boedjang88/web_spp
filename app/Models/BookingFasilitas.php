<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingFasilitas extends Model
{
    use HasFactory;

    protected $table = 'booking_fasilitas';

    protected $fillable = [
        'id_ruangan',
        'id_user',
        'nama_kegiatan',
        'organisasi_pemohon',
        'tanggal_booking',
        'jam_mulai',
        'jam_selesai',
        'status_persetujuan',
        'alasan_penolakan',
        'pejabat_approver',
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
