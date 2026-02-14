<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MarketStatus;
use App\Enums\MatchStatus;
use App\Enums\PointTransactionType;
use App\Enums\RewardRedemptionStatus;
use App\Enums\SelectionStatus;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Exceptions\PointTransactionAlreadyProcessedException;
use App\Models\EsportMatch;
use App\Models\Game;
use App\Models\Market;
use App\Models\MarketOption;
use App\Models\MatchTeam;
use App\Models\Reward;
use App\Models\RewardRedemption;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketSelection;
use App\Models\User;
use App\Services\PointService;
use App\Services\RankService;
use App\Services\RedemptionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AppDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RankSeeder::class);

        [$admin, $users] = $this->seedUsers();
        $this->seedPoints($users);
        $this->seedRewardsAndRedemptions($admin, $users);
        $this->seedGamesAndTeams();
        $this->seedMatchesMarketsTickets($admin, $users);
        $this->refreshRanks($users);
    }

    /**
     * @return array{0: User, 1: array<int, User>}
     */
    private function seedUsers(): array
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'kevinmolines841@gmail.com'],
            [
                'name' => 'Kevin Molines',
                'password' => bcrypt('password'),
                'role' => UserRole::Admin,
                'is_admin' => true,
                'avatar_url' => '/images/user/user-01.jpg',
            ]
        );

        $seedUsers = [
            [
                'email' => 'test@example.com',
                'name' => 'Test User',
                'avatar_url' => '/images/user/user-02.jpg',
            ],
            [
                'email' => 'alex@erah.gg',
                'name' => 'Alex ERAH',
                'avatar_url' => '/images/user/user-03.jpg',
            ],
            [
                'email' => 'sarah@erah.gg',
                'name' => 'Sarah ERAH',
                'avatar_url' => '/images/user/user-04.jpg',
            ],
            [
                'email' => 'noa@erah.gg',
                'name' => 'Noa ERAH',
                'avatar_url' => '/images/user/user-05.jpg',
            ],
        ];

        $users = [$admin];
        foreach ($seedUsers as $seedUser) {
            $users[] = User::query()->updateOrCreate(
                ['email' => $seedUser['email']],
                [
                    'name' => $seedUser['name'],
                    'password' => bcrypt('password'),
                    'role' => UserRole::User,
                    'is_admin' => false,
                    'avatar_url' => $seedUser['avatar_url'],
                ]
            );
        }

        return [$admin, $users];
    }

    /**
     * @param  array<int, User>  $users
     */
    private function seedPoints(array $users): void
    {
        $service = app(PointService::class);

        $credits = [
            $users[0]->id => 4000,
            $users[1]->id => 5500,
            $users[2]->id => 3200,
            $users[3]->id => 1800,
            $users[4]->id => 900,
        ];

        foreach ($users as $user) {
            $amount = $credits[$user->id] ?? 1000;
            try {
                $service->addPoints(
                    user: $user,
                    amount: $amount,
                    type: PointTransactionType::AdminAdjustment->value,
                    description: 'Seed initial points',
                    referenceId: $user->id,
                    referenceType: 'seed_user',
                    idempotencyKey: 'seed:points:user:'.$user->id
                );
            } catch (PointTransactionAlreadyProcessedException) {
                // already seeded
            }
        }
    }

    /**
     * @param  array<int, User>  $users
     */
    private function seedRewardsAndRedemptions(User $admin, array $users): void
    {
        $rewardsData = [
            [
                'slug' => 'maillot-erah-2026',
                'name' => 'Maillot ERAH 2026',
                'description' => 'Maillot officiel de la saison 2026.',
                'points_cost' => 2500,
                'stock' => 20,
                'is_active' => true,
            ],
            [
                'slug' => 'hoodie-erah-black',
                'name' => 'Hoodie ERAH Black',
                'description' => 'Hoodie edition communautaire.',
                'points_cost' => 3200,
                'stock' => 12,
                'is_active' => true,
            ],
            [
                'slug' => 'mousepad-erah-pro',
                'name' => 'Mousepad ERAH Pro',
                'description' => 'Mousepad large performance.',
                'points_cost' => 900,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'slug' => 'vip-discord-pass',
                'name' => 'VIP Discord Pass',
                'description' => 'Role VIP sur Discord ERAH.',
                'points_cost' => 700,
                'stock' => null,
                'is_active' => true,
            ],
        ];

        $rewards = [];
        foreach ($rewardsData as $row) {
            $rewards[] = Reward::query()->updateOrCreate(
                ['slug' => $row['slug']],
                array_merge($row, ['created_by' => $admin->id])
            );
        }

        $redemptionService = app(RedemptionService::class);
        $seedScenarios = [
            ['key' => 'seed:redemption:pending', 'user' => $users[1], 'reward' => $rewards[2], 'action' => 'pending'],
            ['key' => 'seed:redemption:approved', 'user' => $users[2], 'reward' => $rewards[3], 'action' => 'approved'],
            ['key' => 'seed:redemption:shipped', 'user' => $users[0], 'reward' => $rewards[2], 'action' => 'shipped'],
            ['key' => 'seed:redemption:rejected', 'user' => $users[3], 'reward' => $rewards[3], 'action' => 'rejected'],
            ['key' => 'seed:redemption:cancelled', 'user' => $users[4], 'reward' => $rewards[3], 'action' => 'cancelled'],
        ];

        foreach ($seedScenarios as $scenario) {
            $existing = RewardRedemption::query()
                ->where('admin_note', $scenario['key'])
                ->first();

            if ($existing) {
                continue;
            }

            $redemption = $redemptionService->createRedemption(
                user: $scenario['user'],
                reward: $scenario['reward'],
                shippingData: [
                    'shipping_name' => $scenario['user']->name,
                    'shipping_email' => $scenario['user']->email,
                    'shipping_country' => 'France',
                ]
            );

            if ($scenario['action'] === 'approved') {
                $redemption = $redemptionService->approveRedemption($admin, $redemption);
            } elseif ($scenario['action'] === 'shipped') {
                $redemption = $redemptionService->approveRedemption($admin, $redemption);
                $redemption = $redemptionService->markShipped($admin, $redemption, 'TRK-ERAH-2026-'.(string) $redemption->id);
            } elseif ($scenario['action'] === 'rejected') {
                $redemption = $redemptionService->rejectRedemption($admin, $redemption, $scenario['key']);
            } elseif ($scenario['action'] === 'cancelled') {
                $redemption = $redemptionService->cancelRedemption($scenario['user'], $redemption);
            }

            $redemption->admin_note = $scenario['key'];
            $redemption->save();
        }
    }

    private function seedGamesAndTeams(): void
    {
        $games = [
            ['name' => 'VALORANT', 'slug' => 'valorant'],
            ['name' => 'League of Legends', 'slug' => 'lol'],
            ['name' => 'Counter-Strike 2', 'slug' => 'cs2'],
        ];

        foreach ($games as $game) {
            Game::query()->updateOrCreate(['slug' => $game['slug']], $game);
        }

        $teams = [
            ['name' => 'ERAH', 'slug' => 'erah', 'logo_url' => '/images/logo/logo-icon.svg'],
            ['name' => 'Nova Squad', 'slug' => 'nova-squad', 'logo_url' => '/images/logo/logo-icon.svg'],
            ['name' => 'Titan Core', 'slug' => 'titan-core', 'logo_url' => '/images/logo/logo-icon.svg'],
            ['name' => 'Velocity', 'slug' => 'velocity', 'logo_url' => '/images/logo/logo-icon.svg'],
        ];

        foreach ($teams as $team) {
            Team::query()->updateOrCreate(['slug' => $team['slug']], $team);
        }
    }

    /**
     * @param  array<int, User>  $users
     */
    private function seedMatchesMarketsTickets(User $admin, array $users): void
    {
        $valorant = Game::query()->where('slug', 'valorant')->firstOrFail();
        $teamErah = Team::query()->where('slug', 'erah')->firstOrFail();
        $teamNova = Team::query()->where('slug', 'nova-squad')->firstOrFail();
        $teamTitan = Team::query()->where('slug', 'titan-core')->firstOrFail();

        $openMatch = EsportMatch::query()->updateOrCreate(
            ['title' => 'ERAH vs Nova Squad', 'starts_at' => now()->addDays(2)->startOfHour()],
            [
                'game_id' => $valorant->id,
                'game' => 'VALORANT',
                'format' => 'BO3',
                'lock_at' => now()->addDays(2)->startOfHour()->subMinutes(10),
                'status' => MatchStatus::Open,
                'points_reward' => 100,
                'created_by' => $admin->id,
            ]
        );

        $completedMatch = EsportMatch::query()->updateOrCreate(
            ['title' => 'ERAH vs Titan Core', 'starts_at' => now()->subDays(3)->startOfHour()],
            [
                'game_id' => $valorant->id,
                'game' => 'VALORANT',
                'format' => 'BO3',
                'lock_at' => now()->subDays(3)->startOfHour()->subMinutes(10),
                'status' => MatchStatus::Completed,
                'result_json' => ['winner' => 'ERAH', 'score' => '2-1'],
                'completed_at' => now()->subDays(3)->addHours(2),
                'points_reward' => 100,
                'created_by' => $admin->id,
            ]
        );

        $this->syncMatchTeams($openMatch->id, [$teamErah->id, $teamNova->id]);
        $this->syncMatchTeams($completedMatch->id, [$teamErah->id, $teamTitan->id]);

        $openMarketWinner = Market::query()->updateOrCreate(
            ['match_id' => $openMatch->id, 'code' => 'MATCH_WINNER'],
            ['name' => 'Match Winner', 'status' => MarketStatus::Open]
        );
        $openMarketScore = Market::query()->updateOrCreate(
            ['match_id' => $openMatch->id, 'code' => 'EXACT_SCORE'],
            ['name' => 'Exact Score', 'status' => MarketStatus::Open]
        );

        $this->syncMarketOptions($openMarketWinner->id, [
            ['key' => 'ERAH_WIN', 'label' => 'ERAH', 'odds_decimal' => 1.75, 'popularity_weight' => 0.93],
            ['key' => 'NOVA_WIN', 'label' => 'Nova Squad', 'odds_decimal' => 2.10, 'popularity_weight' => 1.06],
        ]);
        $this->syncMarketOptions($openMarketScore->id, [
            ['key' => '2-0', 'label' => 'ERAH 2-0', 'odds_decimal' => 2.50, 'popularity_weight' => 1.12],
            ['key' => '2-1', 'label' => 'ERAH 2-1', 'odds_decimal' => 2.90, 'popularity_weight' => 1.08],
            ['key' => '1-2', 'label' => 'Nova 2-1', 'odds_decimal' => 3.20, 'popularity_weight' => 1.15],
            ['key' => '0-2', 'label' => 'Nova 2-0', 'odds_decimal' => 3.60, 'popularity_weight' => 1.20],
        ]);

        $completedMarketWinner = Market::query()->updateOrCreate(
            ['match_id' => $completedMatch->id, 'code' => 'MATCH_WINNER'],
            ['name' => 'Match Winner', 'status' => MarketStatus::Settled, 'settled_at' => now()->subDays(3)->addHours(2)]
        );
        $completedMarketScore = Market::query()->updateOrCreate(
            ['match_id' => $completedMatch->id, 'code' => 'EXACT_SCORE'],
            ['name' => 'Exact Score', 'status' => MarketStatus::Settled, 'settled_at' => now()->subDays(3)->addHours(2)]
        );

        $winnerOptions = $this->syncMarketOptions($completedMarketWinner->id, [
            ['key' => 'ERAH_WIN', 'label' => 'ERAH', 'odds_decimal' => 1.80, 'is_winner' => true],
            ['key' => 'TITAN_WIN', 'label' => 'Titan Core', 'odds_decimal' => 2.00, 'is_winner' => false],
        ], now()->subDays(3)->addHours(2));
        $scoreOptions = $this->syncMarketOptions($completedMarketScore->id, [
            ['key' => '2-0', 'label' => 'ERAH 2-0', 'odds_decimal' => 2.60, 'is_winner' => false],
            ['key' => '2-1', 'label' => 'ERAH 2-1', 'odds_decimal' => 2.90, 'is_winner' => true],
            ['key' => '1-2', 'label' => 'Titan 2-1', 'odds_decimal' => 3.10, 'is_winner' => false],
        ], now()->subDays(3)->addHours(2));

        $this->seedHistoricalTickets($users, $completedMatch, $completedMarketWinner, $completedMarketScore, $winnerOptions, $scoreOptions);
    }

    private function syncMatchTeams(int $matchId, array $teamIds): void
    {
        foreach (['home', 'away'] as $i => $side) {
            MatchTeam::query()->updateOrCreate(
                ['match_id' => $matchId, 'side' => $side],
                ['team_id' => $teamIds[$i]]
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<string, MarketOption>
     */
    private function syncMarketOptions(int $marketId, array $rows, $settledAt = null): array
    {
        $map = [];
        foreach ($rows as $row) {
            $payload = Arr::except($row, ['key']);
            if ($settledAt !== null) {
                $payload['settled_at'] = $settledAt;
            }

            $option = MarketOption::query()->updateOrCreate(
                ['market_id' => $marketId, 'key' => $row['key']],
                $payload
            );
            $map[$row['key']] = $option;
        }

        return $map;
    }

    /**
     * @param  array<int, User>  $users
     * @param  array<string, MarketOption>  $winnerOptions
     * @param  array<string, MarketOption>  $scoreOptions
     */
    private function seedHistoricalTickets(
        array $users,
        EsportMatch $match,
        Market $winnerMarket,
        Market $scoreMarket,
        array $winnerOptions,
        array $scoreOptions
    ): void {
        $service = app(PointService::class);

        $ticketsData = [
            [
                'user' => $users[1],
                'stake' => 200,
                'status' => TicketStatus::Won,
                'payout' => 1044,
                'selections' => [
                    ['market' => $winnerMarket, 'option' => $winnerOptions['ERAH_WIN'], 'status' => SelectionStatus::Won],
                    ['market' => $scoreMarket, 'option' => $scoreOptions['2-1'], 'status' => SelectionStatus::Won],
                ],
            ],
            [
                'user' => $users[2],
                'stake' => 150,
                'status' => TicketStatus::Lost,
                'payout' => 0,
                'selections' => [
                    ['market' => $winnerMarket, 'option' => $winnerOptions['TITAN_WIN'], 'status' => SelectionStatus::Lost],
                    ['market' => $scoreMarket, 'option' => $scoreOptions['1-2'], 'status' => SelectionStatus::Lost],
                ],
            ],
            [
                'user' => $users[3],
                'stake' => 100,
                'status' => TicketStatus::Void,
                'payout' => 0,
                'refund' => 100,
                'selections' => [
                    ['market' => $winnerMarket, 'option' => $winnerOptions['ERAH_WIN'], 'status' => SelectionStatus::Void],
                ],
            ],
        ];

        foreach ($ticketsData as $idx => $row) {
            $existing = Ticket::query()
                ->where('user_id', $row['user']->id)
                ->where('match_id', $match->id)
                ->first();

            if ($existing) {
                continue;
            }

            DB::transaction(function () use ($row, $match, $idx, $service): void {
                $totalOdds = 1.0;
                foreach ($row['selections'] as $selection) {
                    $totalOdds *= (float) $selection['option']->odds_decimal;
                }

                $ticket = Ticket::query()->create([
                    'user_id' => $row['user']->id,
                    'match_id' => $match->id,
                    'stake_points' => $row['stake'],
                    'total_odds_decimal' => round($totalOdds, 3),
                    'potential_payout_points' => (int) floor($row['stake'] * $totalOdds),
                    'status' => $row['status'],
                    'locked_at' => now()->subDays(3)->subHour(),
                    'settled_at' => now()->subDays(3)->addHours(2),
                    'payout_points' => $row['payout'],
                    'refunded_points' => $row['refund'] ?? 0,
                ]);

                foreach ($row['selections'] as $selection) {
                    TicketSelection::query()->create([
                        'ticket_id' => $ticket->id,
                        'market_id' => $selection['market']->id,
                        'option_id' => $selection['option']->id,
                        'odds_decimal_snapshot' => $selection['option']->odds_decimal,
                        'status' => $selection['status'],
                    ]);
                }

                try {
                    $service->removePoints(
                        user: $row['user'],
                        amount: $row['stake'],
                        type: PointTransactionType::TicketStake->value,
                        description: 'Seed historical ticket #'.$ticket->id,
                        referenceId: $ticket->id,
                        referenceType: 'ticket',
                        idempotencyKey: 'seed:ticket:stake:'.$ticket->id
                    );
                } catch (PointTransactionAlreadyProcessedException) {
                }

                if ((int) $row['payout'] > 0) {
                    try {
                        $service->addPoints(
                            user: $row['user'],
                            amount: (int) $row['payout'],
                            type: PointTransactionType::TicketPayout->value,
                            description: 'Seed ticket payout #'.$ticket->id,
                            referenceId: $ticket->id,
                            referenceType: 'ticket',
                            idempotencyKey: 'seed:ticket:payout:'.$ticket->id
                        );
                    } catch (PointTransactionAlreadyProcessedException) {
                    }
                }

                if ((int) ($row['refund'] ?? 0) > 0) {
                    try {
                        $service->addPoints(
                            user: $row['user'],
                            amount: (int) $row['refund'],
                            type: PointTransactionType::TicketRefund->value,
                            description: 'Seed ticket refund #'.$ticket->id,
                            referenceId: $ticket->id,
                            referenceType: 'ticket',
                            idempotencyKey: 'seed:ticket:refund:'.$ticket->id
                        );
                    } catch (PointTransactionAlreadyProcessedException) {
                    }
                }
            });
        }
    }

    /**
     * @param  array<int, User>  $users
     */
    private function refreshRanks(array $users): void
    {
        $rankService = app(RankService::class);
        foreach ($users as $user) {
            $user->refresh();
            $rankService->updateUserRank($user);
        }
    }
}

