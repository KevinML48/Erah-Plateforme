<?php

namespace Database\Factories;

use App\Models\EsportMatch;
use App\Models\MatchMarket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MatchMarket>
 */
class MatchMarketFactory extends Factory
{
    protected $model = MatchMarket::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'match_id' => EsportMatch::factory(),
            'key' => MatchMarket::KEY_WINNER,
            'title' => 'Match Winner',
            'is_active' => true,
        ];
    }
}

