<?php

namespace Database\Factories;

use App\Models\Bet;
use App\Models\BetSettlement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BetSettlement>
 */
class BetSettlementFactory extends Factory
{
    protected $model = BetSettlement::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bet_id' => Bet::factory(),
            'outcome' => fake()->randomElement(['won', 'lost', 'void']),
            'payout' => fake()->numberBetween(0, 5000),
            'settled_at' => now(),
            'metadata' => null,
        ];
    }
}

