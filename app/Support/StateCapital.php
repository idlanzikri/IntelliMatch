<?php

namespace App\Support;

class StateCapital
{
    public static function resolve(?string $location): ?array
    {
        if (! $location) {
            return null;
        }
        $location = mb_strtolower($location);
        // Prefer a state's explicit name over city aliases in an address.
        foreach (config('state_capitals') as $state => $capital) {
            if (preg_match('/(?<![\pL])'.preg_quote($state, '/').'(?![\pL])/u', $location)) {
                return ['state' => $state, ...$capital];
            }
        }
        foreach (config('state_capitals') as $state => $capital) {
            foreach ($capital['aliases'] as $alias) {
                if (preg_match('/(?<![\pL])'.preg_quote($alias, '/').'(?![\pL])/u', $location)) {
                    return ['state' => $state, ...$capital];
                }
            }
        }

        return null;
    }
}
