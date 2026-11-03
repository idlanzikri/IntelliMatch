<?php

namespace App\Support;

class DemoLocation
{
    public static function for(?string $location, string $seed): ?array
    {
        $capital = StateCapital::resolve($location);
        if (! $capital) {
            return null;
        }
        $cities = config('demo_cities.'.$capital['state'], []);
        if (! $cities) {
            return null;
        }
        // Stable random selection: repeated seed runs do not move existing demo pins.
        $index = hexdec(substr(hash('sha256', $seed), 0, 8)) % count($cities);
        [$city, $latitude, $longitude] = $cities[$index];

        return ['city' => $city, 'latitude' => $latitude, 'longitude' => $longitude];
    }
}
