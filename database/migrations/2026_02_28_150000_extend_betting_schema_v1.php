<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('game_key', 40)->nullable()->after('match_key');
            $table->string('team_a_name', 120)->nullable()->after('game_key');
            $table->string('team_b_name', 120)->nullable()->after('team_a_name');
            $table->timestamp('locked_at')->nullable()->after('starts_at');
            $table->timestamp('finished_at')->nullable()->after('result');

            $table->index('game_key');
            $table->index('locked_at');
            $table->index('finished_at');
        });

        Schema::table('bets', function (Blueprint $table) {
            $table->string('market_key', 40)->default('WINNER')->after('match_id');
            $table->string('selection_key', 20)->nullable()->after('prediction');
            $table->unsignedInteger('stake')->default(0)->after('selection_key');
            $table->decimal('odds_snapshot', 6, 3)->default(2.000)->after('stake');
            $table->timestamp('cancelled_at')->nullable()->after('placed_at');
            $table->unsignedInteger('payout')->nullable()->after('settled_at');

            $table->index(['market_key', 'status'], 'bets_market_status_idx');

            $table->dropUnique('bets_user_match_unique');
            $table->unique(['user_id', 'match_id', 'market_key'], 'bets_user_match_market_unique');
        });

        Schema::create('match_markets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->string('key', 40)->default('WINNER');
            $table->string('title', 120);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['match_id', 'key'], 'match_markets_match_key_unique');
            $table->index(['match_id', 'is_active'], 'match_markets_match_active_idx');
        });

        Schema::create('match_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained('match_markets')->cascadeOnDelete();
            $table->string('key', 20);
            $table->string('label', 120);
            $table->decimal('odds', 6, 3)->default(2.000);
            $table->timestamps();

            $table->unique(['market_id', 'key'], 'match_selections_market_key_unique');
            $table->index(['market_id', 'odds'], 'match_selections_market_odds_idx');
        });

        Schema::create('user_wallets', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->integer('balance')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 30)->index();
            $table->integer('amount');
            $table->integer('balance_after');
            $table->string('ref_type', 40)->nullable()->index();
            $table->string('ref_id', 191)->nullable()->index();
            $table->string('unique_key', 191);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['user_id', 'created_at'], 'wallet_transactions_user_created_idx');
            $table->unique(['user_id', 'unique_key'], 'wallet_transactions_user_unique_key_unique');
        });

        Schema::create('bet_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bet_id')->constrained('bets')->cascadeOnDelete();
            $table->string('outcome', 20)->index();
            $table->unsignedInteger('payout')->default(0);
            $table->timestamp('settled_at')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('bet_id', 'bet_settlements_bet_unique');
        });

        $this->backfillMatches();
        $this->backfillBets();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bet_settlements');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('user_wallets');
        Schema::dropIfExists('match_selections');
        Schema::dropIfExists('match_markets');

        Schema::table('bets', function (Blueprint $table) {
            $table->dropUnique('bets_user_match_market_unique');
            $table->unique(['user_id', 'match_id'], 'bets_user_match_unique');

            $table->dropIndex('bets_market_status_idx');
            $table->dropColumn([
                'market_key',
                'selection_key',
                'stake',
                'odds_snapshot',
                'cancelled_at',
                'payout',
            ]);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex(['game_key']);
            $table->dropIndex(['locked_at']);
            $table->dropIndex(['finished_at']);
            $table->dropColumn([
                'game_key',
                'team_a_name',
                'team_b_name',
                'locked_at',
                'finished_at',
            ]);
        });
    }

    private function backfillMatches(): void
    {
        $rows = DB::table('matches')
            ->select(['id', 'match_key', 'home_team', 'away_team', 'starts_at', 'status', 'result', 'settled_at'])
            ->get();

        foreach ($rows as $row) {
            $startsAt = $row->starts_at ? \Illuminate\Support\Carbon::parse($row->starts_at) : null;
            $lockedAt = $startsAt?->copy()->subMinutes(5);
            $finishedAt = in_array((string) $row->status, ['finished', 'settled'], true)
                ? ($row->settled_at ? \Illuminate\Support\Carbon::parse($row->settled_at) : now())
                : null;

            DB::table('matches')
                ->where('id', $row->id)
                ->update([
                    'game_key' => $row->match_key ?: null,
                    'team_a_name' => $row->home_team,
                    'team_b_name' => $row->away_team,
                    'locked_at' => $lockedAt,
                    'finished_at' => $finishedAt,
                ]);
        }
    }

    private function backfillBets(): void
    {
        $rows = DB::table('bets')
            ->select([
                'id',
                'prediction',
                'stake_points',
                'potential_payout',
                'settlement_points',
                'status',
            ])
            ->get();

        foreach ($rows as $row) {
            $stake = (int) $row->stake_points;
            $potentialPayout = (int) $row->potential_payout;
            $status = (string) $row->status;

            $selectionKey = match ((string) $row->prediction) {
                'home' => 'team_a',
                'away' => 'team_b',
                'draw' => 'draw',
                default => null,
            };

            $oddsSnapshot = $stake > 0
                ? round(max(1, $potentialPayout) / $stake, 3)
                : 2.000;

            $payout = in_array($status, ['won', 'void'], true)
                ? (int) $row->settlement_points
                : null;

            DB::table('bets')
                ->where('id', $row->id)
                ->update([
                    'market_key' => 'WINNER',
                    'selection_key' => $selectionKey,
                    'stake' => $stake,
                    'odds_snapshot' => $oddsSnapshot,
                    'payout' => $payout,
                ]);
        }
    }
};

