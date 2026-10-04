<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nik',
        'nama_guru',
        'jenis_kelamin',
        'no_telp',
        'email',
        'alamat',
        'consent_pdp_at',
        'consent_pdp_ip',
    ];

    protected $casts = [
        'nik' => 'encrypted',
        'consent_pdp_at' => 'datetime',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id_guru');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_guru');
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class, 'id_guru');
    }
}
