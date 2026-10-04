<?php

namespace App\Traits;

use App\Models\PiiAccessLog;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

trait EncryptsPiiData
{
    /**
     * Get masked PII attribute for standard user display (e.g., 327312******0001)
     */
    public function getMaskedAttribute(string $key): string
    {
        $value = (string) ($this->attributes[$key] ?? '');

        // Try decrypting if attribute is encrypted in database
        try {
            $value = Crypt::decryptString($value);
        } catch (\Throwable $e) {}

        if (strlen($value) <= 6) {
            return str_repeat('*', strlen($value));
        }

        return substr($value, 0, 4) . str_repeat('*', strlen($value) - 8) . substr($value, -4);
    }

    /**
     * Get unmasked sensitive attribute with UU PDP Audit Log recording
     */
    public function getUnmaskedPii(string $key, ?string $reason = 'ADMIN_EXPLICIT_VIEW'): string
    {
        $rawValue = (string) ($this->attributes[$key] ?? '');
        $decryptedValue = $rawValue;

        try {
            $decryptedValue = Crypt::decryptString($rawValue);
        } catch (\Throwable $e) {}

        // Audit UU PDP Access Event
        if (auth()->check()) {
            PiiAccessLog::create([
                'user_id' => auth()->id(),
                'target_model' => static::class,
                'target_id' => (string) $this->getKey(),
                'accessed_field' => $key,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'purpose_reason' => $reason,
            ]);
        }

        return $decryptedValue;
    }
}
