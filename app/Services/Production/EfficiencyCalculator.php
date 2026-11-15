<?php

namespace App\Services\Production;

/**
 * Standard garment line efficiency:
 * (pieces × SMV) / (operators × available minutes) × 100.
 */
class EfficiencyCalculator
{
    public function percent(int $pieces, float $smv, int $operators, int $availableMinutes): float
    {
        $available = $operators * $availableMinutes;

        if ($pieces <= 0 || $smv <= 0 || $available <= 0) {
            return 0;
        }

        return round(($pieces * $smv) / $available * 100, 2);
    }
}
