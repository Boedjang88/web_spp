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
        'gateway_provider',
        'prefix_va',
        'secret_key',
        'server_key',
        'client_key',
        'merchant_id',
        'environment',
        'webhook_url',
        'is_active',
    ];

    protected $hidden = [
        'secret_key',
        'server_key',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tagihanVas(): HasMany
    {
        return $this->hasMany(TagihanVa::class, 'id_bank_mitra');
    }

    public function isPaymentGateway(): bool
    {
        return in_array(strtoupper($this->gateway_provider ?? ''), ['MIDTRANS', 'XENDIT']);
    }

    public function isMidtrans(): bool
    {
        return strtoupper($this->gateway_provider ?? '') === 'MIDTRANS';
    }

    public function isXendit(): bool
    {
        return strtoupper($this->gateway_provider ?? '') === 'XENDIT';
    }
}
