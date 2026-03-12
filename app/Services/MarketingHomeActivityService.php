<?php

namespace App\Services;

use App\Models\Duel;
use App\Models\EsportMatch;
use App\Models\MissionTemplate;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserMission;
use App\Models\UserProgress;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MarketingHomeActivityService
{
    public function build(?User $user): array
    {
        $publicItems = Cache::remember('marketing.home.activity.public', 60, fn (): array => $this->publicItems());
        $liveMatches = (int) collect($publicItems)
            ->where('type', 'match')
            ->where('status', 'En cours')
            ->count();

        $quickStats = [
            'total_xp' => 0,
            'rank_points' => 0,
            'league_points' => 0,
            'league_name' => null,
            'pending_duels' => 0,
            'active_duels' => 0,
            'active_missions' => 0,
            'available_missions' => 0,
            'unread_notifications' => 0,
            'live_matches' => $liveMatches,
        ];
        $userItems = [];

        if ($user !== null) {
            $payload = Cache::remember('marketing.home.activity.user.'.$user->id, 15, fn (): array => $this->userPayload($user, $liveMatches));
            $quickStats = $payload['quick_stats'];
            $userItems = $payload['activity_items'];
        }

        $items = array_merge($userItems, $publicItems);
        usort($items, static fn (array $a, array $b): int => [$a['priority'], -$a['timestamp']] <=> [$b['priority'], -$b['timestamp']]);

        return [
            'quick_stats' => $quickStats,
            'activity_items' => collect(array_slice(array_map(function (array $item): array {
                unset($item['priority'], $item['timestamp']);
                return $item;
            }, $items), 0, 8)),
        ];
    }

    private function userPayload(User $user, int $liveMatches): array
    {
        $progress = UserProgress::query()->with('league')->find($user->id);
        $duelBase = Duel::query()->forUser((int) $user->id);
        $activeDuels = (clone $duelBase)->where('status', Duel::STATUS_ACCEPTED)->count();
        $pendingDuels = (clone $duelBase)->where('status', Duel::STATUS_PENDING)->count();
        $unreadNotifications = Notification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $missionQuery = UserMission::query()
            ->where('user_id', $user->id)
            ->whereNull('completed_at')
            ->whereHas('instance', fn ($q) => $q->where('period_start', '<=', now())->where('period_end', '>=', now()))
            ->with('instance.template');
        $activeMission = (clone $missionQuery)->latest('updated_at')->first();
        $activeCount = (clone $missionQuery)->count();

        $availableCount = 0;
        if ($activeCount === 0) {
            $availableCount = MissionTemplate::query()
                ->where('is_active', true)
                ->where(fn ($q) => $q->whereNull('start_at')->orWhere('start_at', '<=', now()))
                ->where(fn ($q) => $q->whereNull('end_at')->orWhere('end_at', '>=', now()))
                ->count();
        }

        $quickStats = [
            'total_xp' => (int) ($progress->total_xp ?? 0),
            'rank_points' => (int) ($progress->total_rank_points ?? 0),
            'league_points' => (int) ($progress->total_rank_points ?? 0),
            'league_name' => $progress?->league?->name,
            'pending_duels' => (int) $pendingDuels,
            'active_duels' => (int) $activeDuels,
            'active_missions' => (int) $activeCount,
            'available_missions' => (int) $availableCount,
            'unread_notifications' => (int) $unreadNotifications,
            'live_matches' => $liveMatches,
        ];

        $items = [[
            'type' => 'points',
            'label' => 'Points',
            'title' => 'Ma progression membre',
            'excerpt' => 'XP: '.$quickStats['total_xp'].' - Points classement: '.$quickStats['league_points'],
            'status' => 'Mon rang',
            'image_url' => null,
            'url' => route('app.profile'),
            'date' => $progress?->last_points_at?->toIso8601String(),
            'priority' => 0,
            'timestamp' => $progress?->last_points_at?->timestamp ?? now()->timestamp,
        ]];

        if ($activeDuels > 0) {
            $items[] = $this->item('duel', 'Duel', $activeDuels.' duel(s) actif(s)', 'Vous avez des duels en cours actuellement.', 'En cours', route('app.duels.index', ['status' => 'active']), now(), 1);
        }
        if ($pendingDuels > 0) {
            $items[] = $this->item('duel', 'Duel', $pendingDuels.' duel(s) en attente', 'Des reponses sont attendues sur vos defis.', 'En attente', route('app.duels.index', ['status' => 'pending']), now(), 2);
        }
        if ($activeMission !== null && $activeCount > 0) {
            $template = $activeMission->instance?->template;
            $target = max(1, (int) ($template->target_count ?? 1));
            $done = max(0, min((int) $activeMission->progress_count, $target));
            $items[] = $this->item('mission', 'Mission', $activeCount.' mission(s) en cours', (string) ($template?->title ?? 'Mission active').' - '.$done.'/'.$target, 'En cours', route('app.missions.index'), $activeMission->updated_at, 1);
        } elseif ($availableCount > 0) {
            $items[] = $this->item('mission', 'Mission', 'Missions disponibles', $availableCount.' mission(s) disponible(s) a activer.', 'Disponible', route('app.missions.index'), now(), 3);
        }

        if ($unreadNotifications > 0) {
            $items[] = $this->item(
                'notification',
                'Notification',
                $unreadNotifications.' notification(s) non lue(s)',
                'Mettez a jour vos actions recentes.',
                'A verifier',
                route('app.notifications.index'),
                now(),
                2
            );
        }

        return [
            'quick_stats' => $quickStats,
            'activity_items' => $items,
        ];
    }

    private function publicItems(): array
    {
        $liveMatch = EsportMatch::query()
            ->where('status', EsportMatch::STATUS_LIVE)
            ->orderByDesc('starts_at')
            ->first();
        if ($liveMatch !== null) {
            return [$this->matchItem($liveMatch, 'En cours', 1)];
        }
        $nextMatch = EsportMatch::query()
            ->whereIn('status', [EsportMatch::STATUS_SCHEDULED, EsportMatch::STATUS_LOCKED])
            ->orderBy('starts_at')
            ->first();
        if ($nextMatch !== null) {
            return [$this->matchItem($nextMatch, 'A venir', 4)];
        }
        return [];
    }

    private function matchItem(EsportMatch $match, string $statusLabel, int $priority): array
    {
        $teamA = (string) ($match->team_a_name ?: $match->home_team ?: 'Equipe A');
        $teamB = (string) ($match->team_b_name ?: $match->away_team ?: 'Equipe B');
        return $this->item('match', 'Match', $teamA.' vs '.$teamB, 'Suivez le match du moment sur la plateforme.', $statusLabel, route('app.matches.show', ['matchId' => $match->id]), $match->starts_at, $priority);
    }

    private function item(
        string $type,
        string $label,
        string $title,
        ?string $excerpt,
        ?string $status,
        ?string $url,
        mixed $date,
        int $priority
    ): array {
        $dateValue = $date instanceof Carbon ? $date : ($date ? Carbon::parse((string) $date) : null);

        return [
            'type' => $type,
            'label' => $label,
            'title' => $title,
            'excerpt' => $excerpt,
            'status' => $status,
            'image_url' => null,
            'url' => $url,
            'date' => $dateValue?->toIso8601String(),
            'priority' => $priority,
            'timestamp' => $dateValue?->timestamp ?? now()->timestamp,
        ];
    }
}
