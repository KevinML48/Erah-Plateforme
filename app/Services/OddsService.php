<?php
declare(strict_types=1);

namespace App\Services;

class OddsService
{
    /**
     * @param  array<int, array{odds_decimal: float|int|string, popularity_weight: float|int|string|null}>  $optionSnapshots
     */
    public function computeTotalOdds(array $optionSnapshots): float
    {
        $total = 1.0;

        foreach ($optionSnapshots as $snapshot) {
            $odds = max(1.0, (float) $snapshot['odds_decimal']);
            $total *= $odds;
        }

        return round($total, 3);
    }

    public function computePotentialPayout(int $stake, float $totalOdds, ?float $popFactor): int
    {
        $factor = max(1.0, $popFactor ?? 1.0);

        return (int) floor($stake * $totalOdds * $factor);
    }

    /**
     * @param  array<int, array{popularity_weight: float|int|string|null}>  $optionSnapshots
     */
    public function computePopularityFactor(array $optionSnapshots): float
    {
        if (!config('betting.use_popularity_factor', true)) {
            return 1.0;
        }

        $weights = [];
        foreach ($optionSnapshots as $snapshot) {
            $weight = $snapshot['popularity_weight'] ?? null;
            if ($weight === null) {
                continue;
            }

            $weights[] = max(0.5, min(2.0, (float) $weight));
        }

        if ($weights === []) {
            return 1.0;
        }

        $avg = array_sum($weights) / count($weights);

        return round(max(0.75, min(1.25, $avg)), 4);
    }
}

