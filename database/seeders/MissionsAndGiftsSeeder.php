<?php

namespace Database\Seeders;

use App\Application\Actions\Rewards\ApplyRewardWalletTransactionAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\MissionCompletion;
use App\Models\MissionTemplate;
use App\Models\RewardWalletTransaction;
use App\Models\User;
use App\Models\UserMission;
use App\Models\UserRewardWallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MissionsAndGiftsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(LaunchMissionCatalogSeeder::class);
        $this->call(LaunchGiftCatalogSeeder::class);
        $this->seedWalletsAndProgress();
    }

    private function seedWalletsAndProgress(): void
    {
        $applyRewardWalletTransactionAction = app(ApplyRewardWalletTransactionAction::class);
        $ensureCurrentMissionInstancesAction = app(EnsureCurrentMissionInstancesAction::class);

        $users = User::query()
            ->where('role', User::ROLE_USER)
            ->orderBy('id')
            ->get();

        foreach ($users as $index => $user) {
            UserRewardWallet::query()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );

            $grantAmount = match ($index) {
                0 => 1800,
                1 => 1200,
                2 => 850,
                default => 300,
            };

            $applyRewardWalletTransactionAction->execute(
                user: $user,
                type: RewardWalletTransaction::TYPE_GRANT,
                amount: $grantAmount,
                uniqueKey: 'seed.reward_wallet.grant.user.'.$user->id.'.v2',
                refType: RewardWalletTransaction::REF_TYPE_SYSTEM,
                refId: 'missions-gifts-seeder',
                metadata: ['seed' => self::class],
                initialBalanceIfMissing: 0
            );

            $ensureCurrentMissionInstancesAction->execute($user);
        }

        $firstUser = $users->first();
        if (! $firstUser) {
            return;
        }

        $this->seedMissionProgressForUser($firstUser, 'launch.profile-operational', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'launch.first-duel', 0, false);
        $this->seedMissionProgressForUser($firstUser, 'launch.community-voice', 1, false);
        $this->seedMissionProgressForUser($firstUser, 'launch.bet-prono-valid', 1, true);
        $this->seedMissionProgressForUser($firstUser, 'launch.bet-read-the-game', 1, false);
        $this->seedMissionProgressForUser($firstUser, 'launch.duelist-regular', 2, false);
        $this->seedMissionProgressForUser($firstUser, 'launch.community-pulse', 2, false);
        $this->seedMissionProgressForUser($firstUser, 'launch.live-community-challenge', 2, false);
    }

    private function seedMissionProgressForUser(User $user, string $templateKey, int $progressCount, bool $completed): void
    {
        $template = MissionTemplate::query()->where('key', $templateKey)->first();
        if (! $template) {
            return;
        }

        $periodStart = match ($template->scope) {
            MissionTemplate::SCOPE_DAILY => now()->startOfDay(),
            MissionTemplate::SCOPE_WEEKLY => now()->startOfWeek(),
            MissionTemplate::SCOPE_ONCE => $template->start_at?->copy()->startOfDay() ?? Carbon::create(2020, 1, 1, 0, 0, 0),
            MissionTemplate::SCOPE_EVENT_WINDOW => $template->start_at?->copy() ?? now()->startOfDay(),
            default => now()->startOfDay(),
        };

        $userMission = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance', function ($query) use ($template, $periodStart): void {
                $query->where('mission_template_id', $template->id)
                    ->whereDate('period_start', $periodStart->toDateString());
            })
            ->with('instance')
            ->first();

        if (! $userMission) {
            return;
        }

        $userMission->progress_count = min($progressCount, (int) $template->target_count);
        $userMission->completed_at = $completed ? now() : null;
        $userMission->rewarded_at = $completed && ! $template->requires_claim ? now() : null;
        $userMission->claimed_at = $completed && ! $template->requires_claim ? now() : null;
        $userMission->save();

        if ($completed) {
            MissionCompletion::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'user_mission_id' => $userMission->id,
                ],
                [
                    'completed_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
