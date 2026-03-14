<?php

namespace Database\Seeders;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Models\MissionTemplate;
use App\Models\User;
use App\Services\ExperienceService;
use App\Services\MissionEngine;
use App\Services\RankService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;

class LaunchMissionCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = collect(require database_path('seeders/data/launch_missions.php'))
            ->map(fn (array $definition): array => $this->normalizeDefinition($definition))
            ->values();

        $this->assertCatalog($catalog);

        foreach ($catalog as $definition) {
            MissionTemplate::query()->updateOrCreate(
                ['key' => $definition['key']],
                $definition,
            );
        }

        MissionTemplate::query()
            ->whereNotIn('key', $catalog->pluck('key')->all())
            ->update([
                'is_active' => false,
                'is_discovery' => false,
                'is_featured' => false,
                'updated_at' => now(),
            ]);

        $this->backfillProgressSignals();
    }

    /**
     * @param array<string, mixed> $definition
     * @return array<string, mixed>
     */
    private function normalizeDefinition(array $definition): array
    {
        return [
            'key' => (string) $definition['key'],
            'title' => (string) $definition['title'],
            'short_description' => $this->nullableString($definition['short_description'] ?? null),
            'description' => $this->nullableString($definition['description'] ?? null),
            'long_description' => $this->nullableString($definition['long_description'] ?? null),
            'category' => $this->nullableString($definition['category'] ?? null),
            'type' => $this->nullableString($definition['type'] ?? null),
            'event_type' => MissionTemplate::normalizeEventType((string) $definition['event_type']),
            'target_count' => max(1, (int) ($definition['target_count'] ?? 1)),
            'scope' => (string) $definition['scope'],
            'difficulty' => $this->nullableString($definition['difficulty'] ?? null),
            'estimated_minutes' => ($definition['estimated_minutes'] ?? null) !== null
                ? max(1, (int) $definition['estimated_minutes'])
                : null,
            'is_discovery' => (bool) ($definition['is_discovery'] ?? false),
            'is_featured' => (bool) ($definition['is_featured'] ?? false),
            'is_repeatable' => (bool) ($definition['is_repeatable'] ?? false),
            'requires_claim' => (bool) ($definition['requires_claim'] ?? false),
            'sort_order' => max(0, (int) ($definition['sort_order'] ?? 0)),
            'start_at' => $this->resolveBoundary($definition['start_at'] ?? null, false),
            'end_at' => $this->resolveBoundary($definition['end_at'] ?? null, true),
            'constraints' => $this->normalizeArray($definition['constraints'] ?? null),
            'rewards' => [
                'xp' => max(0, (int) data_get($definition, 'rewards.xp', 0)),
                'points' => max(0, (int) data_get($definition, 'rewards.points', 0)),
            ],
            'prerequisites' => $this->normalizeArray($definition['prerequisites'] ?? null),
            'icon' => $this->nullableString($definition['icon'] ?? null),
            'badge_label' => $this->nullableString($definition['badge_label'] ?? null),
            'ui_meta' => $this->normalizeArray($definition['ui_meta'] ?? null),
            'is_active' => (bool) ($definition['is_active'] ?? true),
        ];
    }

    /**
     * @param Collection<int, array<string, mixed>> $catalog
     */
    private function assertCatalog(Collection $catalog): void
    {
        if ($catalog->count() !== 50) {
            throw new RuntimeException('Le catalogue de lancement doit contenir exactement 50 missions.');
        }

        $duplicateKeys = $catalog
            ->pluck('key')
            ->duplicates()
            ->unique()
            ->values();

        if ($duplicateKeys->isNotEmpty()) {
            throw new RuntimeException('Cles missions dupliquees: '.$duplicateKeys->implode(', '));
        }
    }

    private function backfillProgressSignals(): void
    {
        /** @var EnsureCurrentMissionInstancesAction $ensureInstances */
        $ensureInstances = app(EnsureCurrentMissionInstancesAction::class);
        /** @var ExperienceService $experienceService */
        $experienceService = app(ExperienceService::class);
        /** @var RankService $rankService */
        $rankService = app(RankService::class);
        /** @var MissionEngine $missionEngine */
        $missionEngine = app(MissionEngine::class);

        User::query()
            ->with('progress')
            ->orderBy('id')
            ->get()
            ->each(function (User $user) use ($ensureInstances, $experienceService, $rankService, $missionEngine): void {
                $ensureInstances->execute($user);

                $totalXp = (int) ($user->progress?->total_xp ?? 0);
                $level = $experienceService->levelForXp($totalXp);
                if ($level >= 5) {
                    $missionEngine->recordEvent($user, 'progress.level.reached', 1, [
                        'event_key' => 'launch.backfill.level.'.$user->id,
                        'level' => $level,
                        'total_xp' => $totalXp,
                        'subject_type' => User::class,
                        'subject_id' => (string) $user->id,
                    ]);
                }

                $rank = $rankService->resolveLeague($totalXp);
                if (in_array((string) ($rank['key'] ?? ''), ['argent', 'gold', 'platine', 'diamant', 'champion', 'erah-prime'], true)) {
                    $missionEngine->recordEvent($user, 'progress.rank.reached', 1, [
                        'event_key' => 'launch.backfill.rank.'.$user->id,
                        'rank_key' => (string) $rank['key'],
                        'rank_name' => (string) ($rank['name'] ?? ''),
                        'total_xp' => $totalXp,
                        'subject_type' => User::class,
                        'subject_id' => (string) $user->id,
                    ]);
                }
            });
    }

    /**
     * @param array<string, mixed>|null $value
     * @return array<string, mixed>|null
     */
    private function normalizeArray(mixed $value): ?array
    {
        return is_array($value) && $value !== [] ? $value : null;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function resolveBoundary(mixed $value, bool $isEnd): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            return Carbon::parse($value);
        }

        if (! is_array($value)) {
            return null;
        }

        $offsetKey = $isEnd ? 'end_offset_days' : 'start_offset_days';
        if (! array_key_exists($offsetKey, $value)) {
            return null;
        }

        $boundary = now()->copy()->startOfDay()->addDays((int) $value[$offsetKey]);

        return $isEnd ? $boundary->endOfDay() : $boundary->startOfDay();
    }
}
