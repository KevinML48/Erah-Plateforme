<?php
declare(strict_types=1);

use App\Enums\MarketStatus;
use App\Enums\MatchStatus;
use App\Enums\PointTransactionType;
use App\Enums\TicketStatus;
use App\Models\EsportMatch;
use App\Models\Market;
use App\Models\MarketOption;
use App\Models\PointLog;
use App\Models\User;
use App\Services\SettlementService;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a ticket and debits stake points', function (): void {
    $user = User::factory()->create(['points_balance' => 1000]);

    $match = EsportMatch::query()->create([
        'game' => 'VALORANT',
        'title' => 'ERAH vs Team X',
        'starts_at' => now()->addHours(5),
        'lock_at' => now()->addHours(4),
        'status' => MatchStatus::Open,
        'points_reward' => 100,
    ]);

    $market = Market::query()->create([
        'match_id' => $match->id,
        'code' => 'MATCH_WINNER',
        'name' => 'Match Winner',
        'status' => MarketStatus::Open,
    ]);

    $option = MarketOption::query()->create([
        'market_id' => $market->id,
        'label' => 'ERAH',
        'key' => 'ERAH_WIN',
        'odds_decimal' => 1.80,
    ]);

    $ticket = app(TicketService::class)->createTicket(
        user: $user,
        match: $match,
        stake: 200,
        selections: [$option->id]
    );

    $user->refresh();
    $ticket->refresh();

    expect($ticket->status)->toBe(TicketStatus::Pending);
    expect($ticket->stake_points)->toBe(200);
    expect((int) $ticket->potential_payout_points)->toBe(360);
    expect($user->points_balance)->toBe(800);
    expect(PointLog::query()->where('type', PointTransactionType::TicketStake->value)->count())->toBe(1);
});

it('settles a market and pays out winning ticket', function (): void {
    $user = User::factory()->create(['points_balance' => 1000]);

    $match = EsportMatch::query()->create([
        'game' => 'VALORANT',
        'title' => 'ERAH vs Team X',
        'starts_at' => now()->addHours(6),
        'lock_at' => now()->addHours(5),
        'status' => MatchStatus::Open,
        'points_reward' => 100,
    ]);

    $market = Market::query()->create([
        'match_id' => $match->id,
        'code' => 'MATCH_WINNER',
        'name' => 'Match Winner',
        'status' => MarketStatus::Open,
    ]);

    $winner = MarketOption::query()->create([
        'market_id' => $market->id,
        'label' => 'ERAH',
        'key' => 'ERAH_WIN',
        'odds_decimal' => 1.90,
    ]);

    MarketOption::query()->create([
        'market_id' => $market->id,
        'label' => 'OPPONENT',
        'key' => 'OPP_WIN',
        'odds_decimal' => 1.90,
    ]);

    $ticket = app(TicketService::class)->createTicket(
        user: $user,
        match: $match,
        stake: 100,
        selections: [$winner->id]
    );

    app(SettlementService::class)->settleMarket($market, (int) $winner->id, null);

    $user->refresh();
    $ticket->refresh();

    expect($ticket->status)->toBe(TicketStatus::Won);
    expect($ticket->payout_points)->toBe(190);
    expect($user->points_balance)->toBe(1090);
});

it('keeps payout idempotent when settling same market twice', function (): void {
    $user = User::factory()->create(['points_balance' => 1000]);

    $match = EsportMatch::query()->create([
        'game' => 'VALORANT',
        'title' => 'ERAH vs Team X',
        'starts_at' => now()->addHours(8),
        'lock_at' => now()->addHours(7),
        'status' => MatchStatus::Open,
        'points_reward' => 100,
    ]);

    $market = Market::query()->create([
        'match_id' => $match->id,
        'code' => 'MATCH_WINNER',
        'name' => 'Match Winner',
        'status' => MarketStatus::Open,
    ]);

    $winner = MarketOption::query()->create([
        'market_id' => $market->id,
        'label' => 'ERAH',
        'key' => 'ERAH_WIN',
        'odds_decimal' => 2.00,
    ]);

    app(TicketService::class)->createTicket(
        user: $user,
        match: $match,
        stake: 100,
        selections: [$winner->id]
    );

    $service = app(SettlementService::class);
    $service->settleMarket($market, (int) $winner->id, null);
    $service->settleMarket($market, (int) $winner->id, null);

    $user->refresh();

    expect($user->points_balance)->toBe(1100);
    expect(PointLog::query()->where('type', PointTransactionType::TicketPayout->value)->count())->toBe(1);
});

