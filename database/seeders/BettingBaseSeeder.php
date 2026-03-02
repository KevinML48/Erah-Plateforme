<?php

namespace Database\Seeders;

use App\Models\Bet;
use App\Models\BetSettlement;
use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use RuntimeException;

class BettingBaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->first();

        if (! $admin) {
            throw new RuntimeException('Admin user is required before BettingBaseSeeder.');
        }

        $this->seedWallets();
        $matches = $this->seedMatches($admin);
        $this->seedWinnerMarkets($matches);
        $this->seedBetsAndSettlements($matches);
    }

    private function seedWallets(): void
    {
        $initialBalance = (int) config('betting.wallet.initial_balance', 1000);

        $users = User::query()->orderBy('id')->get();

        foreach ($users as $user) {
            $wallet = UserWallet::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );

            $this->applyWalletTransaction(
                wallet: $wallet,
                userId: $user->id,
                type: WalletTransaction::TYPE_GRANT,
                amount: $initialBalance,
                uniqueKey: 'seed.wallet.initial.v1',
                refType: WalletTransaction::REF_TYPE_SYSTEM,
                refId: 'initial_grant',
                metadata: ['source' => self::class]
            );
        }
    }

    /**
     * @return array<string, EsportMatch>
     */
    private function seedMatches(User $admin): array
    {
        $definitions = [
            'bet-v1-scheduled-1' => [
                'game_key' => 'valorant',
                'team_a_name' => 'ERAH Falcons',
                'team_b_name' => 'Midnight Owls',
                'starts_at' => now()->addHours(2),
                'locked_at' => now()->addHours(2)->subMinutes(5),
                'status' => EsportMatch::STATUS_SCHEDULED,
                'result' => null,
                'finished_at' => null,
                'settled_at' => null,
            ],
            'bet-v1-scheduled-2' => [
                'game_key' => 'lol',
                'team_a_name' => 'Nova Breakers',
                'team_b_name' => 'Atlas Wolves',
                'starts_at' => now()->addHours(6),
                'locked_at' => now()->addHours(6)->subMinutes(5),
                'status' => EsportMatch::STATUS_SCHEDULED,
                'result' => null,
                'finished_at' => null,
                'settled_at' => null,
            ],
            'bet-v1-scheduled-3' => [
                'game_key' => 'cs2',
                'team_a_name' => 'Vector Prime',
                'team_b_name' => 'Crimson Shift',
                'starts_at' => now()->addHours(10),
                'locked_at' => now()->addHours(10)->subMinutes(5),
                'status' => EsportMatch::STATUS_SCHEDULED,
                'result' => null,
                'finished_at' => null,
                'settled_at' => null,
            ],
            'bet-v1-live-1' => [
                'game_key' => 'valorant',
                'team_a_name' => 'Titan Squad',
                'team_b_name' => 'Storm Unit',
                'starts_at' => now()->subMinutes(15),
                'locked_at' => now()->subMinutes(20),
                'status' => EsportMatch::STATUS_LIVE,
                'result' => null,
                'finished_at' => null,
                'settled_at' => null,
            ],
            'bet-v1-finished-1' => [
                'game_key' => 'lol',
                'team_a_name' => 'Quantum Five',
                'team_b_name' => 'Solar Rush',
                'starts_at' => now()->subHours(3),
                'locked_at' => now()->subHours(3)->subMinutes(5),
                'status' => EsportMatch::STATUS_FINISHED,
                'result' => MatchSelection::KEY_TEAM_A,
                'finished_at' => now()->subHours(2),
                'settled_at' => null,
            ],
        ];

        $rows = [];

        foreach ($definitions as $matchKey => $definition) {
            $match = EsportMatch::query()->updateOrCreate(
                ['match_key' => $matchKey],
                [
                    'game_key' => $definition['game_key'],
                    'team_a_name' => $definition['team_a_name'],
                    'team_b_name' => $definition['team_b_name'],
                    'home_team' => $definition['team_a_name'],
                    'away_team' => $definition['team_b_name'],
                    'starts_at' => $definition['starts_at'],
                    'locked_at' => $definition['locked_at'],
                    'status' => $definition['status'],
                    'result' => $definition['result'],
                    'finished_at' => $definition['finished_at'],
                    'settled_at' => $definition['settled_at'],
                    'meta' => ['seed' => self::class],
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ]
            );

            $rows[$matchKey] = $match->fresh();
        }

        return $rows;
    }

    /**
     * @param array<string, EsportMatch> $matches
     */
    private function seedWinnerMarkets(array $matches): void
    {
        foreach ($matches as $match) {
            $market = MatchMarket::query()->updateOrCreate(
                [
                    'match_id' => $match->id,
                    'key' => MatchMarket::KEY_WINNER,
                ],
                [
                    'title' => 'Winner',
                    'is_active' => true,
                ]
            );

            MatchSelection::query()->updateOrCreate(
                ['market_id' => $market->id, 'key' => MatchSelection::KEY_TEAM_A],
                ['label' => (string) ($match->team_a_name ?: $match->home_team), 'odds' => 2.000]
            );

            MatchSelection::query()->updateOrCreate(
                ['market_id' => $market->id, 'key' => MatchSelection::KEY_TEAM_B],
                ['label' => (string) ($match->team_b_name ?: $match->away_team), 'odds' => 2.000]
            );

            MatchSelection::query()->updateOrCreate(
                ['market_id' => $market->id, 'key' => MatchSelection::KEY_DRAW],
                ['label' => 'Draw', 'odds' => 3.000]
            );
        }
    }

    /**
     * @param array<string, EsportMatch> $matches
     */
    private function seedBetsAndSettlements(array $matches): void
    {
        $players = User::query()
            ->where('role', User::ROLE_USER)
            ->orderBy('id')
            ->limit(4)
            ->get();

        if ($players->count() < 4) {
            throw new RuntimeException('BettingBaseSeeder requires at least 4 non-admin users.');
        }

        $betDefinitions = [
            [
                'user_id' => $players[0]->id,
                'match_id' => $matches['bet-v1-scheduled-1']->id,
                'prediction' => Bet::PREDICTION_HOME,
                'selection_key' => MatchSelection::KEY_TEAM_A,
                'stake' => 120,
                'odds' => 2.000,
                'status' => Bet::STATUS_PLACED,
                'settled_at' => null,
                'cancelled_at' => null,
                'payout' => null,
            ],
            [
                'user_id' => $players[1]->id,
                'match_id' => $matches['bet-v1-scheduled-2']->id,
                'prediction' => Bet::PREDICTION_AWAY,
                'selection_key' => MatchSelection::KEY_TEAM_B,
                'stake' => 90,
                'odds' => 2.000,
                'status' => Bet::STATUS_PLACED,
                'settled_at' => null,
                'cancelled_at' => null,
                'payout' => null,
            ],
            [
                'user_id' => $players[2]->id,
                'match_id' => $matches['bet-v1-finished-1']->id,
                'prediction' => Bet::PREDICTION_HOME,
                'selection_key' => MatchSelection::KEY_TEAM_A,
                'stake' => 150,
                'odds' => 2.000,
                'status' => Bet::STATUS_WON,
                'settled_at' => $matches['bet-v1-finished-1']->finished_at,
                'cancelled_at' => null,
                'payout' => 300,
            ],
            [
                'user_id' => $players[3]->id,
                'match_id' => $matches['bet-v1-finished-1']->id,
                'prediction' => Bet::PREDICTION_AWAY,
                'selection_key' => MatchSelection::KEY_TEAM_B,
                'stake' => 80,
                'odds' => 2.000,
                'status' => Bet::STATUS_LOST,
                'settled_at' => $matches['bet-v1-finished-1']->finished_at,
                'cancelled_at' => null,
                'payout' => 0,
            ],
        ];

        foreach ($betDefinitions as $index => $definition) {
            $stake = (int) $definition['stake'];
            $potentialPayout = (int) round($stake * (float) $definition['odds']);
            $status = (string) $definition['status'];
            $payout = $definition['payout'];
            $settlementPoints = $status === Bet::STATUS_WON ? (int) $payout : 0;

            $bet = Bet::query()->updateOrCreate(
                [
                    'user_id' => $definition['user_id'],
                    'match_id' => $definition['match_id'],
                    'market_key' => 'WINNER',
                ],
                [
                    'selection_key' => $definition['selection_key'],
                    'prediction' => $definition['prediction'],
                    'stake' => $stake,
                    'odds_snapshot' => $definition['odds'],
                    'stake_points' => $stake,
                    'potential_payout' => $potentialPayout,
                    'settlement_points' => $settlementPoints,
                    'status' => $status,
                    'idempotency_key' => 'seed-bet-base-'.$index,
                    'placed_at' => now()->subMinutes(25 - ($index * 3)),
                    'cancelled_at' => $definition['cancelled_at'],
                    'settled_at' => $definition['settled_at'],
                    'payout' => $payout,
                    'meta' => ['seed' => self::class],
                ]
            );

            $wallet = UserWallet::query()->where('user_id', $definition['user_id'])->firstOrFail();

            $this->applyWalletTransaction(
                wallet: $wallet,
                userId: (int) $definition['user_id'],
                type: WalletTransaction::TYPE_STAKE,
                amount: -$stake,
                uniqueKey: 'seed.bet.stake.'.$bet->id.'.v1',
                refType: WalletTransaction::REF_TYPE_BET,
                refId: (string) $bet->id,
                metadata: ['match_id' => $definition['match_id']]
            );

            if ($status === Bet::STATUS_WON && (int) $payout > 0) {
                $this->applyWalletTransaction(
                    wallet: $wallet,
                    userId: (int) $definition['user_id'],
                    type: WalletTransaction::TYPE_PAYOUT,
                    amount: (int) $payout,
                    uniqueKey: 'seed.bet.payout.'.$bet->id.'.v1',
                    refType: WalletTransaction::REF_TYPE_BET,
                    refId: (string) $bet->id,
                    metadata: ['match_id' => $definition['match_id']]
                );
            }

            if (in_array($status, [Bet::STATUS_WON, Bet::STATUS_LOST, Bet::STATUS_VOID], true) && $definition['settled_at']) {
                BetSettlement::query()->updateOrCreate(
                    ['bet_id' => $bet->id],
                    [
                        'outcome' => $status === Bet::STATUS_WON
                            ? Bet::STATUS_WON
                            : ($status === Bet::STATUS_VOID ? Bet::STATUS_VOID : Bet::STATUS_LOST),
                        'payout' => (int) ($payout ?? 0),
                        'settled_at' => $definition['settled_at'],
                        'metadata' => ['seed' => self::class],
                    ]
                );
            }
        }
    }

    /**
     * @param array<string, mixed> $metadata
     */
    private function applyWalletTransaction(
        UserWallet $wallet,
        int $userId,
        string $type,
        int $amount,
        string $uniqueKey,
        ?string $refType = null,
        ?string $refId = null,
        array $metadata = []
    ): void {
        $existing = WalletTransaction::query()
            ->where('user_id', $userId)
            ->where('unique_key', $uniqueKey)
            ->first();

        if ($existing) {
            return;
        }

        $newBalance = $wallet->balance + $amount;
        if ($newBalance < 0) {
            $newBalance = 0;
        }

        WalletTransaction::query()->create([
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $newBalance,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'unique_key' => $uniqueKey,
            'metadata' => $metadata ?: null,
            'created_at' => now(),
        ]);

        $wallet->balance = $newBalance;
        $wallet->save();
    }
}

