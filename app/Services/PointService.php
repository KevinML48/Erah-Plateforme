<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\InsufficientPointsException;
use App\Exceptions\PointTransactionAlreadyProcessedException;
use App\Jobs\RefreshLeaderboardStatsForUserJob;
use App\Models\PointLog;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class PointService
{
    public function __construct(
        private readonly RankService $rankService,
        private readonly LeaderboardService $leaderboardService
    ) {
    }

    public function addPoints(
        User $user,
        int $amount,
        string $type,
        ?string $description = null,
        ?int $referenceId = null,
        ?string $referenceType = null,
        ?string $idempotencyKey = null
    ): PointLog {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than 0.');
        }

        try {
            return DB::transaction(function () use ($user, $amount, $type, $description, $referenceId, $referenceType, $idempotencyKey): PointLog {
                $lockedUser = User::query()
                    ->whereKey($user->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($idempotencyKey !== null) {
                    $existingLog = PointLog::query()
                        ->where('idempotency_key', $idempotencyKey)
                        ->first();

                    if ($existingLog) {
                        throw new PointTransactionAlreadyProcessedException();
                    }
                }

                $lockedUser->points_balance += $amount;
                $lockedUser->save();
                $this->rankService->updateUserRank($lockedUser);

                $log = PointLog::query()->create([
                    'user_id' => $lockedUser->id,
                    'amount' => $amount,
                    'type' => $type,
                    'description' => $description,
                    'reference_id' => $referenceId,
                    'reference_type' => $referenceType,
                    'idempotency_key' => $idempotencyKey,
                ]);

                $user->setAttribute('points_balance', $lockedUser->points_balance);
                $user->setAttribute('rank_id', $lockedUser->rank_id);

                DB::afterCommit(function () use ($lockedUser): void {
                    $this->leaderboardService->invalidateCache();
                    RefreshLeaderboardStatsForUserJob::dispatch((int) $lockedUser->id);
                });

                if ($amount >= 10000) {
                    Log::channel('daily')->warning('points.high_credit_detected', [
                        'user_id' => $lockedUser->id,
                        'amount' => $amount,
                        'type' => $type,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                    ]);
                }

                return $log;
            });
        } catch (QueryException $exception) {
            if ($idempotencyKey !== null && str_contains(strtolower((string) $exception->getMessage()), 'points_logs_idempotency_key_unique')) {
                throw new PointTransactionAlreadyProcessedException();
            }

            throw $exception;
        }
    }

    public function removePoints(
        User $user,
        int $amount,
        string $type,
        ?string $description = null,
        ?int $referenceId = null,
        ?string $referenceType = null,
        ?string $idempotencyKey = null
    ): PointLog {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than 0.');
        }

        try {
            return DB::transaction(function () use ($user, $amount, $type, $description, $referenceId, $referenceType, $idempotencyKey): PointLog {
                $lockedUser = User::query()
                    ->whereKey($user->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($idempotencyKey !== null) {
                    $existingLog = PointLog::query()
                        ->where('idempotency_key', $idempotencyKey)
                        ->first();

                    if ($existingLog) {
                        throw new PointTransactionAlreadyProcessedException();
                    }
                }

                if ($lockedUser->points_balance < $amount) {
                    throw new InsufficientPointsException();
                }

                $lockedUser->points_balance -= $amount;
                $lockedUser->save();
                $this->rankService->updateUserRank($lockedUser);

                $log = PointLog::query()->create([
                    'user_id' => $lockedUser->id,
                    'amount' => -$amount,
                    'type' => $type,
                    'description' => $description,
                    'reference_id' => $referenceId,
                    'reference_type' => $referenceType,
                    'idempotency_key' => $idempotencyKey,
                ]);

                $user->setAttribute('points_balance', $lockedUser->points_balance);
                $user->setAttribute('rank_id', $lockedUser->rank_id);

                DB::afterCommit(function () use ($lockedUser): void {
                    $this->leaderboardService->invalidateCache();
                    RefreshLeaderboardStatsForUserJob::dispatch((int) $lockedUser->id);
                });

                if ($amount >= 10000) {
                    Log::channel('daily')->warning('points.high_debit_detected', [
                        'user_id' => $lockedUser->id,
                        'amount' => $amount,
                        'type' => $type,
                        'reference_type' => $referenceType,
                        'reference_id' => $referenceId,
                    ]);
                }

                return $log;
            });
        } catch (QueryException $exception) {
            if ($idempotencyKey !== null && str_contains(strtolower((string) $exception->getMessage()), 'points_logs_idempotency_key_unique')) {
                throw new PointTransactionAlreadyProcessedException();
            }

            throw $exception;
        }
    }
}
