<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\PredictionChoice;
use App\Enums\PointTransactionType;
use App\Exceptions\MatchNotOpenException;
use App\Exceptions\PredictionAlreadyExistsException;
use App\Models\EsportMatch;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PredictionService
{
    public function __construct(
        private readonly PointService $pointService
    ) {
    }

    public function placePrediction(User $user, EsportMatch $match, string $prediction, int $stakePoints): Prediction
    {
        if (!$match->isOpen()) {
            throw new MatchNotOpenException();
        }

        if (Prediction::query()->where('match_id', $match->id)->where('user_id', $user->id)->exists()) {
            throw new PredictionAlreadyExistsException();
        }

        $potentialPoints = (int) floor($stakePoints + ($stakePoints * ((int) $match->points_reward / 100)));

        try {
            return DB::transaction(function () use ($user, $match, $prediction, $stakePoints, $potentialPoints): Prediction {
                $this->pointService->removePoints(
                    user: $user,
                    amount: $stakePoints,
                    type: PointTransactionType::PredictionStake->value,
                    description: 'Mise pronostic match #'.$match->id,
                    referenceId: (int) $match->id,
                    referenceType: 'match',
                    idempotencyKey: 'prediction-stake:match-'.$match->id.':user-'.$user->id
                );

                return Prediction::query()->create([
                    'match_id' => $match->id,
                    'user_id' => $user->id,
                    'prediction' => PredictionChoice::from($prediction),
                    'stake_points' => $stakePoints,
                    'potential_points' => $potentialPoints,
                ]);
            });
        } catch (QueryException $exception) {
            if (str_contains(strtolower((string) $exception->getMessage()), 'predictions_match_id_user_id_unique')) {
                throw new PredictionAlreadyExistsException();
            }

            throw $exception;
        }
    }
}
