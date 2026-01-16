<?php

namespace App\Services;

class PolygonService
{
    /**
     * Check if a point is inside a polygon (Ray Casting algorithm)
     * @param array $point [lat, lng]
     * @param array $polygon [[lat, lng] or ['lat'=>, 'lng'=>], ...]
     * @return bool
     */
    public function pointInPolygon($point, $polygon)
    {
        if (!is_array($point) || !array_key_exists(0, $point) || !array_key_exists(1, $point)) {
            return false;
        }
        $x = (float)$point[1]; // lng
        $y = (float)$point[0]; // lat
        $inside = false;
        $n = count($polygon);
        // Remove duplicate last point if polygon is closed
        if ($n > 1 && isset($polygon[0][0], $polygon[0][1], $polygon[$n-1][0], $polygon[$n-1][1]) && $polygon[0][0] === $polygon[$n-1][0] && $polygon[0][1] === $polygon[$n-1][1]) {
            $n = $n - 1;
        }
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            // Root-level validation: skip if either point is not a valid [lat, lng] array
            if (!is_array($polygon[$i]) || count($polygon[$i]) < 2 || !is_array($polygon[$j]) || count($polygon[$j]) < 2) {
                continue;
            }
            // Support both [lat, lng] and ['lat'=>, 'lng'=>] formats
            $yi = isset($polygon[$i]['lat']) ? (float)$polygon[$i]['lat'] : (float)$polygon[$i][0];
            $xi = isset($polygon[$i]['lng']) ? (float)$polygon[$i]['lng'] : (float)$polygon[$i][1];
            $yj = isset($polygon[$j]['lat']) ? (float)$polygon[$j]['lat'] : (float)$polygon[$j][0];
            $xj = isset($polygon[$j]['lng']) ? (float)$polygon[$j]['lng'] : (float)$polygon[$j][1];
            $onEdge = (($y == $yi && $y == $yj) && ($x >= min($xi, $xj) && $x <= max($xi, $xj))) ||
                     (($x == $xi && $x == $xj) && ($y >= min($yi, $yj) && $y <= max($yi, $yj)));
            if ($onEdge) {
                return true;
            }
            if ((($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-10) + $xi)) {
                $inside = !$inside;
            }
        }
        return $inside;
    }
    /**
     * Calculate delivery fee based on distance and settings
     * @param float $distance
     * @param array $settings ['delivery_fee', 'delivery_per_km_fee', 'delivery_distance_threshold']
     * @return float
     */
    public function calculateDeliveryFee($distance, $settings)
    {
        $fixedFee = isset($settings['delivery_fee']) ? (float)$settings['delivery_fee'] : 0;
        $perKmFee = isset($settings['delivery_per_km_fee']) ? (float)$settings['delivery_per_km_fee'] : 0;
        $threshold = isset($settings['delivery_distance_threshold']) ? (float)$settings['delivery_distance_threshold'] : 0;
        $extraDistance = max(0, $distance - $threshold);
        return $fixedFee + ($extraDistance > 0 ? $extraDistance * $perKmFee : 0);
    }
}
