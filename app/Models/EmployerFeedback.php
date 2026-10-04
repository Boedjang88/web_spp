<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerFeedback extends Model
{
    use HasFactory;

    protected $table = 'employer_feedbacks';

    protected $fillable = [
        'id_tracer_study',
        'access_token',
        'nama_penilai_atasan',
        'jabatan_penilai',
        'email_perusahaan',
        'nama_perusahaan',
        'skor_integritas_etika',
        'skor_keahlian_bidang',
        'skor_bahasa_asing',
        'skor_penggunaan_ti',
        'skor_komunikasi',
        'skor_kerjasama_tim',
        'skor_pengembangan_diri',
        'saran_kurikulum',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function tracerStudy(): BelongsTo
    {
        return $this->belongsTo(TracerStudy::class, 'id_tracer_study');
    }
}
