<?php

namespace Tests\Feature\Bets;

use App\Models\Bet;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BettingBaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_betting_v1_schema_tables_exist_and_seeders_populate_base_data(): void
    {
        Artisan::call('db:seed');

        $this->assertTrue(Schema::hasTable('match_markets'));
        $this->assertTrue(Schema::hasTable('match_selections'));
        $this->assertTrue(Schema::hasTable('user_wallets'));
        $this->assertTrue(Schema::hasTable('wallet_transactions'));
        $this->assertTrue(Schema::hasTable('bet_settlements'));

        $this->assertDatabaseCount('matches', 8);
        $this->assertDatabaseHas('matches', ['match_key' => 'bet-v1-scheduled-1', 'status' => 'scheduled']);
        $this->assertDatabaseHas('matches', ['match_key' => 'bet-v1-live-1', 'status' => 'live']);
        $this->assertDatabaseHas('matches', ['match_key' => 'bet-v1-finished-1', 'status' => 'finished']);

        $scheduled = \App\Models\EsportMatch::query()->where('match_key', 'bet-v1-scheduled-1')->firstOrFail();
        $market = MatchMarket::query()->where('match_id', $scheduled->id)->where('key', MatchMarket::KEY_WINNER)->firstOrFail();

        $this->assertDatabaseHas('match_selections', ['market_id' => $market->id, 'key' => MatchSelection::KEY_TEAM_A]);
        $this->assertDatabaseHas('match_selections', ['market_id' => $market->id, 'key' => MatchSelection::KEY_TEAM_B]);
        $this->assertDatabaseHas('match_selections', ['market_id' => $market->id, 'key' => MatchSelection::KEY_DRAW]);

        $usersCount = User::query()->count();
        $this->assertDatabaseCount('user_wallets', $usersCount);
        $this->assertDatabaseHas('wallet_transactions', ['type' => 'grant']);
        $this->assertDatabaseHas('wallet_transactions', ['type' => 'stake']);

        $this->assertDatabaseHas('bets', ['status' => Bet::STATUS_PLACED]);
        $this->assertDatabaseHas('bets', ['status' => Bet::STATUS_WON]);
        $this->assertDatabaseHas('bets', ['status' => Bet::STATUS_LOST]);
        $this->assertDatabaseHas('bet_settlements', ['outcome' => Bet::STATUS_WON]);
    }
}

