<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankMitra extends Model
{
    use HasFactory;

    protected $table = 'bank_mitras';

    protected $fillable = [
        'kode_bank',
        'nama_bank',
        'prefix_va',
        'secret_key',
        'webhook_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tagihanVas(): HasMany
    {
        return $this->hasMany(TagihanVa::class, 'id_bank_mitra');
    }
}
