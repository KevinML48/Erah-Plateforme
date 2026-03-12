<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Console\StoreMissionTemplateRequest;
use App\Http\Requests\Web\Console\UpdateMissionTemplateRequest;
use App\Models\LiveCode;
use App\Models\LiveCodeRedemption;
use App\Models\MissionCompletion;
use App\Models\MissionTemplate;
use App\Models\PlatformEvent;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\MissionMaintenanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMissionController extends Controller
{
    public function index(Request $request): View
    {
        $scope = (string) $request->query('scope', 'all');
        $status = (string) $request->query('status', 'all');
        $category = trim((string) $request->query('category', ''));
        $difficulty = trim((string) $request->query('difficulty', ''));

        $templates = MissionTemplate::query()
            ->when($scope !== 'all', fn ($query) => $query->where('scope', $scope))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($difficulty !== '', fn ($query) => $query->where('difficulty', $difficulty))
            ->orderByDesc('is_active')
            ->orderByDesc('is_discovery')
            ->orderBy('sort_order')
            ->orderBy('scope')
            ->orderBy('key')
            ->paginate(25)
            ->withQueryString();

        $activeTemplatesCount = MissionTemplate::query()->where('is_active', true)->count();
        $activeDailyCount = MissionTemplate::query()->where('is_active', true)->where('scope', MissionTemplate::SCOPE_DAILY)->count();
        $activeQuizCount = Quiz::query()->where('is_active', true)->count();
        $activeLiveCodeCount = LiveCode::query()->where('status', 'published')->count();
        $activeEventCount = PlatformEvent::query()->activeWindow()->count();
        $missionsCompletedToday = MissionCompletion::query()->whereDate('completed_at', now()->toDateString())->count();
        $quizAttemptsToday = QuizAttempt::query()->whereDate('finished_at', now()->toDateString())->count();
        $liveCodeRedemptionsToday = LiveCodeRedemption::query()->whereDate('redeemed_at', now()->toDateString())->count();
        $pendingValidationCount = 0;

        return view('pages.admin.missions.index', [
            'templates' => $templates,
            'scopes' => MissionTemplate::scopes(),
            'filters' => [
                'scope' => $scope,
                'status' => $status,
                'category' => $category,
                'difficulty' => $difficulty,
            ],
            'categories' => MissionTemplate::query()
                ->whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
            'quizzes' => Quiz::query()->withCount('attempts')->latest('id')->limit(8)->get(),
            'liveCodes' => LiveCode::query()->withCount('redemptions')->latest('id')->limit(8)->get(),
            'events' => PlatformEvent::query()->latest('id')->limit(8)->get(),
            'overview' => [
                'active_templates' => $activeTemplatesCount,
                'active_daily_templates' => $activeDailyCount,
                'active_quizzes' => $activeQuizCount,
                'active_live_codes' => $activeLiveCodeCount,
                'active_events' => $activeEventCount,
                'missions_completed_today' => $missionsCompletedToday,
                'quiz_attempts_today' => $quizAttemptsToday,
                'live_code_redemptions_today' => $liveCodeRedemptionsToday,
                'pending_validation' => $pendingValidationCount,
            ],
        ]);
    }

    public function storeTemplate(
        StoreMissionTemplateRequest $request,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $validated = $request->validated();
        $template = MissionTemplate::query()->create($this->payloadFromValidated($validated, $request->boolean('is_active')));

        $storeAuditLogAction->execute(
            action: 'missions.template.created',
            actor: $request->user(),
            target: $template,
            context: [
                'mission_template_id' => $template->id,
                'scope' => $template->scope,
                'event_type' => $template->event_type,
            ],
        );

        return back()->with('success', 'Template mission cree.');
    }

    public function updateTemplate(
        UpdateMissionTemplateRequest $request,
        int $templateId,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $template = MissionTemplate::query()->findOrFail($templateId);
        $validated = $request->validated();
        $template->fill($this->payloadFromValidated($validated, $request->boolean('is_active')))->save();

        $storeAuditLogAction->execute(
            action: 'missions.template.updated',
            actor: $request->user(),
            target: $template,
            context: [
                'mission_template_id' => $template->id,
                'scope' => $template->scope,
                'event_type' => $template->event_type,
            ],
        );

        return back()->with('success', 'Template mission mis a jour.');
    }

    public function destroyTemplate(int $templateId, StoreAuditLogAction $storeAuditLogAction): RedirectResponse
    {
        $template = MissionTemplate::query()->findOrFail($templateId);
        $storeAuditLogAction->execute(
            action: 'missions.template.deleted',
            actor: request()->user(),
            target: $template,
            context: [
                'mission_template_id' => $template->id,
                'scope' => $template->scope,
                'event_type' => $template->event_type,
            ],
        );
        $template->delete();

        return back()->with('success', 'Template mission supprime.');
    }

    public function generateDaily(
        EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $usersCount = $this->generateForUsers($ensureCurrentMissionInstancesAction);

        $storeAuditLogAction->execute(
            action: 'missions.generation.daily',
            actor: request()->user(),
            context: ['users_count' => $usersCount],
        );

        return back()->with('success', 'Generation daily forcee pour '.$usersCount.' utilisateurs.');
    }

    public function generateWeekly(
        EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $usersCount = $this->generateForUsers($ensureCurrentMissionInstancesAction);

        $storeAuditLogAction->execute(
            action: 'missions.generation.weekly',
            actor: request()->user(),
            context: ['users_count' => $usersCount],
        );

        return back()->with('success', 'Generation weekly forcee pour '.$usersCount.' utilisateurs.');
    }

    public function generateEventWindow(
        EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $usersCount = $this->generateForUsers($ensureCurrentMissionInstancesAction);

        $storeAuditLogAction->execute(
            action: 'missions.generation.event_window',
            actor: request()->user(),
            context: ['users_count' => $usersCount],
        );

        return back()->with('success', 'Generation event_window forcee pour '.$usersCount.' utilisateurs.');
    }

    public function repair(
        MissionMaintenanceService $missionMaintenanceService,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $result = ['users' => 0, 'expired_marked' => 0, 'pruned_focuses' => 0];

        User::query()->orderBy('id')->chunkById(200, function (Collection $users) use ($missionMaintenanceService, &$result): void {
            $chunkResult = $missionMaintenanceService->repairMany($users);
            $result['users'] += $chunkResult['users'];
            $result['expired_marked'] += $chunkResult['expired_marked'];
            $result['pruned_focuses'] += $chunkResult['pruned_focuses'];
        });

        $storeAuditLogAction->execute(
            action: 'missions.repair.run',
            actor: request()->user(),
            context: $result,
        );

        return back()->with(
            'success',
            'Reparation missions terminee pour '.$result['users'].' utilisateurs. '
            .$result['expired_marked'].' mission(s) expiree(s) marquee(s), '
            .$result['pruned_focuses'].' focus nettoye(s).'
        );
    }

    private function generateForUsers(EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction): int
    {
        $total = 0;

        User::query()->orderBy('id')->chunkById(200, function (Collection $users) use ($ensureCurrentMissionInstancesAction, &$total): void {
            foreach ($users as $user) {
                $ensureCurrentMissionInstancesAction->execute($user);
                $total++;
            }
        });

        return $total;
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function payloadFromValidated(array $validated, bool $isActive): array
    {
        $constraints = $this->mergeDifficultyConstraint(
            $this->decodeJsonOrNull($validated['constraints_json'] ?? null),
            $validated['difficulty'] ?? null,
        );

        return [
            'key' => $validated['key'],
            'title' => $validated['title'],
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'long_description' => $validated['long_description'] ?? ($validated['description'] ?? null),
            'category' => $validated['category'] ?? $this->resolveCategory((string) $validated['event_type']),
            'type' => $validated['type'] ?? $this->resolveType((string) $validated['scope']),
            'event_type' => MissionTemplate::normalizeEventType((string) $validated['event_type']),
            'target_count' => (int) $validated['target_count'],
            'scope' => $validated['scope'],
            'difficulty' => $validated['difficulty'] ?? null,
            'estimated_minutes' => $validated['estimated_minutes'] ?? null,
            'is_discovery' => (bool) ($validated['is_discovery'] ?? false),
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
            'is_repeatable' => (bool) ($validated['is_repeatable'] ?? in_array($validated['scope'], [
                MissionTemplate::SCOPE_DAILY,
                MissionTemplate::SCOPE_WEEKLY,
                MissionTemplate::SCOPE_MONTHLY,
            ], true)),
            'requires_claim' => (bool) ($validated['requires_claim'] ?? false),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'start_at' => $validated['start_at'] ?? null,
            'end_at' => $validated['end_at'] ?? null,
            'constraints' => $constraints,
            'rewards' => [
                'xp' => (int) ($validated['rewards_xp'] ?? 0),
                'points' => (int) ($validated['rewards_points'] ?? 0),
            ],
            'prerequisites' => $this->decodeJsonOrNull($validated['prerequisites_json'] ?? null),
            'icon' => $validated['icon'] ?? null,
            'badge_label' => $validated['badge_label'] ?? null,
            'ui_meta' => array_filter([
                'source' => 'admin',
                'featured' => (bool) ($validated['is_featured'] ?? false),
            ] + ($this->decodeJsonOrNull($validated['ui_meta_json'] ?? null) ?? []), fn (mixed $value): bool => $value !== null),
            'is_active' => $isActive,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonOrNull(?string $payload): ?array
    {
        if ($payload === null || trim($payload) === '') {
            return null;
        }

        $decoded = json_decode($payload, true);
        if (! is_array($decoded)) {
            return null;
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed>|null $constraints
     * @return array<string, mixed>|null
     */
    private function mergeDifficultyConstraint(?array $constraints, ?string $difficulty): ?array
    {
        $payload = $constraints ?? [];

        if ($difficulty !== null && $difficulty !== '') {
            $payload['difficulty'] = $difficulty;
        }

        return $payload === [] ? null : $payload;
    }

    private function resolveCategory(string $eventType): string
    {
        return (string) str($eventType)->before('.')->replace('_', '-')->value();
    }

    private function resolveType(string $scope): string
    {
        return match ($scope) {
            MissionTemplate::SCOPE_EVENT_WINDOW => 'event',
            MissionTemplate::SCOPE_ONCE => 'core',
            default => 'repeatable',
        };
    }
}
