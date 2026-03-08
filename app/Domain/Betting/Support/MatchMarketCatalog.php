<?php

namespace App\Domain\Betting\Support;

use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;

class MatchMarketCatalog
{
    /**
     * @return array<string, string>
     */
    public function eventTypeOptions(): array
    {
        return (array) config('betting.events.types', []);
    }

    /**
     * @return array<string, string>
     */
    public function gameOptions(): array
    {
        return (array) config('betting.events.games', []);
    }

    /**
     * @return array<int, string>
     */
    public function bestOfOptions(): array
    {
        return array_map('strval', (array) config('betting.events.best_of', []));
    }

    /**
     * @return array<string, string>
     */
    public function statusOptions(): array
    {
        return (array) config('betting.events.statuses', []);
    }

    /**
     * @return array<string, string>
     */
    public function statusShortLabels(): array
    {
        return (array) config('betting.events.status_short_labels', $this->statusOptions());
    }

    /**
     * @return array<string, string>
     */
    public function presetOptions(): array
    {
        return (array) config('betting.market_presets.labels', []);
    }

    public function defaultPresetKeyFor(array|EsportMatch $context): string
    {
        $eventType = (string) data_get($context, 'event_type', EsportMatch::EVENT_TYPE_HEAD_TO_HEAD);
        $gameKey = (string) data_get($context, 'game_key', '');
        $bestOf = data_get($context, 'best_of');

        if ($eventType === EsportMatch::EVENT_TYPE_TOURNAMENT_RUN) {
            return 'rocket_league_tournament';
        }

        if ($gameKey === EsportMatch::GAME_ROCKET_LEAGUE && (int) $bestOf === 7) {
            return 'rocket_league_bo7';
        }

        if ($gameKey === EsportMatch::GAME_ROCKET_LEAGUE && (int) $bestOf === 5) {
            return 'rocket_league_bo5';
        }

        return 'classic_winner';
    }

    /**
     * @param array<int, array<string, mixed>>|null $markets
     * @return array<int, array<string, mixed>>
     */
    public function normalizeSubmittedMarkets(?array $markets, array|EsportMatch $context): array
    {
        if (blank($markets)) {
            $presetKey = (string) data_get($context, 'market_preset', $this->defaultPresetKeyFor($context));

            return $this->buildMarketsFromPreset($presetKey, $context);
        }

        $normalized = [];

        foreach ($markets as $marketIndex => $market) {
            $key = strtoupper(trim((string) data_get($market, 'key')));
            $title = trim((string) data_get($market, 'title', $key));

            if ($key === '' || $title === '') {
                continue;
            }

            $selections = [];
            foreach ((array) data_get($market, 'selections', []) as $selectionIndex => $selection) {
                $selectionKey = strtolower(trim((string) data_get($selection, 'key')));
                $selectionLabel = trim((string) data_get($selection, 'label', $selectionKey));

                if ($selectionKey === '' || $selectionLabel === '') {
                    continue;
                }

                $selections[] = [
                    'key' => $selectionKey,
                    'label' => $selectionLabel,
                    'odds' => round((float) data_get($selection, 'odds', 2), 3),
                    'sort_order' => (int) data_get($selection, 'sort_order', $selectionIndex),
                ];
            }

            if ($selections === []) {
                continue;
            }

            $normalized[] = [
                'key' => $key,
                'title' => $title,
                'is_active' => filter_var(data_get($market, 'is_active', true), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true,
                'sort_order' => (int) data_get($market, 'sort_order', $marketIndex),
                'selections' => $selections,
            ];
        }

        return $normalized !== [] ? $normalized : $this->buildDefaultMarkets($context);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildDefaultMarkets(array|EsportMatch $context): array
    {
        return $this->buildMarketsFromPreset($this->defaultPresetKeyFor($context), $context);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function buildMarketsFromPreset(string $presetKey, array|EsportMatch $context): array
    {
        return match ($presetKey) {
            'rocket_league_tournament' => [
                $this->buildTournamentRunMarket(),
            ],
            'rocket_league_bo5' => [
                $this->buildWinnerMarket($context),
                $this->buildExactScoreMarket(5, $context),
            ],
            'rocket_league_bo7' => [
                $this->buildWinnerMarket($context),
                $this->buildExactScoreMarket(7, $context),
            ],
            default => [
                $this->buildWinnerMarket($context),
            ],
        };
    }

    public function labelForGame(?string $gameKey): string
    {
        return $this->gameOptions()[$gameKey ?? ''] ?? strtoupper((string) $gameKey);
    }

    public function labelForEventType(?string $eventType): string
    {
        return $this->eventTypeOptions()[$eventType ?? ''] ?? strtoupper((string) $eventType);
    }

    public function labelForStatus(?string $status, bool $short = false): string
    {
        $labels = $short ? $this->statusShortLabels() : $this->statusOptions();

        return $labels[$status ?? ''] ?? strtoupper((string) $status);
    }

    public function descriptionForStatus(?string $status): ?string
    {
        return config('betting.events.status_descriptions.'.$status);
    }

    public function labelForMarketKey(?string $marketKey): string
    {
        return config('betting.markets.labels.'.$marketKey, strtoupper((string) $marketKey));
    }

    public function labelForBetStatus(?string $status): string
    {
        return config('betting.bets.statuses.'.$status, strtoupper((string) $status));
    }

    public function labelForResult(EsportMatch $match, ?string $result): string
    {
        if ($result === null || trim($result) === '') {
            return '-';
        }

        if ($match->isTournamentRun()) {
            if (strtolower($result) === EsportMatch::RESULT_VOID) {
                return 'Annule / rembourse';
            }

            $market = $match->relationLoaded('markets')
                ? $match->markets->firstWhere('key', MatchMarket::KEY_TOURNAMENT_FINISH)
                : null;
            $selection = $market?->selections?->firstWhere('key', $result);

            if ($selection) {
                return (string) $selection->label;
            }

            return $this->tournamentSelectionLabels()[$result] ?? ucfirst(str_replace('_', ' ', (string) $result));
        }

        return match (EsportMatch::normalizeResultKey($result)) {
            EsportMatch::RESULT_HOME => (string) ($match->team_a_name ?: $match->home_team ?: 'Equipe A'),
            EsportMatch::RESULT_AWAY => (string) ($match->team_b_name ?: $match->away_team ?: 'Equipe B'),
            EsportMatch::RESULT_DRAW => 'Match nul',
            EsportMatch::RESULT_VOID => 'Annule / rembourse',
            default => strtoupper((string) $result),
        };
    }

    public function labelForPreset(string $presetKey): string
    {
        return $this->presetOptions()[$presetKey] ?? $presetKey;
    }

    /**
     * @return array<string, string>
     */
    public function tournamentSelectionLabels(): array
    {
        return (array) config('betting.market_presets.tournament_finish_labels', []);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildWinnerMarket(array|EsportMatch $context): array
    {
        [$teamA, $teamB] = $this->teamLabels($context);

        return [
            'key' => MatchMarket::KEY_WINNER,
            'title' => 'Winner',
            'is_active' => true,
            'sort_order' => 0,
            'selections' => [
                [
                    'key' => MatchSelection::KEY_TEAM_A,
                    'label' => $teamA,
                    'odds' => round((float) config('betting.odds.winner_fixed', 2.0), 3),
                    'sort_order' => 0,
                ],
                [
                    'key' => MatchSelection::KEY_TEAM_B,
                    'label' => $teamB,
                    'odds' => round((float) config('betting.odds.winner_fixed', 2.0), 3),
                    'sort_order' => 1,
                ],
                [
                    'key' => MatchSelection::KEY_DRAW,
                    'label' => 'Match nul',
                    'odds' => round((float) config('betting.odds.draw_fixed', 3.0), 3),
                    'sort_order' => 2,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTournamentRunMarket(): array
    {
        $labels = $this->tournamentSelectionLabels();
        $odds = (array) config('betting.odds.rocket_league_finish', []);
        $selections = [];

        foreach (MatchSelection::rocketLeagueTournamentSelectionKeys() as $index => $key) {
            $selections[] = [
                'key' => $key,
                'label' => $labels[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                'odds' => round((float) ($odds[$key] ?? 2.0), 3),
                'sort_order' => $index,
            ];
        }

        return [
            'key' => MatchMarket::KEY_TOURNAMENT_FINISH,
            'title' => 'Parcours final ERAH',
            'is_active' => true,
            'sort_order' => 0,
            'selections' => $selections,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildExactScoreMarket(int $bestOf, array|EsportMatch $context): array
    {
        [$teamA, $teamB] = $this->teamLabels($context);
        $oddsMap = (array) data_get(config('betting.odds.rocket_league_exact_score', []), $bestOf, []);
        $selections = [];

        foreach ($oddsMap as $scoreKey => $odds) {
            [$teamAScore, $teamBScore] = array_map('intval', explode('_', (string) $scoreKey));

            $selections[] = [
                'key' => (string) $scoreKey,
                'label' => $teamA.' '.$teamAScore.' - '.$teamBScore.' '.$teamB,
                'odds' => round((float) $odds, 3),
                'sort_order' => count($selections),
            ];
        }

        return [
            'key' => MatchMarket::KEY_EXACT_SCORE,
            'title' => 'Score exact',
            'is_active' => true,
            'sort_order' => 1,
            'selections' => $selections,
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function teamLabels(array|EsportMatch $context): array
    {
        $teamA = trim((string) data_get($context, 'team_a_name', data_get($context, 'home_team', 'Equipe A')));
        $teamB = trim((string) data_get($context, 'team_b_name', data_get($context, 'away_team', 'Equipe B')));

        return [
            $teamA !== '' ? $teamA : 'Equipe A',
            $teamB !== '' ? $teamB : 'Equipe B',
        ];
    }
}
