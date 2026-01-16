<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case PENDING_VERIFICATION = 'pending_verification';
    case REFUNDED = 'refunded';

    /**
     * Get all payment status values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get payment status label
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
            self::PENDING_VERIFICATION => 'Pending Verification',
            self::REFUNDED => 'Refunded',
        };
    }

    /**
     * Get payment status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::SUCCESS => 'success',
            self::FAILED => 'danger',
            self::PENDING_VERIFICATION => 'info',
            self::REFUNDED => 'secondary',
        };
    }

    /**
     * Get payment status icon
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::SUCCESS => 'check-circle',
            self::FAILED => 'x-circle',
            self::PENDING_VERIFICATION => 'help-circle',
            self::REFUNDED => 'rotate-ccw',
        };
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return $this === self::SUCCESS;
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return in_array($this, [self::PENDING, self::PENDING_VERIFICATION]);
    }

    /**
     * Check if payment failed
     */
    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }

    /**
     * Check if payment can be refunded
     */
    public function canBeRefunded(): bool
    {
        return $this === self::SUCCESS;
    }
}
