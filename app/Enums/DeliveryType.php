<?php

namespace App\Enums;

enum DeliveryType: string
{
    case NORMAL = 'normal';
    case EXPRESS = 'express';

    /**
     * Get all delivery type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get delivery type label
     */
    public function label(): string
    {
        return match($this) {
            self::NORMAL => 'Normal Delivery',
            self::EXPRESS => 'Express Delivery',
        };
    }

    /**
     * Get delivery type icon
     */
    public function icon(): string
    {
        return match($this) {
            self::NORMAL => 'truck',
            self::EXPRESS => 'zap',
        };
    }

    /**
     * Get delivery fee multiplier
     */
    public function getFeeMultiplier(): float
    {
        return match($this) {
            self::NORMAL => 1.0,
            self::EXPRESS => 1.5, // 50% more for express
        };
    }
}
