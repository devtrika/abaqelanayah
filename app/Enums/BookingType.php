<?php

namespace App\Enums;

enum BookingType: string
{
    case HOME = 'home';
    case SALON = 'salon';

    /**
     * Get all booking type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get booking type label
     */
    public function label(): string
    {
        return match($this) {
            self::HOME => 'Home Service',
            self::SALON => 'Salon Visit',
        };
    }

    /**
     * Get booking type icon
     */
    public function icon(): string
    {
        return match($this) {
            self::HOME => 'home',
            self::SALON => 'map-pin',
        };
    }

    /**
     * Check if booking type requires home service fee
     */
    public function requiresHomeServiceFee(): bool
    {
        return $this === self::HOME;
    }
}
