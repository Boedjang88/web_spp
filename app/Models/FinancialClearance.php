<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialClearance extends Model
{
    use HasFactory;

    protected $table = 'financial_clearances';

    protected $fillable = [
        'id_siswa',
        'id_tahun_akademik',
        'is_krs_unlocked',
        'is_uts_unlocked',
        'is_uas_unlocked',
        'unlocked_at',
        'unlocked_by_channel',
    ];

    protected $casts = [
        'is_krs_unlocked' => 'boolean',
        'is_uts_unlocked' => 'boolean',
        'is_uas_unlocked' => 'boolean',
        'unlocked_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }
}
