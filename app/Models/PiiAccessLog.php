<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PiiAccessLog extends Model
{
    protected $table = 'pii_access_logs';

    protected $fillable = [
        'user_id',
        'target_model',
        'target_id',
        'accessed_field',
        'ip_address',
        'user_agent',
        'purpose_reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
