<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InsufficientPointsException;
use App\Exceptions\MatchNotOpenException;
use App\Exceptions\PointTransactionAlreadyProcessedException;
use App\Exceptions\PredictionAlreadyExistsException;
use App\Http\Requests\PlacePredictionRequest;
use App\Models\EsportMatch;
use App\Models\Prediction;
use App\Services\EventTrackingService;
use App\Services\PredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function store(
        PlacePredictionRequest $request,
        EsportMatch $match,
        PredictionService $predictionService,
        EventTrackingService $eventTrackingService
    ): JsonResponse {
        $user = $request->user();
        abort_unless($user, 401);

        try {
            $prediction = $predictionService->placePrediction(
                user: $user,
                match: $match,
                prediction: (string) $request->string('prediction'),
                stakePoints: (int) $request->integer('stake_points')
            );
        } catch (MatchNotOpenException|PredictionAlreadyExistsException|InsufficientPointsException|PointTransactionAlreadyProcessedException $exception) {
            if (!$request->expectsJson()) {
                return back()->withInput()->withErrors(['prediction' => $exception->getMessage()]);
            }

            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        $eventTrackingService->trackAction($user, 'prediction_created', [
            'match_id' => $match->id,
            'prediction_id' => $prediction->id,
        ]);

        if (!$request->expectsJson()) {
            return redirect()
                ->route('matches.show', $match)
                ->with('success', 'Pronostic enregistre avec succes.');
        }

        return response()->json([
            'message' => 'Prediction placed successfully.',
            'prediction' => $prediction,
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $predictions = Prediction::query()
            ->with(['match:id,game,title,starts_at,status,result,points_reward'])
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($predictions);
    }
}
