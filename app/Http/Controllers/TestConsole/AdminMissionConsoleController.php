<?php

namespace App\Http\Controllers\TestConsole;

use App\Application\Actions\Rewards\EnsureCurrentMissionInstancesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Console\StoreMissionTemplateRequest;
use App\Http\Requests\Web\Console\UpdateMissionTemplateRequest;
use App\Models\MissionTemplate;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminMissionConsoleController extends Controller
{
    public function index(): View
    {
        $templates = MissionTemplate::query()
            ->orderByDesc('is_active')
            ->orderBy('scope')
            ->orderBy('key')
            ->paginate(25);

        return view('pages.admin.missions.index', [
            'templates' => $templates,
            'scopes' => MissionTemplate::scopes(),
        ]);
    }

    public function storeTemplate(StoreMissionTemplateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        MissionTemplate::query()->create($this->payloadFromValidated($validated, $request->boolean('is_active')));

        return back()->with('success', 'Template mission cree.');
    }

    public function updateTemplate(UpdateMissionTemplateRequest $request, int $templateId): RedirectResponse
    {
        $template = MissionTemplate::query()->findOrFail($templateId);
        $validated = $request->validated();
        $template->fill($this->payloadFromValidated($validated, $request->boolean('is_active')))->save();

        return back()->with('success', 'Template mission mis a jour.');
    }

    public function destroyTemplate(int $templateId): RedirectResponse
    {
        $template = MissionTemplate::query()->findOrFail($templateId);
        $template->delete();

        return back()->with('success', 'Template mission supprime.');
    }

    public function generateDaily(EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction): RedirectResponse
    {
        $usersCount = $this->generateForUsers($ensureCurrentMissionInstancesAction);

        return back()->with('success', 'Generation daily forcee pour '.$usersCount.' utilisateurs.');
    }

    public function generateWeekly(EnsureCurrentMissionInstancesAction $ensureCurrentMissionInstancesAction): RedirectResponse
    {
        $usersCount = $this->generateForUsers($ensureCurrentMissionInstancesAction);

        return back()->with('success', 'Generation weekly forcee pour '.$usersCount.' utilisateurs.');
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
        return [
            'key' => $validated['key'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'event_type' => $validated['event_type'],
            'target_count' => (int) $validated['target_count'],
            'scope' => $validated['scope'],
            'start_at' => $validated['start_at'] ?? null,
            'end_at' => $validated['end_at'] ?? null,
            'constraints' => $this->decodeJsonOrNull($validated['constraints_json'] ?? null),
            'rewards' => [
                'xp' => (int) ($validated['rewards_xp'] ?? 0),
                'rank_points' => (int) ($validated['rewards_rank_points'] ?? 0),
                'reward_points' => (int) ($validated['rewards_reward_points'] ?? 0),
                'bet_points' => (int) ($validated['rewards_bet_points'] ?? 0),
            ],
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
}

