<?php

namespace App\Services;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Models\UserMission;
use App\Models\UserMissionFocus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MissionFocusService
{
    public const MAX_FOCUS_MISSIONS = 3;

    public function __construct(
        private readonly EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        private readonly MissionEngine $missionEngine,
    ) {
    }

    /**
     * @return Collection<int, UserMissionFocus>
     */
    public function forUser(User $user): Collection
    {
        $this->ensureCurrentMissionInstancesAction->execute($user);
        $this->pruneUnavailable($user);

        return UserMissionFocus::query()
            ->where('user_id', $user->id)
            ->with('template')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function add(User $user, MissionTemplate $template): UserMissionFocus
    {
        $createdFocus = null;

        $focus = DB::transaction(function () use ($user, $template, &$createdFocus) {
            $this->ensureCurrentMissionInstancesAction->execute($user);
            $this->pruneUnavailable($user);

            if (! $this->isAvailableForUser($user, $template)) {
                throw new RuntimeException("Cette mission n'est pas disponible en focus pour le moment.");
            }

            $current = UserMissionFocus::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->orderBy('sort_order')
                ->get();

            $existing = $current->firstWhere('mission_template_id', $template->id);
            if ($existing) {
                return $existing;
            }

            if ($current->count() >= self::MAX_FOCUS_MISSIONS) {
                throw new RuntimeException('Vous pouvez garder seulement 3 missions en focus.');
            }

            $createdFocus = UserMissionFocus::query()->create([
                'user_id' => $user->id,
                'mission_template_id' => $template->id,
                'sort_order' => $this->nextSortOrder($current),
            ]);

            return $createdFocus;
        });

        if ($createdFocus instanceof UserMissionFocus) {
            $this->missionEngine->recordEvent($user, 'mission.focus.added', 1, [
                'event_key' => 'mission.focus.added.'.$user->id.'.'.$template->id,
                'subject_type' => MissionTemplate::class,
                'subject_id' => (string) $template->id,
                'mission_template_id' => $template->id,
            ]);
        }

        return $focus;
    }

    public function remove(User $user, MissionTemplate $template): void
    {
        DB::transaction(function () use ($user, $template) {
            UserMissionFocus::query()
                ->where('user_id', $user->id)
                ->where('mission_template_id', $template->id)
                ->delete();

            $this->normalizeSortOrder($user);
        });
    }

    public function pruneUnavailable(User $user): int
    {
        $activeTemplateIds = UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance', fn ($query) => $query
                ->where('period_start', '<=', now())
                ->where('period_end', '>=', now()))
            ->with('instance:id,mission_template_id')
            ->get()
            ->pluck('instance.mission_template_id')
            ->filter()
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        $query = UserMissionFocus::query()->where('user_id', $user->id);
        if ($activeTemplateIds !== []) {
            $query->whereNotIn('mission_template_id', $activeTemplateIds);
        }

        $deleted = $query->delete();

        if ($deleted > 0) {
            $this->normalizeSortOrder($user);
        }

        return $deleted;
    }

    private function nextSortOrder(Collection $current): int
    {
        $max = (int) $current->max('sort_order');

        return max(1, $max + 1);
    }

    private function normalizeSortOrder(User $user): void
    {
        UserMissionFocus::query()
            ->where('user_id', $user->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values()
            ->each(function (UserMissionFocus $focus, int $index): void {
                $focus->sort_order = $index + 1;
                $focus->save();
            });
    }

    private function isAvailableForUser(User $user, MissionTemplate $template): bool
    {
        return UserMission::query()
            ->where('user_id', $user->id)
            ->whereHas('instance', fn ($query) => $query
                ->where('period_start', '<=', now())
                ->where('period_end', '>=', now())
                ->where('mission_template_id', $template->id))
            ->exists();
    }
}
