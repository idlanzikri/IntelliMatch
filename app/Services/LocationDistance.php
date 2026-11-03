<?php

namespace App\Services;

class LocationDistance
{
    public static function kilometres(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return round(6371.0088 * 2 * asin(sqrt(max(0, min(1, $a)))), 1);
    }
}
