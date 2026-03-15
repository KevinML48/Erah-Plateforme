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
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'event_name' => null,
            'compétition_name' => null,
            'compétition_stage' => null,
            'compétition_split' => null,
            'best_of' => null,
            'parent_match_id' => null,
            'team_a_name' => $homeTeam,
            'team_b_name' => $awayTeam,
            'home_team' => $homeTeam,
            'away_team' => $awayTeam,
            'starts_at' => $startsAt,
            'locked_at' => $startsAt->copy()->subMinutes(5),
            'ends_at' => null,
            'status' => EsportMatch::STATUS_SCHEDULED,
            'result' => null,
            'finished_at' => null,
            'team_a_score' => null,
            'team_b_score' => null,
            'child_matches_unlocked_at' => null,
            'settled_at' => null,
            'meta' => null,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    public function rocketLeagueTournament(): static
    {
        return $this->state(fn () => [
            'game_key' => EsportMatch::GAME_ROCKET_LEAGUE,
            'event_type' => EsportMatch::EVENT_TYPE_TOURNAMENT_RUN,
            'event_name' => 'RLCS Open',
            'compétition_name' => 'RLCS Open',
            'compétition_stage' => 'Open Qualifier',
            'compétition_split' => 'Spring',
            'best_of' => null,
            'team_a_name' => null,
            'team_b_name' => null,
            'home_team' => 'ERAH Rocket League',
            'away_team' => 'Tournament Run',
            'ends_at' => now()->addDays(2),
        ]);
    }

    public function rocketLeagueChildMatch(?EsportMatch $parent = null): static
    {
        return $this->state(fn () => [
            'game_key' => EsportMatch::GAME_ROCKET_LEAGUE,
            'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
            'best_of' => 5,
            'parent_match_id' => $parent?->id,
            'compétition_name' => $parent?->compétition_name,
            'compétition_stage' => $parent?->compétition_stage,
            'compétition_split' => $parent?->compétition_split,
        ]);
    }
}
