<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Setting;

class GeoService
{
    /**
     * Earth's radius in meters.
     */
    private const EARTH_RADIUS_METERS = 6371000;

    /**
     * Calculate distance between two points using the Haversine formula.
     *
     * @return float Distance in meters
     */
    public function distanceInMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }

    /**
     * Check if user coordinates are within the configured radius of a facility.
     */
    public function isWithinRadius(
        float $userLat,
        float $userLng,
        float $facilityLat,
        float $facilityLng,
        ?int $radiusMeters = null
    ): bool {
        $radius = $radiusMeters ?? (int) Setting::get('geolocation_proximity_threshold_meters', 100);
        $distance = $this->distanceInMeters($userLat, $userLng, $facilityLat, $facilityLng);

        return $distance <= $radius;
    }

    /**
     * Validate that the user is at the given location.
     *
     * @return array{valid: bool, distance: ?float, message: string}
     */
    public function validateProximity(?float $userLat, ?float $userLng, Location $location): array
    {
        $enabled = (bool) Setting::get('geolocation_proximity_enabled', true);
        $threshold = (int) Setting::get('geolocation_proximity_threshold_meters', 100);

        if (!$enabled) {
            return ['valid' => true, 'distance' => null, 'message' => ''];
        }

        if ($userLat === null || $userLng === null) {
            return [
                'valid' => false,
                'distance' => null,
                'message' => 'Location access is required to check in. Please enable location services and try again.',
            ];
        }

        if ($location->latitude === null || $location->longitude === null) {
            return ['valid' => true, 'distance' => null, 'message' => ''];
        }

        $distance = $this->distanceInMeters(
            $userLat,
            $userLng,
            (float) $location->latitude,
            (float) $location->longitude
        );

        if ($distance <= $threshold) {
            return [
                'valid' => true,
                'distance' => round($distance, 2),
                'message' => '',
            ];
        }

        return [
            'valid' => false,
            'distance' => round($distance, 2),
            'message' => 'You must be physically at this facility to check in. You appear to be ' . round($distance) . ' meters away.',
        ];
    }
}
