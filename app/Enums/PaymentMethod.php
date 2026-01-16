<?php

namespace App\Enums;

enum PaymentMethod: int
{
    case VISA = 1;
    case MADA = 2;
    case APPLE_PAY = 3;
    case GOOGLE_PAY = 4;
    case BANK_TRANSFER = 5;
    case WALLET = 6;

    /**
     * Get all payment method values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Add this to PaymentMethod model:
public static function electronicIds(): array
{
    return [
        self::VISA,
        self::MADA,
        self::APPLE_PAY,
        self::GOOGLE_PAY,
    ];
}


    /**
     * Get payment method label
     */
    public function label(): string
    {
        return match($this) {
            self::VISA => 'Visa',
            self::MADA => 'Mada',
            self::APPLE_PAY => 'Apple Pay',
            self::GOOGLE_PAY => 'Google Pay',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::WALLET => 'Wallet',
        };
    }

    /**
     * Check if payment method is electronic
     */
    public function isElectronic(): bool
    {
        return in_array($this, [
            self::VISA,
            self::MADA,
            self::APPLE_PAY,
            self::GOOGLE_PAY,
        ]);
    }

    /**
     * Check if payment method requires immediate processing
     */
    public function requiresImmediateProcessing(): bool
    {
        return $this === self::WALLET;
    }

    /**
     * Get payment method icon
     */
    public function icon(): string
    {
        return match($this) {
            self::VISA => 'credit-card',
            self::MADA => 'credit-card',
            self::APPLE_PAY => 'smartphone',
            self::GOOGLE_PAY => 'smartphone',
            self::BANK_TRANSFER => 'send',
            self::WALLET => 'credit-card',
        };
    }

    /**
     * Check if payment method requires verification
     */
    public function requiresVerification(): bool
    {
        return in_array($this, [self::BANK_TRANSFER]);
    }

    /**
     * Get payment gateway for electronic payments
     */
    public function getGateway(): ?string
    {
        return match($this) {
            self::VISA, self::MADA => 'myfatoorah',
            self::APPLE_PAY => 'apple_pay',
            self::GOOGLE_PAY => 'google_pay',
            default => null,
        };
    }
}
