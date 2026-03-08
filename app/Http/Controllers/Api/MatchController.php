<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EsportMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MatchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(EsportMatch::statuses())],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 20);

        $query = EsportMatch::query()
            ->select([
                'id',
                'match_key',
                'game_key',
                'event_type',
                'event_name',
                'competition_name',
                'competition_stage',
                'competition_split',
                'best_of',
                'parent_match_id',
                'home_team',
                'away_team',
                'starts_at',
                'locked_at',
                'ends_at',
                'status',
                'result',
                'team_a_score',
                'team_b_score',
                'child_matches_unlocked_at',
                'settled_at',
                'created_at',
                'updated_at',
            ])
            ->publicFeed();

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return response()->json([
            'data' => $query->limit($limit)->get(),
        ]);
    }
}
