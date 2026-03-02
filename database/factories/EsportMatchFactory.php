<?php

namespace Database\Factories;

use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EsportMatch>
 */
class EsportMatchFactory extends Factory
{
    protected $model = EsportMatch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $homeTeam = fake()->city();
        $awayTeam = fake()->city();
        $startsAt = now()->addHours(2);

        return [
            'match_key' => 'match-'.Str::lower(Str::random(10)),
            'game_key' => fake()->randomElement(['valorant', 'lol', 'cs2']),
            'team_a_name' => $homeTeam,
            'team_b_name' => $awayTeam,
            'home_team' => $homeTeam,
            'away_team' => $awayTeam,
            'starts_at' => $startsAt,
            'locked_at' => $startsAt->copy()->subMinutes(5),
            'status' => EsportMatch::STATUS_SCHEDULED,
            'result' => null,
            'finished_at' => null,
            'settled_at' => null,
            'meta' => null,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
