<?php

namespace App\Services\Order;

use App\Models\Branch;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\BranchRepository;
use Illuminate\Support\Facades\Log;

/**
 * DeliveryCalculationService
 *
 * Handles delivery fee calculation, branch selection, and distance calculations
 * Uses repositories for all database operations
 */
class DeliveryCalculationService
{
    protected $addressRepository;
    protected $branchRepository;

    public function __construct(
        AddressRepository $addressRepository,
        BranchRepository $branchRepository
    ) {
        $this->addressRepository = $addressRepository;
        $this->branchRepository = $branchRepository;
    }

    /**
     * Calculate delivery details including branch, distance, and fee
     * Branch selection is ALWAYS based on user coordinates (polygon check)
     * regardless of order_type or delivery_type
     *
     * @param User $user
     * @param array $data
     * @return array ['branch_id', 'distance', 'delivery_fee']
     * @throws \Exception
     */
    public function calculateDeliveryDetails(User $user, array $data): array
    {
        $orderType = $data['order_type'] ?? null;
        $deliveryType = $data['delivery_type'] ?? 'immediate';

        // Get user address/coordinates
        $address = $this->getUserAddress($user, $data);


        $userLat = (float) $address->lat;
        $userLng = (float) $address->lng;

        // Find branch based on polygon containment (ALWAYS, regardless of order type)
        // If coordinates are provided, they MUST be within a branch polygon
        $branch = $this->findBranchByLocation($userLat, $userLng);
        if (!$branch) {
            // If coordinates are provided but no branch found, ALWAYS throw error
            // This applies to ALL order types including gift orders
            throw new \Exception(__('apis.address_not_in_branch_area'));
        }

        // Calculate distance
        $distance = $this->calculateDistance(
            $userLat,
            $userLng,
            (float) $branch->lat,
            (float) $branch->lng
        );

        // Calculate delivery fee based on delivery type and distance
        $deliveryFee = $this->calculateDeliveryFee($distance, $deliveryType);
        

        return [
            'branch_id' => $branch->id,
            'distance' => round($distance, 2),
            'delivery_fee' => round($deliveryFee, 2),
        ];
    }



    /**
     * Get user address from data or database
     * Supports multiple coordinate formats: lat/lng, latitude/longitude, gift_lat/gift_lng, gift_latitude/gift_longitude
     *
     * @param User $user
     * @param array $data
     * @return \App\Models\Address|object|null
     */
    private function getUserAddress(User $user, array $data)
    {
        // Priority 1: Use address_id if provided
        if (isset($data['address_id'])) {
            $address = $this->addressRepository->getUserAddress($user, $data['address_id']);
            if ($address) {
                // Normalize field names to lat/lng
                return (object) [
                    'lat' => $address->latitude ?? $address->lat,
                    'lng' => $address->longitude ?? $address->lng,
                ];
            }
        }

        // Priority 2: Gift order coordinates (gift_latitude/gift_longitude or gift_lat/gift_lng)
        $orderType = $data['order_type'] ?? null;
        if ($orderType === 'gift') {
            $lat = $data['gift_latitude'] ?? $data['gift_lat'] ?? null;
            $lng = $data['gift_longitude'] ?? $data['gift_lng'] ?? null;

            if ($lat !== null && $lng !== null) {
                return (object) [
                    'lat' => $lat,
                    'lng' => $lng,
                ];
            }
        }

        // Priority 3: Direct coordinates (latitude/longitude or lat/lng)
        $lat = $data['latitude'] ?? $data['lat'] ?? null;
        $lng = $data['longitude'] ?? $data['lng'] ?? null;

        if ($lat !== null && $lng !== null) {
            return (object) [
                'lat' => $lat,
                'lng' => $lng,
            ];
        }

        return null;
    }

    /**
     * Find branch by location using polygon containment
     * Checks if user coordinates are within any branch's delivery polygon
     *
     * @param float $userLat
     * @param float $userLng
     * @return Branch|null
     * @throws \Exception
     */
    private function findBranchByLocation(float $userLat, float $userLng): ?Branch
    {
        $branches = $this->branchRepository->getActive();

        if ($branches->isEmpty()) {
            throw new \Exception(__('apis.no_branches_available'));
        }

        // Find first branch that contains the user's location in its polygon
        foreach ($branches as $branch) {
            if ($this->isPointInPolygon($userLat, $userLng, $branch)) {
                return $branch;
            }
        }

        return null;
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Distance in kilometers
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if point is within branch delivery polygon
     *
     * @param float $lat
     * @param float $lng
     * @param Branch $branch
     * @return bool
     */
    private function isPointInPolygon(float $lat, float $lng, Branch $branch): bool
    {
        if (empty($branch->polygon)) {
            return false; // No polygon defined, reject
        }

        $polygon = json_decode($branch->polygon, true);

        if (!is_array($polygon) || empty($polygon)) {
            return false; // Invalid polygon, reject
        }

        // Handle Leaflet polygon format (array of arrays)
        $points = $polygon[0] ?? $polygon;

        if (!is_array($points) || count($points) < 3) {
            return false; // Not enough points for a polygon, reject
        }

        // Ray casting algorithm for point-in-polygon test
        $intersections = 0;
        $vertices = count($points);

        for ($i = 0; $i < $vertices; $i++) {
            $vertex1 = $points[$i];
            $vertex2 = $points[($i + 1) % $vertices];

            // Handle both array formats: ['lat' => x, 'lng' => y] and [lat, lng]
            $v1Lat = is_array($vertex1) ? ($vertex1['lat'] ?? $vertex1[0] ?? null) : null;
            $v1Lng = is_array($vertex1) ? ($vertex1['lng'] ?? $vertex1[1] ?? null) : null;
            $v2Lat = is_array($vertex2) ? ($vertex2['lat'] ?? $vertex2[0] ?? null) : null;
            $v2Lng = is_array($vertex2) ? ($vertex2['lng'] ?? $vertex2[1] ?? null) : null;

            if ($v1Lat === null || $v1Lng === null || $v2Lat === null || $v2Lng === null) {
                continue; // Skip invalid vertices
            }

            if ($v1Lng == $v2Lng) {
                continue; // Vertical line, skip
            }

            if ($lng < min($v1Lng, $v2Lng) || $lng > max($v1Lng, $v2Lng)) {
                continue; // Point is outside the longitude range
            }

            // Calculate intersection point
            $xIntersection = ($lng - $v1Lng) * ($v2Lat - $v1Lat) / ($v2Lng - $v1Lng) + $v1Lat;

            if ($lat < $xIntersection) {
                $intersections++;
            }
        }

        // Odd number of intersections means point is inside polygon
        return ($intersections % 2) != 0;
    }

    /**
     * Calculate delivery fee based on distance and site settings
     *
     * @param float $distance Distance in kilometers
     * @return float
     */
    private function calculateDeliveryFee(float $distance, string $deliveryType = 'immediate'): float
    {
        // Choose base fixed fee depending on delivery type
        $fixedDeliveryFee = (float) (
            $deliveryType === 'scheduled'
                ? getSiteSetting('scheduled_delivery_fee', 0)
                : getSiteSetting('ordinary_delivery_fee', getSiteSetting('delivery_fee', 0))
        );
        $perKmDeliveryFee = (float) getSiteSetting('delivery_per_km_fee', 0);
        $deliveryDistanceThreshold = (float) getSiteSetting('delivery_distance_threshold', 0);

        // Fixed fee within threshold, per-km fee beyond threshold
        if ($distance <= $deliveryDistanceThreshold) {
            return $fixedDeliveryFee;
        }

        return $fixedDeliveryFee + ($distance * $perKmDeliveryFee);
    }
}

