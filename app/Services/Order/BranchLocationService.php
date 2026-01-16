<?php

namespace App\Services\Order;

use App\Models\Branch;
use App\Models\User;
use App\Repositories\BranchRepository;
use App\Repositories\AddressRepository;

/**
 * BranchLocationService
 * 
 * Handles branch selection based on location coordinates and polygon containment
 * Manages all location-based branch operations
 */
class BranchLocationService
{
    protected $branchRepository;
    protected $addressRepository;

    public function __construct(
        BranchRepository $branchRepository,
        AddressRepository $addressRepository
    ) {
        $this->branchRepository = $branchRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * Determine location coordinates and appropriate branch using dynamic selection
     *
     * @param User $user
     * @param array $data
     * @return array ['latitude' => float, 'longitude' => float, 'branch' => Branch|null, 'address_id' => int|null]
     * @throws \Exception
     */
    public function determineLocationAndBranch(User $user, array $data): array
    {
        $latitude = null;
        $longitude = null;
        $addressId = null;

        // If this is a gift order, prefer recipient coordinates or recipient address
        if (!empty($data['order_type']) && $data['order_type'] === 'gift') {
            [$latitude, $longitude, $addressId] = $this->extractGiftCoordinates($user, $data);

            // If still no coordinates, allow the order to proceed without a branch
            if ($latitude === null || $longitude === null) {
                return [
                    'latitude' => null,
                    'longitude' => null,
                    'branch' => null,
                    'address_id' => null,
                ];
            }
        } else {
            // Regular orders: extract coordinates from address or direct input
            [$latitude, $longitude, $addressId] = $this->extractRegularCoordinates($user, $data);
        }

        // Find appropriate branch using polygon containment
        $branch = null;
        if ($latitude !== null && $longitude !== null) {
            $branch = $this->findBranchByLocation($latitude, $longitude);

            // If no branch found for the provided coordinates, throw error
            if (!$branch) {
                throw new \Exception(__('apis.address_not_in_branch_area'));
            }
        }

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'branch' => $branch,
            'address_id' => $addressId,
        ];
    }

    /**
     * Extract coordinates for gift orders
     *
     * @param User $user
     * @param array $data
     * @return array [latitude, longitude, address_id]
     */
    private function extractGiftCoordinates(User $user, array $data): array
    {
        $latitude = null;
        $longitude = null;
        $addressId = null;

        // Try gift_latitude/gift_longitude first
        if (!empty($data['gift_latitude']) && !empty($data['gift_longitude'])) {
            $latitude = (float) $data['gift_latitude'];
            $longitude = (float) $data['gift_longitude'];
        }
        // Try gift_lat/gift_lng as alternative
        elseif (!empty($data['gift_lat']) && !empty($data['gift_lng'])) {
            $latitude = (float) $data['gift_lat'];
            $longitude = (float) $data['gift_lng'];
        }

        // If no gift coordinates, fallback to regular address or coordinates
        if ($latitude === null || $longitude === null) {
            [$latitude, $longitude, $addressId] = $this->extractRegularCoordinates($user, $data);
        }

        return [$latitude, $longitude, $addressId];
    }

    /**
     * Extract coordinates for regular orders
     *
     * @param User $user
     * @param array $data
     * @return array [latitude, longitude, address_id]
     * @throws \Exception
     */
    private function extractRegularCoordinates(User $user, array $data): array
    {
        $latitude = null;
        $longitude = null;
        $addressId = null;

        // Approach 1: Address-based selection
        if (!empty($data['address_id'])) {
            $address = $this->addressRepository->getUserAddress($user, $data['address_id']);
            if (!$address) {
                throw new \Exception(__('apis.invalid_address'));
            }

            $latitude = $address->latitude;
            $longitude = $address->longitude;
            $addressId = $address->id;
        }
        // Approach 2: Direct coordinates selection (latitude/longitude or lat/lng)
        elseif (!empty($data['latitude']) && !empty($data['longitude'])) {
            $latitude = (float) $data['latitude'];
            $longitude = (float) $data['longitude'];
        } elseif (!empty($data['lat']) && !empty($data['lng'])) {
            $latitude = (float) $data['lat'];
            $longitude = (float) $data['lng'];
        } else {
            throw new \Exception(__('apis.location_required'));
        }

        return [$latitude, $longitude, $addressId];
    }

    /**
     * Find the appropriate branch based on location coordinates
     *
     * @param float $latitude
     * @param float $longitude
     * @return Branch|null
     */
    public function findBranchByLocation(float $latitude, float $longitude): ?Branch
    {
        $branches = $this->branchRepository->getActive();

        foreach ($branches as $branch) {
            if ($this->isLocationInBranchPolygon($latitude, $longitude, $branch)) {
                return $branch;
            }
        }

        return null;
    }

    /**
     * Check if location coordinates are within branch delivery polygon
     *
     * @param float $latitude
     * @param float $longitude
     * @param Branch $branch
     * @return bool
     */
    public function isLocationInBranchPolygon(float $latitude, float $longitude, Branch $branch): bool
    {
        $polygon = json_decode($branch->polygon, true);

        if (!is_array($polygon) || empty($polygon)) {
            return false;
        }

        // Handle Leaflet polygon format (array of arrays)
        $points = $polygon[0] ?? $polygon;

        return $this->pointInPolygon([$latitude, $longitude], $points);
    }

    /**
     * Point in Polygon (Ray Casting algorithm)
     *
     * @param array $point [lat, lng]
     * @param array $polygon [[lat, lng], ...]
     * @return bool
     */
    private function pointInPolygon(array $point, array $polygon): bool
    {
        // Defensive: ensure point has both lat and lng
        if (!is_array($point) || !array_key_exists(0, $point) || !array_key_exists(1, $point)) {
            return false;
        }

        $x = (float) $point[1]; // lng
        $y = (float) $point[0]; // lat
        $inside = false;
        $n = count($polygon);

        // Remove duplicate last point if polygon is closed
        if ($n > 1 && isset($polygon[0][0], $polygon[0][1], $polygon[$n - 1][0], $polygon[$n - 1][1]) 
            && $polygon[0][0] === $polygon[$n - 1][0] && $polygon[0][1] === $polygon[$n - 1][1]) {
            $n = $n - 1;
        }

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            // Root-level validation: skip if either point is not a valid [lat, lng] array
            if (!is_array($polygon[$i]) || count($polygon[$i]) < 2 
                || !is_array($polygon[$j]) || count($polygon[$j]) < 2) {
                continue;
            }

            $xi = $polygon[$i]['lng'];
            $yi = $polygon[$i]['lat'];
            $xj = $polygon[$j]['lng'];
            $yj = $polygon[$j]['lat'];

            // Ray-casting: check if the edge crosses the horizontal ray to the right of the point
            $onEdge = (($y == $yi && $y == $yj) && ($x >= min($xi, $xj) && $x <= max($xi, $xj))) ||
                (($x == $xi && $x == $xj) && ($y >= min($yi, $yj) && $y <= max($yi, $yj)));

            if ($onEdge) {
                return true; // Point is exactly on a polygon edge
            }

            if ((($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-10) + $xi)) {
                $inside = !$inside;
            }
        }

        return $inside;
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
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}

