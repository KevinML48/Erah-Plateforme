<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\PointTransactionType;
use App\Exceptions\MatchResultMissingException;
use App\Models\EsportMatch;
use App\Models\Prediction;
use Illuminate\Support\Facades\DB;

class PointsAwardingService
{
    public function __construct(
        private readonly PointService $pointService
    ) {
    }

    public function awardPointsForMatch(EsportMatch $match): void
    {
        if ($match->result === null) {
            throw new MatchResultMissingException();
        }

        DB::transaction(function () use ($match): void {
            Prediction::query()
                ->where('match_id', $match->id)
                ->whereNull('is_correct')
                ->update([
                    'is_correct' => DB::raw("CASE WHEN prediction = '".$match->result->value."' THEN 1 ELSE 0 END"),
                ]);

            Prediction::query()
                ->with('user:id,points_balance,rank_id')
                ->where('match_id', $match->id)
                ->where('is_correct', true)
                ->where('points_awarded', false)
                ->orderBy('id')
                ->chunkById(200, function ($predictions) use ($match): void {
                    foreach ($predictions as $prediction) {
                        $payoutAmount = max(0, (int) ($prediction->potential_points ?? 0));
                        if ($payoutAmount <= 0) {
                            continue;
                        }

                        $this->pointService->addPoints(
                            user: $prediction->user,
                            amount: $payoutAmount,
                            type: PointTransactionType::PredictionWin->value,
                            description: 'Gain pronostic match #'.$prediction->match_id,
                            referenceId: (int) $prediction->match_id,
                            referenceType: 'match',
                            idempotencyKey: 'prediction-win:match-'.$prediction->match_id.':prediction-'.$prediction->id
                        );

                        $prediction->points_awarded = true;
                        $prediction->save();
                    }
                }, 'id');
        });
    }
}
