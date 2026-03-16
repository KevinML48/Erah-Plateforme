<?php

namespace App\Services;

use App\Domain\Betting\Support\MatchMarketCatalog;
use App\Models\Bet;
use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MatchBettingCommunityService
{
    /**
     * @param Collection<int, mixed> $markets
     * @return array<string, mixed>
     */
    public function build(
        EsportMatch $match,
        Collection $markets,
        MatchMarketCatalog $matchMarketCatalog,
        bool $betIsOpen,
        string $marketFilter = 'all',
        int $participantsPerPage = 18,
    ): array {
        [$marketLabels, $selectionLabels] = $this->buildMarketMaps($markets, $matchMarketCatalog);
        $state = $this->resolveCommunityState($match, $betIsOpen);
        $visibleStatuses = $this->communityVisibleStatuses();

        $baseQuery = Bet::query()
            ->where('match_id', $match->id)
            ->whereIn('status', $visibleStatuses);

        $totals = [
            'bets_count' => (clone $baseQuery)->count(),
            'bettors_count' => (clone $baseQuery)->distinct('user_id')->count('user_id'),
            'total_staked' => (int) (clone $baseQuery)->sum('stake_points'),
        ];

        $selectionRows = (clone $baseQuery)
            ->select([
                'market_key',
                'selection_key',
                DB::raw('COUNT(*) as bets_count'),
                DB::raw('COUNT(DISTINCT user_id) as bettors_count'),
                DB::raw('COALESCE(SUM(stake_points), 0) as total_staked'),
            ])
            ->groupBy('market_key', 'selection_key')
            ->get();

        $marketSummaries = $this->buildMarketSummaries(
            $marketLabels,
            $selectionLabels,
            $selectionRows,
        );

        $topStakes = (clone $baseQuery)
            ->with('user:id,name,avatar_path,provider_avatar_url')
            ->orderByDesc('stake_points')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn (Bet $bet): array => $this->formatBetRow($bet, $marketLabels, $selectionLabels))
            ->values();

        $availableMarketKeys = collect(array_keys($marketLabels))->values();
        $activeMarketFilter = $this->normalizeMarketFilter($marketFilter, $availableMarketKeys);

        $participantsQuery = (clone $baseQuery)
            ->with('user:id,name,avatar_path,provider_avatar_url')
            ->when($activeMarketFilter !== 'all', fn ($query) => $query->where('market_key', $activeMarketFilter))
            ->orderByDesc('stake_points')
            ->orderByDesc('id');

        $participants = $participantsQuery
            ->paginate($participantsPerPage, ['*'], 'bettors_page')
            ->withQueryString();

        $participants->setCollection(
            $participants
                ->getCollection()
                ->map(fn (Bet $bet): array => $this->formatBetRow($bet, $marketLabels, $selectionLabels))
        );

        $winners = collect();
        $losers = collect();
        $topWinnings = collect();
        $voidCount = 0;
        $totalRedistributed = 0;

        if ($state === 'settled') {
            $winners = (clone $baseQuery)
                ->with('user:id,name,avatar_path,provider_avatar_url')
                ->where('status', Bet::STATUS_WON)
                ->orderByDesc('settlement_points')
                ->orderByDesc('stake_points')
                ->orderByDesc('id')
                ->limit(20)
                ->get()
                ->map(fn (Bet $bet): array => $this->formatBetRow($bet, $marketLabels, $selectionLabels))
                ->values();

            $losers = (clone $baseQuery)
                ->with('user:id,name,avatar_path,provider_avatar_url')
                ->where('status', Bet::STATUS_LOST)
                ->orderByDesc('stake_points')
                ->orderByDesc('id')
                ->limit(20)
                ->get()
                ->map(fn (Bet $bet): array => $this->formatBetRow($bet, $marketLabels, $selectionLabels))
                ->values();

            $topWinnings = $winners
                ->sortByDesc('settlement_points')
                ->take(5)
                ->values();

            $voidCount = (clone $baseQuery)
                ->where('status', Bet::STATUS_VOID)
                ->count();

            $totalRedistributed = (int) (
                ($match->relationLoaded('settlement') && $match->settlement)
                    ? $match->settlement->payout_total
                    : (clone $baseQuery)
                        ->whereIn('status', [Bet::STATUS_WON, Bet::STATUS_VOID])
                        ->sum('settlement_points')
            );
        }

        $outcome = $this->buildOutcomeSummary(
            $match,
            $selectionLabels,
            $matchMarketCatalog,
        );

        return [
            'state' => $state,
            'state_title' => $this->stateTitle($state),
            'state_message' => $this->stateMessage($state),
            'totals' => [
                ...$totals,
                'total_redistributed' => $totalRedistributed,
            ],
            'market_filter' => $activeMarketFilter,
            'market_filter_options' => collect([['key' => 'all', 'label' => 'Tous les marches']])
                ->concat($availableMarketKeys->map(fn (string $key): array => [
                    'key' => $key,
                    'label' => $marketLabels[$key] ?? $key,
                ]))
                ->values(),
            'market_summaries' => $marketSummaries,
            'top_stakes' => $topStakes,
            'participants' => $participants,
            'results' => [
                'winners' => $winners,
                'losers' => $losers,
                'top_winnings' => $topWinnings,
                'void_count' => $voidCount,
                'winner_count' => $winners->count(),
                'loser_count' => $losers->count(),
                'total_redistributed' => $totalRedistributed,
                'outcome' => $outcome,
            ],
            'has_bets' => $totals['bets_count'] > 0,
        ];
    }

    /**
     * @param Collection<int, mixed> $markets
     * @return array{0: array<string, string>, 1: array<string, array<string, string>>}
     */
    private function buildMarketMaps(Collection $markets, MatchMarketCatalog $matchMarketCatalog): array
    {
        $marketLabels = [];
        $selectionLabels = [];

        foreach ($markets as $market) {
            $marketKey = strtoupper(trim((string) data_get($market, 'key')));
            if ($marketKey === '') {
                continue;
            }

            $marketLabels[$marketKey] = trim((string) data_get($market, 'title'))
                ?: $matchMarketCatalog->labelForMarketKey($marketKey);

            $selectionMap = [];
            foreach (collect(data_get($market, 'selections', [])) as $selection) {
                $selectionKey = strtolower(trim((string) data_get($selection, 'key')));
                if ($selectionKey === '') {
                    continue;
                }

                $selectionMap[$selectionKey] = trim((string) data_get($selection, 'label'))
                    ?: Str::headline(str_replace('_', ' ', $selectionKey));
            }

            $selectionLabels[$marketKey] = $selectionMap;
        }

        return [$marketLabels, $selectionLabels];
    }

    /**
     * @param array<string, string> $marketLabels
     * @param array<string, array<string, string>> $selectionLabels
     * @return Collection<int, array<string, mixed>>
     */
    private function buildMarketSummaries(
        array $marketLabels,
        array $selectionLabels,
        Collection $selectionRows,
    ): Collection {
        return collect($marketLabels)
            ->map(function (string $marketLabel, string $marketKey) use ($selectionRows, $selectionLabels): array {
                $rowsForMarket = $selectionRows
                    ->where('market_key', $marketKey)
                    ->keyBy(fn ($row) => strtolower((string) $row->selection_key));

                $knownSelections = collect($selectionLabels[$marketKey] ?? []);
                $knownRows = $knownSelections->map(function (string $selectionLabel, string $selectionKey) use ($rowsForMarket): array {
                    $row = $rowsForMarket->get($selectionKey);

                    return [
                        'selection_key' => $selectionKey,
                        'selection_label' => $selectionLabel,
                        'bets_count' => (int) ($row->bets_count ?? 0),
                        'bettors_count' => (int) ($row->bettors_count ?? 0),
                        'total_staked' => (int) ($row->total_staked ?? 0),
                    ];
                });

                $otherRows = $rowsForMarket
                    ->reject(fn ($row, string $selectionKey) => $knownSelections->has($selectionKey))
                    ->map(function ($row, string $selectionKey): array {
                        return [
                            'selection_key' => $selectionKey,
                            'selection_label' => Str::headline(str_replace('_', ' ', $selectionKey)),
                            'bets_count' => (int) ($row->bets_count ?? 0),
                            'bettors_count' => (int) ($row->bettors_count ?? 0),
                            'total_staked' => (int) ($row->total_staked ?? 0),
                        ];
                    });

                $rows = $knownRows
                    ->concat($otherRows)
                    ->values();

                $marketTotalStaked = (int) $rows->sum('total_staked');
                $marketTotalBettors = (int) $rows->sum('bettors_count');

                $rows = $rows
                    ->map(function (array $row) use ($marketTotalStaked, $marketTotalBettors): array {
                        $stakeShare = $marketTotalStaked > 0
                            ? round(((int) $row['total_staked'] * 100) / $marketTotalStaked, 1)
                            : 0.0;
                        $bettorShare = $marketTotalBettors > 0
                            ? round(((int) $row['bettors_count'] * 100) / $marketTotalBettors, 1)
                            : 0.0;

                        return [
                            ...$row,
                            'stake_share' => $stakeShare,
                            'bettor_share' => $bettorShare,
                        ];
                    })
                    ->sortByDesc('total_staked')
                    ->values();

                return [
                    'market_key' => $marketKey,
                    'market_label' => $marketLabel,
                    'bets_count' => (int) $rows->sum('bets_count'),
                    'bettors_count' => (int) $rows->sum('bettors_count'),
                    'total_staked' => $marketTotalStaked,
                    'selections' => $rows,
                ];
            })
            ->values();
    }

    /**
     * @param array<string, string> $marketLabels
     * @param array<string, array<string, string>> $selectionLabels
     * @return array<string, mixed>
     */
    private function formatBetRow(Bet $bet, array $marketLabels, array $selectionLabels): array
    {
        $marketKey = strtoupper((string) $bet->market_key);
        $selectionKey = strtolower((string) $bet->selection_key);
        $userName = trim((string) ($bet->user?->name ?? 'Utilisateur'));

        return [
            'id' => (int) $bet->id,
            'user_id' => (int) $bet->user_id,
            'user_name' => $userName !== '' ? $userName : 'Utilisateur',
            'user_avatar_url' => $bet->user?->display_avatar_url,
            'market_key' => $marketKey,
            'market_label' => $marketLabels[$marketKey] ?? Str::headline(str_replace('_', ' ', $marketKey)),
            'selection_key' => $selectionKey,
            'selection_label' => $selectionLabels[$marketKey][$selectionKey]
                ?? Str::headline(str_replace('_', ' ', $selectionKey)),
            'stake_points' => (int) $bet->stake_points,
            'potential_payout' => (int) $bet->potential_payout,
            'settlement_points' => (int) $bet->settlement_points,
            'status' => (string) $bet->status,
            'placed_at' => $bet->placed_at,
            'settled_at' => $bet->settled_at,
        ];
    }

    /**
     * @param array<string, array<string, string>> $selectionLabels
     * @return array<string, mixed>
     */
    private function buildOutcomeSummary(
        EsportMatch $match,
        array $selectionLabels,
        MatchMarketCatalog $matchMarketCatalog,
    ): array {
        $resultLabel = $matchMarketCatalog->labelForResult($match, $match->result);
        $normalizedResult = EsportMatch::normalizeResultKey($match->result);

        if ($normalizedResult === null) {
            return [
                'result_label' => $resultLabel,
                'winner_side_label' => null,
                'loser_side_label' => null,
            ];
        }

        if ($normalizedResult === EsportMatch::RESULT_VOID) {
            return [
                'result_label' => $resultLabel,
                'winner_side_label' => 'Pari annule / rembourse',
                'loser_side_label' => null,
            ];
        }

        $winnerMarketSelections = $selectionLabels[MatchMarket::KEY_WINNER] ?? [];

        $winningSelectionKey = match ($normalizedResult) {
            EsportMatch::RESULT_HOME => MatchSelection::KEY_TEAM_A,
            EsportMatch::RESULT_AWAY => MatchSelection::KEY_TEAM_B,
            EsportMatch::RESULT_DRAW => MatchSelection::KEY_DRAW,
            default => null,
        };

        if ($winningSelectionKey === null) {
            return [
                'result_label' => $resultLabel,
                'winner_side_label' => $resultLabel,
                'loser_side_label' => null,
            ];
        }

        $winnerLabel = $winnerMarketSelections[$winningSelectionKey] ?? $resultLabel;
        $loserLabel = match ($winningSelectionKey) {
            MatchSelection::KEY_TEAM_A => $winnerMarketSelections[MatchSelection::KEY_TEAM_B] ?? null,
            MatchSelection::KEY_TEAM_B => $winnerMarketSelections[MatchSelection::KEY_TEAM_A] ?? null,
            default => null,
        };

        return [
            'result_label' => $resultLabel,
            'winner_side_label' => $winnerLabel,
            'loser_side_label' => $loserLabel,
        ];
    }

    /**
     * @param Collection<int, string> $availableMarketKeys
     */
    private function normalizeMarketFilter(string $marketFilter, Collection $availableMarketKeys): string
    {
        $normalized = strtoupper(trim($marketFilter));
        if ($normalized === '' || strtolower($normalized) === 'all') {
            return 'all';
        }

        return $availableMarketKeys->contains($normalized)
            ? $normalized
            : 'all';
    }

    private function resolveCommunityState(EsportMatch $match, bool $betIsOpen): string
    {
        if ($this->isSettled($match)) {
            return 'settled';
        }

        if ($betIsOpen) {
            return 'open';
        }

        return 'closed';
    }

    private function isSettled(EsportMatch $match): bool
    {
        return $match->settled_at !== null || (string) $match->status === EsportMatch::STATUS_SETTLED;
    }

    private function stateTitle(string $state): string
    {
        return match ($state) {
            'open' => 'Paris ouverts',
            'closed' => 'Paris clotures',
            'settled' => 'Paris regles',
            default => 'Paris du match',
        };
    }

    private function stateMessage(string $state): string
    {
        return match ($state) {
            'open' => 'Les mises evoluent en direct. Vous pouvez encore participer tant que la cloture n est pas atteinte.',
            'closed' => 'Les paris sont fermes. Les donnees restent figees jusqu au resultat officiel et au reglement.',
            'settled' => 'Le reglement est termine. Consultez les gagnants, les perdants et les plus gros gains.',
            default => 'Suivi communautaire des mises sur ce match.',
        };
    }

    /**
     * @return array<int, string>
     */
    private function communityVisibleStatuses(): array
    {
        return [
            Bet::STATUS_PENDING,
            Bet::STATUS_PLACED,
            Bet::STATUS_WON,
            Bet::STATUS_LOST,
            Bet::STATUS_VOID,
        ];
    }
}
