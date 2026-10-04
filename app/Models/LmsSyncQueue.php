<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsSyncQueue extends Model
{
    use HasFactory;

    protected $table = 'lms_sync_queues';

    protected $fillable = [
        'id_kelas_kuliah',
        'event_type',
        'lms_course_id',
        'status',
        'retry_count',
        'error_log',
    ];

    public function kelasKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah');
    }
}
