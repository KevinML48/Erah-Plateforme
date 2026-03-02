<?php

namespace Database\Factories;

use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bet>
 */
class BetFactory extends Factory
{
    protected $model = Bet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stake = fake()->numberBetween(10, 1000);
        $prediction = fake()->randomElement(Bet::predictions());
        $selection = match ($prediction) {
            Bet::PREDICTION_HOME => Bet::SELECTION_TEAM_A,
            Bet::PREDICTION_AWAY => Bet::SELECTION_TEAM_B,
            default => Bet::SELECTION_DRAW,
        };

        return [
            'user_id' => User::factory(),
            'match_id' => EsportMatch::factory(),
            'market_key' => 'WINNER',
            'selection_key' => $selection,
            'stake' => $stake,
            'odds_snapshot' => $prediction === Bet::PREDICTION_DRAW ? 3.000 : 2.000,
            'prediction' => $prediction,
            'stake_points' => $stake,
            'potential_payout' => $prediction === Bet::PREDICTION_DRAW ? $stake * 3 : $stake * 2,
            'settlement_points' => 0,
            'status' => Bet::STATUS_PLACED,
            'idempotency_key' => 'bet-'.Str::lower(Str::random(12)),
            'placed_at' => now(),
            'cancelled_at' => null,
            'settled_at' => null,
            'payout' => null,
            'meta' => null,
        ];
    }
}
