<?php

namespace App\Services;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\LiveCode;
use App\Models\LiveCodeRedemption;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class LiveCodeService
{
    public function __construct(
        private readonly RewardGrantService $rewardGrantService,
        private readonly MissionEngine $missionEngine,
        private readonly AchievementService $achievementService,
        private readonly NotifyAction $notifyAction,
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function generate(array $payload, User $actor): LiveCode
    {
        $code = Str::upper((string) ($payload['code'] ?? Str::random(8)));

        $liveCode = LiveCode::query()->create([
            'code' => $code,
            'label' => $payload['label'],
            'description' => $payload['description'] ?? null,
            'status' => $payload['status'] ?? 'draft',
            'reward_points' => (int) ($payload['reward_points'] ?? 0),
            'bet_points' => (int) ($payload['bet_points'] ?? 0),
            'xp_reward' => (int) ($payload['xp_reward'] ?? 0),
            'usage_limit' => $payload['usage_limit'] ?? null,
            'per_user_limit' => (int) ($payload['per_user_limit'] ?? 1),
            'expires_at' => $payload['expires_at'] ?? null,
            'mission_template_id' => $payload['mission_template_id'] ?? null,
            'created_by' => $actor->id,
            'meta' => $payload['meta'] ?? null,
        ]);

        $this->storeAuditLogAction->execute(
            action: 'live-codes.created',
            actor: $actor,
            target: $liveCode,
            context: [
                'live_code_id' => $liveCode->id,
                'code' => $liveCode->code,
                'status' => $liveCode->status,
            ],
        );

        return $liveCode;
    }

    public function redeem(User $user, string $code): LiveCodeRedemption
    {
        return DB::transaction(function () use ($user, $code) {
            $normalized = Str::upper(trim($code));

            $liveCode = LiveCode::query()
                ->redeemable()
                ->where('code', $normalized)
                ->lockForUpdate()
                ->first();

            if (! $liveCode) {
                throw new RuntimeException('Code introuvable ou indisponible.');
            }

            $globalUsage = LiveCodeRedemption::query()
                ->where('live_code_id', $liveCode->id)
                ->lockForUpdate()
                ->count();

            if ($liveCode->usage_limit !== null && $globalUsage >= $liveCode->usage_limit) {
                throw new RuntimeException('Code epuise.');
            }

            $userUsage = LiveCodeRedemption::query()
                ->where('live_code_id', $liveCode->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->count();

            if ($userUsage >= max(1, (int) $liveCode->per_user_limit)) {
                throw new RuntimeException('Code deja utilise.');
            }

            $redemption = LiveCodeRedemption::query()->create([
                'live_code_id' => $liveCode->id,
                'user_id' => $user->id,
                'reward_points' => (int) $liveCode->reward_points,
                'bet_points' => (int) $liveCode->bet_points,
                'xp_reward' => (int) $liveCode->xp_reward,
                'meta' => null,
                'redeemed_at' => now(),
            ]);

            $this->rewardGrantService->grant(
                user: $user,
                domain: 'live_code',
                action: 'redeem',
                dedupeKey: 'live-code.redeem.'.$redemption->id,
                rewards: [
                    'xp' => (int) $liveCode->xp_reward,
                    'points' => (int) $liveCode->reward_points,
                    'bet_points' => (int) $liveCode->bet_points,
                ],
                subjectType: LiveCode::class,
                subjectId: (string) $liveCode->id,
            );

            $this->missionEngine->recordEvent($user, 'live_code.redeem', 1, [
                'event_key' => 'live_code.redeem.'.$redemption->id,
                'subject_type' => LiveCode::class,
                'subject_id' => (string) $liveCode->id,
            ]);
            $this->achievementService->sync($user);
            $this->notifyAction->execute(
                user: $user,
                category: NotificationCategory::LIVE_CODE->value,
                title: 'Code live valide',
                message: 'Le code "'.$liveCode->label.'" a bien ete applique.',
                data: [
                    'live_code_id' => $liveCode->id,
                    'redemption_id' => $redemption->id,
                ],
            );

            $this->storeAuditLogAction->execute(
                action: 'live-codes.redeemed',
                actor: $user,
                target: $liveCode,
                context: [
                    'live_code_id' => $liveCode->id,
                    'redemption_id' => $redemption->id,
                    'reward_points' => (int) $redemption->reward_points,
                    'bet_points' => (int) $redemption->bet_points,
                    'xp_reward' => (int) $redemption->xp_reward,
                ],
            );

            return $redemption;
        });
    }
}
