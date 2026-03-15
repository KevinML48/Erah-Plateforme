<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Bets\SettleMatchBetsAction;
use App\Application\Actions\Matches\CreateMatchAction;
use App\Application\Actions\Matches\SetMatchResultAction;
use App\Application\Actions\Matches\UnlockTournamentChildMatchesAction;
use App\Application\Actions\Matches\UpdateMatchAction;
use App\Application\Actions\Matches\UpdateMatchStatusAction;
use App\Domain\Betting\Support\MatchMarketCatalog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\SetAdminMatchResultRequest;
use App\Http\Requests\Web\Admin\SettleAdminMatchRequest;
use App\Http\Requests\Web\Admin\StoreAdminMatchRequest;
use App\Http\Requests\Web\Admin\UpdateAdminMatchRequest;
use App\Http\Requests\Web\Admin\UpdateAdminMatchStatusRequest;
use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class AdminMatchController extends Controller
{
    public function index(Request $request, MatchMarketCatalog $matchMarketCatalog): View
    {
        $status = blank($request->query('status')) ? 'all' : strtolower(trim((string) $request->query('status')));
        $game = blank($request->query('game')) ? 'all' : strtolower(trim((string) $request->query('game')));
        $eventType = blank($request->query('event_type')) ? 'all' : strtolower(trim((string) $request->query('event_type')));
        $search = trim((string) $request->query('q', ''));

        $matches = EsportMatch::query()
            ->with([
                'parentMatch:id,event_name,compétition_name',
                'settlement:id,match_id,result,processused_at,won_count,lost_count,void_count',
            ])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($game !== 'all', fn ($query) => $query->where('game_key', $game))
            ->when($eventType !== 'all', fn ($query) => $query->where('event_type', $eventType))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('event_name', 'like', '%'.$search.'%')
                        ->orWhere('compétition_name', 'like', '%'.$search.'%')
                        ->orWhere('compétition_stage', 'like', '%'.$search.'%')
                        ->orWhere('compétition_split', 'like', '%'.$search.'%')
                        ->orWhere('team_a_name', 'like', '%'.$search.'%')
                        ->orWhere('team_b_name', 'like', '%'.$search.'%')
                        ->orWhere('home_team', 'like', '%'.$search.'%')
                        ->orWhere('away_team', 'like', '%'.$search.'%');
                });
            })
            ->orderByRaw("case when event_type = ? then 0 else 1 end", [EsportMatch::EVENT_TYPE_TOURNAMENT_RUN])
            ->orderByDesc('starts_at')
            ->paginate(20)
            ->withQueryString();

        $matches->getCollection()->loadCount(['bets', 'childMatches']);

        $statsBaseQuery = EsportMatch::query()
            ->when($game !== 'all', fn ($query) => $query->where('game_key', $game))
            ->when($eventType !== 'all', fn ($query) => $query->where('event_type', $eventType))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('event_name', 'like', '%'.$search.'%')
                        ->orWhere('compétition_name', 'like', '%'.$search.'%')
                        ->orWhere('team_a_name', 'like', '%'.$search.'%')
                        ->orWhere('team_b_name', 'like', '%'.$search.'%')
                        ->orWhere('home_team', 'like', '%'.$search.'%')
                        ->orWhere('away_team', 'like', '%'.$search.'%');
                });
            });

        return view('pages.admin.matches.index', [
            'matches' => $matches,
            'status' => $status,
            'game' => $game,
            'eventType' => $eventType,
            'search' => $search,
            'statuses' => EsportMatch::statuses(),
            'statusOptions' => $matchMarketCatalog->statusOptions(),
            'gameOptions' => $matchMarketCatalog->gameOptions(),
            'eventTypeOptions' => $matchMarketCatalog->eventTypeOptions(),
            'matchLabelResolver' => $matchMarketCatalog,
            'stats' => [
                'total' => (clone $statsBaseQuery)->count(),
                'tournaments' => (clone $statsBaseQuery)->where('event_type', EsportMatch::EVENT_TYPE_TOURNAMENT_RUN)->count(),
                'rocket_league_matches' => (clone $statsBaseQuery)
                    ->where('game_key', EsportMatch::GAME_ROCKET_LEAGUE)
                    ->where('event_type', EsportMatch::EVENT_TYPE_HEAD_TO_HEAD)
                    ->count(),
                'scheduled' => (clone $statsBaseQuery)->where('status', EsportMatch::STATUS_SCHEDULED)->count(),
            ],
        ]);
    }

    public function create(Request $request, MatchMarketCatalog $matchMarketCatalog): View
    {
        $parentMatch = null;
        if ($request->filled('parent_match_id')) {
            $parentMatch = EsportMatch::query()->findOrFail((int) $request->query('parent_match_id'));
        }

        $formState = $this->buildFormState(
            match: null,
            request: $request,
            matchMarketCatalog: $matchMarketCatalog,
            parentMatch: $parentMatch,
        );

        return view('pages.admin.matches.form', [
            'match' => null,
            'parentMatch' => $parentMatch,
            'action' => route('admin.matches.store'),
            'method' => 'POST',
            ...$formState,
        ]);
    }

    public function store(StoreAdminMatchRequest $request, CreateMatchAction $createMatchAction): RedirectResponse
    {
        $validated = $request->validated();
        $matchKey = 'mch-'.now()->format('Ymd-His').'-'.Str::lower(substr((string) Str::uuid(), 0, 8));

        try {
            $match = $createMatchAction->execute(
                actor: $request->user(),
                payload: array_merge($validated, [
                    'match_key' => $matchKey,
                ]),
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.matches.manage', $match->id)
            ->with('success', 'Evenement esport cree avec succes.');
    }

    public function edit(int $matchId, Request $request, MatchMarketCatalog $matchMarketCatalog): View
    {
        $match = EsportMatch::query()
            ->with([
                'parentMatch',
                'markets' => fn ($query) => $query->with('selections'),
            ])
            ->findOrFail($matchId);

        $formState = $this->buildFormState(
            match: $match,
            request: $request,
            matchMarketCatalog: $matchMarketCatalog,
            parentMatch: $match->parentMatch,
        );

        return view('pages.admin.matches.form', [
            'match' => $match,
            'parentMatch' => $match->parentMatch,
            'action' => route('admin.matches.update', $match->id),
            'method' => 'PUT',
            ...$formState,
        ]);
    }

    public function update(
        UpdateAdminMatchRequest $request,
        int $matchId,
        UpdateMatchAction $updateMatchAction
    ): RedirectResponse {
        $match = EsportMatch::query()->findOrFail($matchId);

        try {
            $updateMatchAction->execute(
                actor: $request->user(),
                match: $match,
                payload: $request->validated(),
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.matches.manage', $matchId)
            ->with('success', 'Evenement esport mis a jour.');
    }

    public function manage(int $matchId, MatchMarketCatalog $matchMarketCatalog): View
    {
        $match = EsportMatch::query()
            ->with([
                'parentMatch',
                'childMatches' => fn ($query) => $query->withCount('bets')->orderBy('starts_at'),
                'markets' => fn ($query) => $query->with('selections'),
                'settlement:id,match_id,idempotency_key,result,bets_total,won_count,lost_count,void_count,payout_total,processused_at,meta',
                'bets' => fn ($query) => $query->with('user:id,name')->latest('id')->limit(50),
            ])
            ->findOrFail($matchId);

        $match->loadCount(['bets', 'childMatches']);

        return view('pages.admin.matches.manage', [
            'match' => $match,
            'statuses' => EsportMatch::statuses(),
            'statusOptions' => $matchMarketCatalog->statusOptions(),
            'gameLabel' => $matchMarketCatalog->labelForGame($match->game_key),
            'eventTypeLabel' => $matchMarketCatalog->labelForEventType($match->event_type),
            'matchLabelResolver' => $matchMarketCatalog,
            'resultOptions' => $this->resultOptionsFor($match),
            'marketPresetOptions' => $matchMarketCatalog->presetOptions(),
            'createChildDefaults' => [
                'event_type' => EsportMatch::EVENT_TYPE_HEAD_TO_HEAD,
                'game_key' => $match->game_key,
                'parent_match_id' => $match->id,
                'best_of' => 5,
                'market_preset' => 'rocket_league_bo5',
            ],
        ]);
    }

    public function updateStatus(
        UpdateAdminMatchStatusRequest $request,
        int $matchId,
        UpdateMatchStatusAction $updateMatchStatusAction
    ): RedirectResponse {
        $match = EsportMatch::query()->findOrFail($matchId);

        try {
            $updateMatchStatusAction->execute(
                actor: $request->user(),
                match: $match,
                status: (string) $request->validated()['status'],
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Statut mis a jour.');
    }

    public function setResult(
        SetAdminMatchResultRequest $request,
        int $matchId,
        SetMatchResultAction $setMatchResultAction
    ): RedirectResponse {
        $match = EsportMatch::query()->findOrFail($matchId);
        $validated = $request->validated();

        try {
            $setMatchResultAction->execute(
                actor: $request->user(),
                match: $match,
                result: (string) $validated['result'],
                teamAScore: $validated['team_a_score'] ?? null,
                teamBScore: $validated['team_b_score'] ?? null,
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Resultat mis a jour.');
    }

    public function settle(
        SettleAdminMatchRequest $request,
        int $matchId,
        SettleMatchBetsAction $settleMatchBetsAction
    ): RedirectResponse {
        $validated = $request->validated();

        try {
            $result = $settleMatchBetsAction->execute(
                actor: $request->user(),
                matchId: $matchId,
                result: (string) $validated['result'],
                idempotencyKey: (string) $validated['idempotency_key'],
                teamAScore: $validated['team_a_score'] ?? null,
                teamBScore: $validated['team_b_score'] ?? null,
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        if ($result['idempotent']) {
            return back()->with('success', 'Settlement deja applique (replay idempotent).');
        }

        return back()->with('success', 'Settlement execute.');
    }

    public function unlockChildMatches(
        int $matchId,
        UnlockTournamentChildMatchesAction $unlockTournamentChildMatchesAction,
    ): RedirectResponse {
        $match = EsportMatch::query()->findOrFail($matchId);

        try {
            $unlockTournamentChildMatchesAction->execute(
                actor: request()->user(),
                match: $match,
            );
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'La phase matchs Rocket League est maintenant debloquee.');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFormState(
        ?EsportMatch $match,
        Request $request,
        MatchMarketCatalog $matchMarketCatalog,
        ?EsportMatch $parentMatch
    ): array {
        $context = [
            'event_type' => old('event_type', $match?->event_type ?? ($parentMatch ? EsportMatch::EVENT_TYPE_HEAD_TO_HEAD : (string) $request->query('event_type', EsportMatch::EVENT_TYPE_HEAD_TO_HEAD))),
            'game_key' => old('game_key', $match?->game_key ?? $parentMatch?->game_key ?? (string) $request->query('game_key', EsportMatch::GAME_VALORANT)),
            'event_name' => old('event_name', $match?->event_name),
            'compétition_name' => old('compétition_name', $match?->compétition_name ?? $parentMatch?->compétition_name),
            'compétition_stage' => old('compétition_stage', $match?->compétition_stage ?? $parentMatch?->compétition_stage),
            'compétition_split' => old('compétition_split', $match?->compétition_split ?? $parentMatch?->compétition_split),
            'best_of' => old('best_of', $match?->best_of ?? ($parentMatch ? 5 : null)),
            'team_a_name' => old('team_a_name', $match?->team_a_name),
            'team_b_name' => old('team_b_name', $match?->team_b_name),
            'home_team' => old('team_a_name', $match?->team_a_name ?? $match?->home_team),
            'away_team' => old('team_b_name', $match?->team_b_name ?? $match?->away_team),
            'parent_match_id' => old('parent_match_id', $match?->parent_match_id ?? $parentMatch?->id ?? $request->query('parent_match_id')),
        ];

        $marketPreset = old('market_preset', $request->query('market_preset', $matchMarketCatalog->defaultPresetKeyFor($context)));
        $rawMarkets = old('markets');

        if (! is_array($rawMarkets) && $match) {
            $rawMarkets = $this->serializeMarketsForForm($match);
        }

        $markets = $matchMarketCatalog->normalizeSubmittedMarkets(
            is_array($rawMarkets) ? $rawMarkets : null,
            array_merge($context, ['market_preset' => $marketPreset]),
        );

        $presetDefinitions = [];
        foreach (array_keys($matchMarketCatalog->presetOptions()) as $presetKey) {
            $presetDefinitions[$presetKey] = $matchMarketCatalog->buildMarketsFromPreset($presetKey, $context);
        }

        return [
            'eventTypeOptions' => $matchMarketCatalog->eventTypeOptions(),
            'gameOptions' => $matchMarketCatalog->gameOptions(),
            'bestOfOptions' => $matchMarketCatalog->bestOfOptions(),
            'marketPresetOptions' => $matchMarketCatalog->presetOptions(),
            'marketPreset' => $marketPreset,
            'marketRows' => $markets,
            'presetDefinitions' => $presetDefinitions,
            'formContext' => $context,
            'tournamentParentOptions' => EsportMatch::query()
                ->where('event_type', EsportMatch::EVENT_TYPE_TOURNAMENT_RUN)
                ->where('game_key', EsportMatch::GAME_ROCKET_LEAGUE)
                ->orderByDesc('starts_at')
                ->limit(30)
                ->get(['id', 'event_name', 'compétition_name', 'child_matches_unlocked_at']),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function resultOptionsFor(EsportMatch $match): array
    {
        if ($match->isTournamentRun()) {
            $market = $match->markets->firstWhere('key', MatchMarket::KEY_TOURNAMENT_FINISH);
            $options = $market?->selections
                ->mapWithKeys(fn (MatchSelection $selection) => [$selection->key => $selection->label])
                ->toArray() ?? [];

            return $options + [EsportMatch::RESULT_VOID => 'Annuler et rembourser'];
        }

        $teamA = (string) ($match->team_a_name ?: $match->home_team ?: 'Team A');
        $teamB = (string) ($match->team_b_name ?: $match->away_team ?: 'Team B');

        return [
            EsportMatch::RESULT_TEAM_A => $teamA,
            EsportMatch::RESULT_TEAM_B => $teamB,
            EsportMatch::RESULT_DRAW => 'Match nul',
            EsportMatch::RESULT_VOID => 'Annuler et rembourser',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function serializeMarketsForForm(EsportMatch $match): array
    {
        return $match->markets
            ->sortBy(fn (MatchMarket $market) => $market->key)
            ->map(fn (MatchMarket $market) => [
                'key' => $market->key,
                'title' => $market->title,
                'is_active' => $market->is_active,
                'sort_order' => 0,
                'selections' => $market->selections
                    ->sortBy('sort_order')
                    ->values()
                    ->map(fn (MatchSelection $selection) => [
                        'key' => $selection->key,
                        'label' => $selection->label,
                        'odds' => (float) $selection->odds,
                        'sort_order' => $selection->sort_order,
                    ])
                    ->all(),
            ])
            ->values()
            ->all();
    }
}
