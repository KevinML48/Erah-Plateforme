<?php

namespace App\Services;

use App\Application\Actions\Notifications\NotifyAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\MissionTemplate;
use App\Models\SupporterMonthlyReward;
use App\Models\User;
use App\Models\UserSupportSubscription;
use Illuminate\Support\Carbon;

class GrantMonthlySupporterRewards
{
    public function __construct(
        private readonly SupporterAccessResolver $supporterAccessResolver,
        private readonly EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        private readonly MissionEngine $missionEngine,
        private readonly NotifyAction $notifyAction
    ) {
    }

    public function execute(?Carbon $month = null): int
    {
        $rewardMonth = ($month ?: now())->copy()->startOfMonth();

        $this->supporterAccessResolver->ensureDefaultPlan();
        $this->supporterAccessResolver->ensureCommunityGoals();
        $this->ensureMonthlyMissionTemplate();

        $supporters = User::query()
            ->whereHas('supportSubscriptions', fn ($query) => $query->active())
            ->get();

        foreach ($supporters as $user) {
            $this->grantForUser($user, $rewardMonth);
        }

        $this->supporterAccessResolver->unlockCommunityGoals();

        return $supporters->count();
    }

    private function grantForUser(User $user, Carbon $rewardMonth): void
    {
        $this->supporterAccessResolver->ensurePublicProfile($user);
        $this->ensureCurrentMissionInstancesAction->execute($user);

        $this->firstOrCreateReward($user, $rewardMonth, 'monthly_progress');

        if ($this->supporterAccessResolver->isFoundingSupporter($user)) {
            $this->firstOrCreateReward($user, $rewardMonth, 'badge_founder');
        }

        $months = $this->supporterAccessResolver->supporterMonths($user);
        foreach ((array) config('supporter.loyalty_badges', []) as $threshold => $label) {
            if ($months >= (int) $threshold) {
                $this->firstOrCreateReward($user, $rewardMonth, 'badge_loyalty_'.$threshold);
            }
        }

        $notificationReward = $this->firstOrCreateReward($user, $rewardMonth, 'monthly_notification');

        if ($notificationReward->wasRecentlyCreated) {
            $this->notifyAction->execute(
                user: $user,
                category: NotificationCategory::SYSTEM->value,
                title: 'Avantages supporter du mois',
                message: 'Votre recompense mensuelle supporter est prete. Consultez votre espace Supporter ERAH.',
                data: [
                    'reward_month' => $rewardMonth->toDateString(),
                    'supporter_status' => UserSupportSubscription::STATUS_ACTIVE,
                ],
            );
        }

        $eventType = MissionTemplate::normalizeEventType(
            (string) config('supporter.monthly_reward.event_type', 'supporter.monthly')
        );
        $eventDate = $rewardMonth->toDateString();
        $this->missionEngine->recordEvent($user, $eventType, 1, [
            'event_key' => 'supporter.monthly.'.$user->id.'.'.$eventDate,
            'date' => $eventDate,
            'supporter_monthly' => true,
            'subject_type' => User::class,
            'subject_id' => (string) $user->id,
        ]);
    }

    private function ensureMonthlyMissionTemplate(): MissionTemplate
    {
        $config = (array) config('supporter.monthly_reward', []);

        return MissionTemplate::query()->updateOrCreate(
            ['key' => (string) ($config['mission_key_prefix'] ?? 'supporter-monthly')],
            [
                'title' => (string) ($config['mission_title'] ?? 'Mission supporter mensuelle'),
                'description' => (string) ($config['mission_description'] ?? 'Mission supporter mensuelle.'),
                'event_type' => MissionTemplate::normalizeEventType((string) ($config['event_type'] ?? 'supporter.monthly')),
                'target_count' => (int) ($config['target_count'] ?? 1),
                'scope' => MissionTemplate::SCOPE_MONTHLY,
                'constraints' => [
                    'supporter_only' => true,
                    'supporter_monthly' => true,
                ],
                'rewards' => [
                    'xp' => (int) ($config['xp_bonus'] ?? 0),
                    'points' => (int) ($config['reward_points_bonus'] ?? 0),
                ],
                'is_active' => true,
            ]
        );
    }

    private function firstOrCreateReward(User $user, Carbon $rewardMonth, string $rewardKey): SupporterMonthlyReward
    {
        $rewardDate = $rewardMonth->copy()->startOfMonth()->toDateString();

        $existing = SupporterMonthlyReward::query()
            ->where('user_id', $user->id)
            ->where('reward_key', $rewardKey)
            ->whereDate('reward_month', $rewardDate)
            ->first();

        if ($existing) {
            return $existing;
        }

        return SupporterMonthlyReward::query()->create([
            'user_id' => $user->id,
            'reward_month' => $rewardDate,
            'reward_key' => $rewardKey,
            'granted_at' => now(),
        ]);
    }
}
