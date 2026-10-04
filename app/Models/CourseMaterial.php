<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $table = 'course_materials';

    protected $fillable = [
        'id_kelas_kuliah',
        'judul',
        'deskripsi',
        'file_path',
        'original_filename',
        'file_type',
        'file_size',
        'minggu_ke',
        'publish_at',
        'is_active',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'is_active' => 'boolean',
        'minggu_ke' => 'integer',
        'file_size' => 'integer',
    ];

    public function kelasKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah');
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('publish_at')
                  ->orWhere('publish_at', '<=', now());
            });
    }

    public function scopeAvailableForStudent($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('publish_at')
                  ->orWhere('publish_at', '<=', now());
            });
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->publish_at && $this->publish_at->isFuture()) {
            return false;
        }
        return true;
    }
}
