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
use App\Services\SettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminMarketController extends Controller
{
    public function store(StoreMarketRequest $request, EsportMatch $match): JsonResponse
    {
        $market = $match->markets()->create($request->validated());

        return response()->json([
            'message' => 'Market created.',
            'market' => $market,
        ], 201);
    }

    public function update(StoreMarketRequest $request, Market $market): JsonResponse
    {
        $market->fill($request->validated());
        $market->save();

        return response()->json([
            'message' => 'Market updated.',
            'market' => $market,
        ]);
    }

    public function storeOption(StoreOptionRequest $request, Market $market): JsonResponse
    {
        $option = $market->options()->create($request->validated());

        return response()->json([
            'message' => 'Option created.',
            'option' => $option,
        ], 201);
    }

    public function updateOption(StoreOptionRequest $request, MarketOption $option): JsonResponse
    {
        $option->fill($request->validated());
        $option->save();

        return response()->json([
            'message' => 'Option updated.',
            'option' => $option,
        ]);
    }

    public function lock(Market $market): JsonResponse
    {
        if (!in_array($market->status, [MarketStatus::Settled, MarketStatus::Void], true)) {
            $market->status = MarketStatus::Locked;
            $market->save();
        }

        return response()->json([
            'message' => 'Market locked.',
            'market' => $market,
        ]);
    }

    public function settle(SettleMarketRequest $request, Market $market, SettlementService $settlementService): JsonResponse
    {
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

