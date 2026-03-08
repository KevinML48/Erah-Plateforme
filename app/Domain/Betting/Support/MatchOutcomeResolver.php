<?php

namespace App\Domain\Betting\Support;

use App\Models\EsportMatch;
use App\Models\MatchMarket;
use App\Models\MatchSelection;
use Illuminate\Support\Collection;
use RuntimeException;

class MatchOutcomeResolver
{
    public function resolve(
        EsportMatch $match,
        string $result,
        ?int $teamAScore = null,
        ?int $teamBScore = null,
        bool $requireAllMarketResolutions = true
    ): array {
        $markets = $match->relationLoaded('markets')
            ? $match->markets
            : $match->markets()->with('selections')->where('is_active', true)->get();

        $resultKey = strtolower(trim($result));

        if ($resultKey === EsportMatch::RESULT_VOID) {
            return [
                'stored_result' => EsportMatch::RESULT_VOID,
                'winner_result' => EsportMatch::RESULT_VOID,
                'team_a_score' => $teamAScore,
                'team_b_score' => $teamBScore,
                'is_void' => true,
                'resolved_markets' => [],
            ];
        }

        if ($match->isTournamentRun()) {
            return $this->resolveTournamentRun($match, $markets, $resultKey);
        }

        return $this->resolveHeadToHead($markets, $resultKey, $teamAScore, $teamBScore, $requireAllMarketResolutions);
    }

    /**
     * @param Collection<int, MatchMarket> $markets
     * @return array<string, mixed>
     */
    private function resolveTournamentRun(EsportMatch $match, Collection $markets, string $resultKey): array
    {
        $market = $markets->firstWhere('key', MatchMarket::KEY_TOURNAMENT_FINISH);
        if (! $market) {
            throw new RuntimeException('No tournament finish market is configured for this event.');
        }

        $selection = $market->selections->firstWhere('key', $resultKey);
        if (! $selection) {
            throw new RuntimeException('Invalid tournament result selection.');
        }

        return [
            'stored_result' => $selection->key,
            'winner_result' => $selection->key,
            'team_a_score' => null,
            'team_b_score' => null,
            'is_void' => false,
            'resolved_markets' => [
                MatchMarket::KEY_TOURNAMENT_FINISH => $selection->key,
            ],
        ];
    }

    /**
     * @param Collection<int, MatchMarket> $markets
     * @return array<string, mixed>
     */
    private function resolveHeadToHead(
        Collection $markets,
        string $resultKey,
        ?int $teamAScore,
        ?int $teamBScore,
        bool $requireAllMarketResolutions
    ): array {
        $winnerResult = EsportMatch::normalizeResultKey($resultKey);
        if (! $winnerResult) {
            throw new RuntimeException('Invalid match result.');
        }

        $winnerSelection = match ($winnerResult) {
            EsportMatch::RESULT_HOME => MatchSelection::KEY_TEAM_A,
            EsportMatch::RESULT_AWAY => MatchSelection::KEY_TEAM_B,
            EsportMatch::RESULT_DRAW => MatchSelection::KEY_DRAW,
            default => throw new RuntimeException('Invalid winner result.'),
        };

        $resolvedMarkets = [
            MatchMarket::KEY_WINNER => $winnerSelection,
        ];

        $exactScoreMarket = $markets->firstWhere('key', MatchMarket::KEY_EXACT_SCORE);
        if ($exactScoreMarket) {
            if ($teamAScore === null || $teamBScore === null) {
                if (! $requireAllMarketResolutions) {
                    return [
                        'stored_result' => $winnerResult,
                        'winner_result' => $winnerResult,
                        'team_a_score' => null,
                        'team_b_score' => null,
                        'is_void' => false,
                        'resolved_markets' => $resolvedMarkets,
                    ];
                }

                throw new RuntimeException('Exact score settlement requires the final BO score.');
            }

            $exactScoreKey = $teamAScore.'_'.$teamBScore;
            $selection = $exactScoreMarket->selections->firstWhere('key', $exactScoreKey);

            if (! $selection) {
                throw new RuntimeException('Final BO score does not match an active exact score selection.');
            }

            if (($teamAScore > $teamBScore && $winnerResult !== EsportMatch::RESULT_HOME)
                || ($teamBScore > $teamAScore && $winnerResult !== EsportMatch::RESULT_AWAY)
                || ($teamAScore === $teamBScore && $winnerResult !== EsportMatch::RESULT_DRAW)) {
                throw new RuntimeException('Winner result does not match the provided BO score.');
            }

            $resolvedMarkets[MatchMarket::KEY_EXACT_SCORE] = $selection->key;
        } elseif ($teamAScore !== null && $teamBScore !== null) {
            if (($teamAScore > $teamBScore && $winnerResult !== EsportMatch::RESULT_HOME)
                || ($teamBScore > $teamAScore && $winnerResult !== EsportMatch::RESULT_AWAY)
                || ($teamAScore === $teamBScore && $winnerResult !== EsportMatch::RESULT_DRAW)) {
                throw new RuntimeException('Winner result does not match the provided score.');
            }
        }

        return [
            'stored_result' => $winnerResult,
            'winner_result' => $winnerResult,
            'team_a_score' => $teamAScore,
            'team_b_score' => $teamBScore,
            'is_void' => false,
            'resolved_markets' => $resolvedMarkets,
        ];
    }
}
