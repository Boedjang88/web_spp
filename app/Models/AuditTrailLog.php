<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTrailLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'audit_trail_logs';

    protected $fillable = [
        'id_user',
        'action_event',
        'ip_address',
        'user_agent',
        'url_endpoint',
        'request_payload',
        'state_before',
        'state_after',
        'timestamp_microseconds',
        'created_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'state_before' => 'array',
        'state_after' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
