<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EdomPertanyaan extends Model
{
    use HasFactory;

    protected $table = 'edom_pertanyaans';

    protected $fillable = [
        'kategori',
        'teks_pertanyaan',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function evaluasiItems(): HasMany
    {
        return $this->hasMany(EdomEvaluasiItem::class, 'id_edom_pertanyaan');
    }
}
