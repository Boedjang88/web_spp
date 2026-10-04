<?php

namespace App\Services\Security;

use App\Models\User;
use Illuminate\Support\Str;

class MfaSsoService
{
    /**
     * Generate Base32 Secret Key for TOTP MFA
     */
    public function generateTotpSecret(): string
    {
        return strtoupper(Str::random(16));
    }

    /**
     * Generate dynamic 6-digit TOTP Code for testing & verification
     */
    public function calculateTotpCode(string $secret, ?int $timestamp = null): string
    {
        $timeSlice = floor(($timestamp ?? time()) / 30);
        $hash = hash_hmac('sha1', pack('N*', 0) . pack('N*', $timeSlice), $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncatedHash = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        return str_pad((string) ($truncatedHash % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify submitted TOTP Code against User's MFA Secret Key
     */
    public function verifyTotpCode(User $user, string $code): bool
    {
        if (!$user->mfa_enabled || !$user->mfa_secret) {
            return true; // MFA not required for this user
        }

        // Allow +- 1 time window (30 seconds tolerance)
        $currentTime = time();
        for ($i = -1; $i <= 1; $i++) {
            $testCode = $this->calculateTotpCode($user->mfa_secret, $currentTime + ($i * 30));
            if (hash_equals($testCode, $code)) {
                return true;
            }
        }

        return false;
    }
}
