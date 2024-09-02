<?php

declare(strict_types=1);

namespace App\Services\Api\V1;

class LocationPointsService
{
    // Radius in meters
    const EARTH_RADIUS = 6371000;

    /**
     * Used Haversine formula to calculates the distance between
     * two points on the surface of a sphere, given their latitudes
     * and longitudes
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    public function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $this::EARTH_RADIUS * $c; // Distance in meters

        return $distance;
    }
}
