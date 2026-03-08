<?php

namespace Database\Factories;

use App\Models\MatchMarket;
use App\Models\MatchSelection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MatchSelection>
 */
class MatchSelectionFactory extends Factory
{
    protected $model = MatchSelection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = fake()->randomElement([
            MatchSelection::KEY_TEAM_A,
            MatchSelection::KEY_TEAM_B,
            MatchSelection::KEY_DRAW,
        ]);

        return [
            'market_id' => MatchMarket::factory(),
            'key' => $key,
            'label' => match ($key) {
                MatchSelection::KEY_TEAM_A => 'Team A',
                MatchSelection::KEY_TEAM_B => 'Team B',
                default => 'Draw',
            },
            'odds' => $key === MatchSelection::KEY_DRAW ? 3.000 : 2.000,
            'sort_order' => 0,
        ];
    }
}
