<?php

namespace App\Application\Actions\Matches;

use App\Domain\Betting\Support\MatchMarketCatalog;
use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use RuntimeException;

class SyncMatchMarketsAction
{
    public function __construct(
        private readonly MatchMarketCatalog $matchMarketCatalog
    ) {
    }

    /**
     * @param array<int, array<string, mixed>>|null $markets
     * @param array<string, mixed> $context
     */
    public function execute(EsportMatch $match, ?array $markets, array $context = []): void
    {
        $normalized = $this->matchMarketCatalog->normalizeSubmittedMarkets($markets, array_merge($match->toArray(), $context));
        $hasBets = $match->bets()->exists();

        if ($hasBets && $this->structureChanged($match, $normalized)) {
            throw new RuntimeException('Market structure cannot be changed once bets exist.');
        }

        $keptMarketIds = [];

        foreach ($normalized as $marketData) {
            $market = MatchMarket::query()->updateOrCreate(
                [
                    'match_id' => $match->id,
                    'key' => (string) $marketData['key'],
                ],
                [
                    'title' => (string) $marketData['title'],
                    'is_active' => (bool) ($marketData['is_active'] ?? true),
                ],
            );

            $keptMarketIds[] = $market->id;
            $keptSelectionIds = [];

            foreach ((array) ($marketData['selections'] ?? []) as $selectionData) {
                $selection = MatchSelection::query()->updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'key' => (string) $selectionData['key'],
                    ],
                    [
                        'label' => (string) $selectionData['label'],
                        'odds' => round((float) $selectionData['odds'], 3),
                        'sort_order' => (int) ($selectionData['sort_order'] ?? 0),
                    ],
                );

                $keptSelectionIds[] = $selection->id;
            }

            if (! $hasBets) {
                MatchSelection::query()
                    ->where('market_id', $market->id)
                    ->whereNotIn('id', $keptSelectionIds)
                    ->delete();
            }
        }

        if (! $hasBets) {
            MatchMarket::query()
                ->where('match_id', $match->id)
                ->whereNotIn('id', $keptMarketIds)
                ->delete();
        }
    }

    /**
     * @param array<int, array<string, mixed>> $normalized
     */
    private function structureChanged(EsportMatch $match, array $normalized): bool
    {
        $existing = $match->markets()->with('selections')->get()
            ->mapWithKeys(fn (MatchMarket $market) => [
                $market->key => $market->selections
                    ->pluck('key')
                    ->sort()
                    ->values()
                    ->all(),
            ])
            ->toArray();

        $incoming = collect($normalized)
            ->mapWithKeys(fn (array $market) => [
                (string) $market['key'] => collect((array) ($market['selections'] ?? []))
                    ->pluck('key')
                    ->map(fn ($key) => (string) $key)
                    ->sort()
                    ->values()
                    ->all(),
            ])
            ->toArray();

        ksort($existing);
        ksort($incoming);

        return $existing !== $incoming;
    }
}
