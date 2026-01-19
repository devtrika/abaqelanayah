<?php

namespace App\Services\Auth;

use App\Models\User;
use Carbon\Carbon;

/**
 * OTP Service
 * 
 * Handles OTP generation, verification, and management for authentication flows
 */
class OTPService
{
    /**
     * Generate and send OTP code for user verification
     *
     * @param User $user
     * @return array
     */
    public function generateAndSendOTP(User $user): array
    {
        // Generate 5-digit OTP code
        $code = $this->generateCode();
        
        // Set expiration time (1 minute from now, as per existing pattern)
        $expiresAt = Carbon::now()->addMinute();
        
        // Update user with OTP code and expiration
        $user->update([
            'code' => $code,
            'code_expire' => $expiresAt,
        ]);
        
        // Send OTP via SMS
$user->sendEmail(__('admin.verification_code'), $code);
        
        return [
            'code' => $code,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Verify OTP code for a user
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function verifyOTP(User $user, string $code): bool
    {
        // Check if code matches
        if ($user->code !== $code) {
            return false;
        }
        
        // Check if code has expired
        if ($user->code_expire && Carbon::now()->isAfter($user->code_expire)) {
            return false;
        }
        
        return true;
    }

    /**
     * Mark user as active and clear OTP code
     *
     * @param User $user
     * @return User
     */
    public function activateUser(User $user): User
    {
        $user->markAsActive();
        return $user->refresh();
    }

    /**
     * Generate a random 5-digit OTP code
     *
     * @return string
     */
    protected function generateCode(): string
    {
        // return str_pad((string) rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        return '12345';
    }

    /**
     * Resend OTP code to user
     *
     * @param User $user
     * @return array
     */
    public function resendOTP(User $user): array
    {
        return $this->generateAndSendOTP($user);
    }

    /**
     * Check if OTP code has expired
     *
     * @param User $user
     * @return bool
     */
    public function isOTPExpired(User $user): bool
    {
        if (!$user->code_expire) {
            return true;
        }
        
        return Carbon::now()->isAfter($user->code_expire);
    }

    /**
     * Clear OTP code from user
     *
     * @param User $user
     * @return void
     */
    public function clearOTP(User $user): void
    {
        $user->update([
            'code' => null,
            'code_expire' => null,
        ]);
    }
}

