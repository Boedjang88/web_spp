<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a new activity log entry.
     */
    public static function record(string $action, string $description, ?int $userId = null): self
    {
        $request = request();

        return self::create([
            'user_id' => $userId ?? auth('sanctum')->id() ?? auth()->id(),
            'action' => strtoupper($action),
            'description' => $description,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? substr($request->userAgent() ?? '', 0, 255) : null,
        ]);
    }
}
