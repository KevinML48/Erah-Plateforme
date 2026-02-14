<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EsportMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        $upcoming = EsportMatch::query()
            ->withCount('predictions')
            ->withCount('markets')
            ->whereIn('status', ['DRAFT', 'OPEN', 'LOCKED', 'LIVE'])
            ->orderBy('starts_at')
            ->get();

        $past = EsportMatch::query()
            ->withCount('predictions')
            ->withCount('markets')
            ->where('status', 'COMPLETED')
            ->orderByDesc('starts_at')
            ->get();

        if (!$request->expectsJson()) {
            return view('pages.matches.index', [
                'title' => 'Matchs',
                'upcoming' => $upcoming,
                'past' => $past,
            ]);
        }

        return response()->json([
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }

    public function show(EsportMatch $match, Request $request): JsonResponse|View
    {
        $user = $request->user();

        $match->load([
            'predictions' => function ($query): void {
                $query->select(['id', 'match_id', 'user_id', 'prediction', 'stake_points', 'potential_points', 'is_correct', 'points_awarded', 'created_at']);
            },
            'markets' => function ($query): void {
                $query->with(['options:id,market_id,label,key,odds_decimal,popularity_weight'])
                    ->select(['id', 'match_id', 'code', 'name', 'status', 'settled_at']);
            },
            'creator:id,name',
        ]);

        $myPrediction = null;
        $myTicket = null;
        if ($user) {
            $myPrediction = $match->predictions()->where('user_id', $user->id)->first();
            $myTicket = $match->tickets()
                ->with(['selections.option:id,label,key', 'selections.market:id,name,code'])
                ->where('user_id', $user->id)
                ->first();
        }

        if (!$request->expectsJson()) {
            return view('pages.matches.show', [
                'title' => 'Parier sur match',
                'match' => $match,
                'myPrediction' => $myPrediction,
                'myTicket' => $myTicket,
                'userPointsBalance' => (int) ($user?->points_balance ?? 0),
            ]);
        }

        return response()->json([
            'match' => $match,
            'my_prediction' => $myPrediction,
            'my_ticket' => $myTicket,
        ]);
    }
}
