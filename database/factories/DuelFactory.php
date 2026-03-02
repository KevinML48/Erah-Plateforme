<?php

namespace Database\Factories;

use App\Models\Duel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Duel>
 */
class DuelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $requestedAt = now();

        return [
            'challenger_id' => User::factory(),
            'challenged_id' => User::factory(),
            'status' => Duel::STATUS_PENDING,
            'idempotency_key' => 'duel-'.Str::lower(Str::random(12)),
            'message' => fake()->optional()->sentence(),
            'requested_at' => $requestedAt,
            'expires_at' => $requestedAt->copy()->addHour(),
            'responded_at' => null,
            'accepted_at' => null,
            'refused_at' => null,
            'expired_at' => null,
        ];
    }
}
