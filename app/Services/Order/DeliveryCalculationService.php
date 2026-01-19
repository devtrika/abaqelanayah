<?php

namespace App\Services\Order;

use App\Models\User;
use App\Repositories\AddressRepository;
use Illuminate\Support\Facades\Log;

/**
 * DeliveryCalculationService
 *
 * Handles delivery fee calculation
 */
class DeliveryCalculationService
{
    protected $addressRepository;

    public function __construct(
        AddressRepository $addressRepository
    ) {
        $this->addressRepository = $addressRepository;
    }

    /**
     * Calculate delivery details
     *
     * @param User $user
     * @param array $data
     * @return array ['delivery_fee']
     * @throws \Exception
     */
    public function calculateDeliveryDetails(User $user, array $data): array
    {
        $deliveryType = $data['delivery_type'] ?? 'immediate';

        // Calculate delivery fee based on delivery type
        $deliveryFee = $this->calculateDeliveryFee($deliveryType);
        
        return [
            'delivery_fee' => round($deliveryFee, 2),
        ];
    }



    /**
     * Calculate delivery fee based on distance and site settings
     *
     * @param float $distance Distance in kilometers
     * @return float
     */
    private function calculateDeliveryFee(string $deliveryType = 'immediate'): float
    {
       

        return 15 ;
    }
}

