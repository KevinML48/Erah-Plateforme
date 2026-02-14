<?php
declare(strict_types=1);

namespace App\Services;

use App\Enums\MatchResult;
use App\Enums\MatchStatus;
use App\Exceptions\MatchAlreadyCompletedException;
use App\Exceptions\MatchNotOpenException;
use App\Exceptions\MatchResultMissingException;
use App\Models\EsportMatch;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MatchService
{
    public function __construct(
        private readonly PointsAwardingService $pointsAwardingService
    ) {
    }

    public function createMatch(array $data, User $admin): EsportMatch
    {
        $payload = Arr::only($data, [
            'game_id',
            'game',
            'title',
            'format',
            'starts_at',
            'lock_at',
            'status',
            'result',
            'result_json',
            'points_reward',
        ]);
        $payload['created_by'] = $admin->id;

        return EsportMatch::query()->create($payload);
    }

    public function updateMatch(EsportMatch $match, array $data): EsportMatch
    {
        if ($match->isCompleted()) {
            throw new MatchAlreadyCompletedException();
        }

        $payload = Arr::only($data, [
            'game_id',
            'game',
            'title',
            'format',
            'starts_at',
            'lock_at',
            'status',
            'result_json',
            'points_reward',
        ]);

        $match->fill($payload);
        $match->save();

        return $match->refresh();
    }

    public function openPredictions(EsportMatch $match): EsportMatch
    {
        if ($match->isCompleted()) {
            throw new MatchAlreadyCompletedException();
        }

        $match->status = MatchStatus::Open;
        $match->predictions_locked_at = null;
        $match->save();

        return $match->refresh();
    }

    public function lockPredictions(EsportMatch $match): EsportMatch
    {
        if ($match->isCompleted()) {
            throw new MatchAlreadyCompletedException();
        }

        if (!$match->isOpen()) {
            throw new MatchNotOpenException('Match must be OPEN before locking predictions.');
        }

        $match->status = MatchStatus::Locked;
        $match->predictions_locked_at = now();
        $match->save();

        return $match->refresh();
    }

    public function completeMatchWithResult(EsportMatch $match, string $result): EsportMatch
    {
        if ($match->isCompleted()) {
            throw new MatchAlreadyCompletedException();
        }

        if (!in_array($result, [MatchResult::Win->value, MatchResult::Lose->value], true)) {
            throw new MatchResultMissingException();
        }

        DB::transaction(function () use ($match, $result): void {
            $lockedMatch = EsportMatch::query()
                ->whereKey($match->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedMatch->isCompleted()) {
                throw new MatchAlreadyCompletedException();
            }

            $lockedMatch->result = MatchResult::from($result);
            $lockedMatch->status = MatchStatus::Completed;
            $lockedMatch->completed_at = now();
            $lockedMatch->predictions_locked_at = $lockedMatch->predictions_locked_at ?? now();
            $lockedMatch->save();

            $this->pointsAwardingService->awardPointsForMatch($lockedMatch);
        });

        return $match->refresh();
    }

    public function setLive(EsportMatch $match): EsportMatch
    {
        if ($match->isCompleted()) {
            throw new MatchAlreadyCompletedException();
        }

        $match->status = MatchStatus::Live;
        $match->predictions_locked_at = $match->predictions_locked_at ?? now();
        $match->save();

        return $match->refresh();
    }

    public function cancelMatch(EsportMatch $match): EsportMatch
    {
        if ($match->isCompleted()) {
            throw new MatchAlreadyCompletedException();
        }

        $match->status = MatchStatus::Cancelled;
        $match->completed_at = now();
        $match->save();

        return $match->refresh();
    }
}
