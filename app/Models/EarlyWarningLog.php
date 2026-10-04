<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EarlyWarningLog extends Model
{
    use HasFactory;

    protected $table = 'early_warning_logs';

    protected $fillable = [
        'id_siswa',
        'id_dosen_pa',
        'severity',
        'trigger_type',
        'trigger_reason',
        'metrics_payload',
        'is_resolved',
        'resolved_at',
        'notes',
    ];

    protected $casts = [
        'metrics_payload' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function dosenPa(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_dosen_pa');
    }

    public function scopeActive($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'CRITICAL');
    }
}
