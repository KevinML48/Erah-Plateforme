<?php

namespace Database\Seeders;

use App\Application\Actions\Rewards\ApplyRewardWalletTransactionAction;
use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\Gift;
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
        $this->seedGifts();
        $this->seedWalletsAndProgress();
    }

    private function seedGifts(): void
    {
        $gifts = [
            ['title' => 'T-shirt ERAH', 'description' => 'T-shirt officiel ERAH Edition Community.', 'image_url' => null, 'cost_points' => 1000, 'stock' => 10, 'is_active' => true],
            ['title' => 'Mug ERAH', 'description' => 'Mug premium pour les sessions VOD.', 'image_url' => null, 'cost_points' => 600, 'stock' => 20, 'is_active' => true],
            ['title' => 'Ticket event', 'description' => 'Ticket pour un event communautaire ERAH.', 'image_url' => null, 'cost_points' => 2000, 'stock' => 5, 'is_active' => true],
            ['title' => 'Hoodie ERAH', 'description' => 'Hoodie noir ERAH pour les sessions LAN.', 'image_url' => null, 'cost_points' => 1400, 'stock' => 8, 'is_active' => true],
            ['title' => 'Casquette ERAH', 'description' => 'Casquette officielle ERAH edition club.', 'image_url' => null, 'cost_points' => 500, 'stock' => 18, 'is_active' => true],
            ['title' => 'Mousepad Pro ERAH', 'description' => 'Grand mousepad competition, surface rapide.', 'image_url' => null, 'cost_points' => 750, 'stock' => 15, 'is_active' => true],
            ['title' => 'Maillot ERAH 2026', 'description' => 'Maillot officiel saison 2026.', 'image_url' => null, 'cost_points' => 1600, 'stock' => 10, 'is_active' => true],
            ['title' => 'Poster Team ERAH', 'description' => 'Poster A2 de la line-up ERAH.', 'image_url' => null, 'cost_points' => 250, 'stock' => 30, 'is_active' => true],
            ['title' => 'Sticker Pack ERAH', 'description' => 'Pack de stickers officiels ERAH.', 'image_url' => null, 'cost_points' => 180, 'stock' => 40, 'is_active' => true],
            ['title' => 'VIP Viewing Pass', 'description' => 'Acces VIP pour suivre un match avec le staff.', 'image_url' => null, 'cost_points' => 2200, 'stock' => 4, 'is_active' => true],
            ['title' => 'Coaching Session 30m', 'description' => 'Session coaching strategique de 30 minutes.', 'image_url' => null, 'cost_points' => 1750, 'stock' => 6, 'is_active' => true],
            ['title' => 'Discord Role Elite', 'description' => 'Role premium sur le Discord ERAH.', 'image_url' => null, 'cost_points' => 450, 'stock' => 999, 'is_active' => true],
            ['title' => 'Nameplate Profile Gold', 'description' => 'Badge profile gold pour la plateforme.', 'image_url' => null, 'cost_points' => 700, 'stock' => 999, 'is_active' => true],
            ['title' => 'Bootcamp Access 1 Day', 'description' => 'Invite a un bootcamp ERAH sur 1 jour.', 'image_url' => null, 'cost_points' => 2600, 'stock' => 3, 'is_active' => true],
            ['title' => 'Collector Pin ERAH', 'description' => 'Pin metal collector edition ERAH.', 'image_url' => null, 'cost_points' => 320, 'stock' => 22, 'is_active' => true],
        ];

        foreach ($gifts as $gift) {
            Gift::query()->updateOrCreate(['title' => $gift['title']], $gift);
        }
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
