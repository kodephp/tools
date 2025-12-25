<?php

namespace Kode\Geo;

/**
 * Geographical location utility class
 * Provides distance calculation between coordinates and location-related operations
 */
class Geo
{
    /**
     * Earth radius in kilometers (WGS84 ellipsoid)
     */
    private const EARTH_RADIUS_KM = 6378.137;
    
    /**
     * Earth radius in miles
     */
    private const EARTH_RADIUS_MI = 3963.191;
    
    /**
     * Earth radius in meters
     */
    private const EARTH_RADIUS_M = 6378137;

    /**
     * Calculate distance between two coordinates using Haversine formula
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @param string $unit Unit of measurement (km, mi, m)
     * @return float Distance between points
     */
    public static function distance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit = 'km'): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return match($unit) {
            'mi' => $c * self::EARTH_RADIUS_MI,
            'm' => $c * self::EARTH_RADIUS_M,
            default => $c * self::EARTH_RADIUS_KM
        };
    }

    /**
     * Convert coordinates to radians
     * @param float $degrees Degrees value
     * @return float Radians value
     */
    public static function toRadians(float $degrees): float
    {
        return deg2rad($degrees);
    }

    /**
     * Convert radians to degrees
     * @param float $radians Radians value
     * @return float Degrees value
     */
    public static function toDegrees(float $radians): float
    {
        return rad2deg($radians);
    }

    /**
     * Validate coordinates
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return bool True if valid coordinates
     */
    public static function isValid(float $lat, float $lon): bool
    {
        return $lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180;
    }

    /**
     * Calculate midpoint between two coordinates
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @return array Midpoint coordinates [lat, lon]
     */
    public static function midpoint(float $lat1, float $lon1, float $lat2, float $lon2): array
    {
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        
        $bx = cos($lat2Rad) * cos($lon2Rad - $lon1Rad);
        $by = cos($lat2Rad) * sin($lon2Rad - $lon1Rad);
        
        $lat3Rad = atan2(sin($lat1Rad) + sin($lat2Rad), sqrt((cos($lat1Rad) + $bx) * (cos($lat1Rad) + $bx) + $by * $by));
        $lon3Rad = $lon1Rad + atan2($by, cos($lat1Rad) + $bx);
        
        return [rad2deg($lat3Rad), rad2deg($lon3Rad)];
    }

    /**
     * Calculate bearing between two coordinates
     * @param float $lat1 Latitude of first point
     * @param float $lon1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lon2 Longitude of second point
     * @return float Bearing in degrees (0-360)
     */
    public static function bearing(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);
        
        $dLon = $lon2Rad - $lon1Rad;
        
        $y = sin($dLon) * cos($lat2Rad);
        $x = cos($lat1Rad) * sin($lat2Rad) - sin($lat1Rad) * cos($lat2Rad) * cos($dLon);
        
        $bearing = atan2($y, $x);
        $bearing = rad2deg($bearing);
        $bearing = ($bearing + 360) % 360;
        
        return $bearing;
    }

    /**
     * Convert decimal degrees to degrees, minutes, seconds (DMS)
     * @param float $decimal Decimal degrees
     * @param bool $isLatitude True if latitude, false if longitude
     * @return array DMS components [degrees, minutes, seconds, direction]
     */
    public static function toDMS(float $decimal, bool $isLatitude): array
    {
        $direction = match(true) {
            $isLatitude && $decimal >= 0 => 'N',
            $isLatitude && $decimal < 0 => 'S',
            !$isLatitude && $decimal >= 0 => 'E',
            default => 'W'
        };
        
        $absDecimal = abs($decimal);
        $degrees = (int)$absDecimal;
        $minutes = ($absDecimal - $degrees) * 60;
        $seconds = ($minutes - (int)$minutes) * 60;
        
        return [$degrees, (int)$minutes, round($seconds, 4), $direction];
    }

    /**
     * Convert degrees, minutes, seconds (DMS) to decimal degrees
     * @param int $degrees Degrees
     * @param int $minutes Minutes
     * @param float $seconds Seconds
     * @param string $direction Direction (N, S, E, W)
     * @return float Decimal degrees
     */
    public static function toDecimal(int $degrees, int $minutes, float $seconds, string $direction): float
    {
        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
        
        return in_array(strtoupper($direction), ['S', 'W']) ? -$decimal : $decimal;
    }
}