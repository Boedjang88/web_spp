<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagihanVaItem extends Model
{
    use HasFactory;

    protected $table = 'tagihan_va_items';

    protected $fillable = [
        'id_tagihan_va',
        'nama_item',
        'nominal',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function tagihanVa(): BelongsTo
    {
        return $this->belongsTo(TagihanVa::class, 'id_tagihan_va');
    }
}
