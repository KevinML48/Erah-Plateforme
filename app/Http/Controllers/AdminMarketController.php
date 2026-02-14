<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MarketStatus;
use App\Http\Requests\SettleMarketRequest;
use App\Http\Requests\StoreMarketRequest;
use App\Http\Requests\StoreOptionRequest;
use App\Models\EsportMatch;
use App\Models\Market;
use App\Models\MarketOption;
use App\Services\AdminAuditService;
use App\Services\SettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminMarketController extends Controller
{
    public function store(
        StoreMarketRequest $request,
        EsportMatch $match,
        AdminAuditService $auditService
    ): JsonResponse {
        $this->authorize('manage-market');

        $market = $match->markets()->create($request->validated());

        $auditService->log(
            actor: $request->user(),
            action: 'market.create',
            entityType: 'market',
            entityId: (int) $market->id,
            metadata: ['after' => $market->toArray()]
        );

        return response()->json([
            'message' => 'Market created.',
            'market' => $market,
        ], 201);
    }

    public function update(
        StoreMarketRequest $request,
        Market $market,
        AdminAuditService $auditService
    ): JsonResponse {
        $this->authorize('manage-market');

        $before = $market->toArray();
        $market->fill($request->validated());
        $market->save();

        $auditService->log(
            actor: $request->user(),
            action: 'market.update',
            entityType: 'market',
            entityId: (int) $market->id,
            metadata: ['before' => $before, 'after' => $market->toArray()]
        );

        return response()->json([
            'message' => 'Market updated.',
            'market' => $market,
        ]);
    }

    public function storeOption(
        StoreOptionRequest $request,
        Market $market,
        AdminAuditService $auditService
    ): JsonResponse {
        $this->authorize('manage-market');

        $option = $market->options()->create($request->validated());

        $auditService->log(
            actor: $request->user(),
            action: 'market_option.create',
            entityType: 'market_option',
            entityId: (int) $option->id,
            metadata: ['after' => $option->toArray()]
        );

        return response()->json([
            'message' => 'Option created.',
            'option' => $option,
        ], 201);
    }

    public function updateOption(
        StoreOptionRequest $request,
        MarketOption $option,
        AdminAuditService $auditService
    ): JsonResponse {
        $this->authorize('manage-market');

        $before = $option->toArray();
        $option->fill($request->validated());
        $option->save();

        $auditService->log(
            actor: $request->user(),
            action: 'market_option.update',
            entityType: 'market_option',
            entityId: (int) $option->id,
            metadata: ['before' => $before, 'after' => $option->toArray()]
        );

        return response()->json([
            'message' => 'Option updated.',
            'option' => $option,
        ]);
    }

    public function lock(Request $request, Market $market, AdminAuditService $auditService): JsonResponse
    {
        $this->authorize('manage-market');

        if (!in_array($market->status, [MarketStatus::Settled, MarketStatus::Void], true)) {
            $market->status = MarketStatus::Locked;
            $market->save();
        }

        $auditService->log(
            actor: $request->user(),
            action: 'market.lock',
            entityType: 'market',
            entityId: (int) $market->id,
            metadata: ['status' => $market->status->value]
        );

        return response()->json([
            'message' => 'Market locked.',
            'market' => $market,
        ]);
    }

    public function settle(SettleMarketRequest $request, Market $market, SettlementService $settlementService): JsonResponse
    {
        $this->authorize('manage-market');

        $settled = $settlementService->settleMarket(
            market: $market,
            winnerOptionId: $request->filled('winner_option_id') ? (int) $request->integer('winner_option_id') : null,
            actorUserId: (int) $request->user()->id
        );

        return response()->json([
            'message' => 'Market settled.',
            'market' => $settled,
        ]);
    }
}
