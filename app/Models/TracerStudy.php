<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TracerStudy extends Model
{
    use HasFactory;

    protected $table = 'tracer_studies';

    protected $fillable = [
        'id_siswa',
        'tahun_lulus',
        'status_alumni',
        'nama_instansi_kerja',
        'jabatan_posisi',
        'gaji_pertama',
        'masa_tunggu_bulan',
        'keselarasan_bidang',
    ];

    protected $casts = [
        'gaji_pertama' => 'decimal:2',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function employerFeedback(): HasOne
    {
        return $this->hasOne(EmployerFeedback::class, 'id_tracer_study');
    }
}
