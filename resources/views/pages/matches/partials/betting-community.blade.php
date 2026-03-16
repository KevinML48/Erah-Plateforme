@php
    $community = $betCommunity ?? [];
    $state = (string) ($community['state'] ?? 'open');
    $stateTitle = (string) ($community['state_title'] ?? 'Paris du match');
    $stateMessage = (string) ($community['state_message'] ?? 'Suivi communautaire des mises sur ce match.');

    $totals = $community['totals'] ?? [];
    $totalBettors = (int) ($totals['bettors_count'] ?? 0);
    $totalBets = (int) ($totals['bets_count'] ?? 0);
    $totalStaked = (int) ($totals['total_staked'] ?? 0);
    $totalRedistributed = (int) ($totals['total_redistributed'] ?? 0);

    $marketSummaries = collect($community['market_summaries'] ?? []);
    $topStakes = collect($community['top_stakes'] ?? []);
    $participants = $community['participants'] ?? null;
    $marketFilterOptions = collect($community['market_filter_options'] ?? []);
    $marketFilter = (string) ($community['market_filter'] ?? 'all');

    $results = $community['results'] ?? [];
    $winners = collect($results['winners'] ?? []);
    $losers = collect($results['losers'] ?? []);
    $topWinnings = collect($results['top_winnings'] ?? []);
    $winnerCount = (int) ($results['winner_count'] ?? 0);
    $loserCount = (int) ($results['loser_count'] ?? 0);
    $voidCount = (int) ($results['void_count'] ?? 0);
    $outcome = $results['outcome'] ?? [];
    $resultLabel = (string) ($outcome['result_label'] ?? '-');
    $winnerSideLabel = (string) ($outcome['winner_side_label'] ?? '-');
    $loserSideLabel = (string) ($outcome['loser_side_label'] ?? '-');

    $formatPoints = static fn (int $value): string => number_format($value, 0, ',', ' ').' pts';
    $publicProfileUrl = static fn (array $row): ?string => ((int) ($row['user_id'] ?? 0) > 0)
        ? route('users.public', (int) $row['user_id'])
        : null;
@endphp

<section class="match-market-card tt-anim-fadeinup">
    <div class="match-market-head">
        <div>
            <h3 class="match-market-title">Paris du match</h3>
            <p class="match-market-note">{{ $stateMessage }}</p>
        </div>
        <span class="match-community-status {{ $state === 'open' ? 'is-open' : ($state === 'settled' ? 'is-settled' : 'is-closed') }}">{{ $stateTitle }}</span>
    </div>

    @if($totalBets === 0)
        <p class="match-market-note">Aucun pari enregistre pour le moment sur ce match.</p>
    @else
        <div class="match-community-grid">
            <div class="match-community-kpi-grid">
                <article class="match-community-kpi">
                    <span>Parieurs</span>
                    <strong>{{ $totalBettors }}</strong>
                </article>
                <article class="match-community-kpi">
                    <span>Paris enregistres</span>
                    <strong>{{ $totalBets }}</strong>
                </article>
                <article class="match-community-kpi">
                    <span>Total mise</span>
                    <strong>{{ $formatPoints($totalStaked) }}</strong>
                </article>
                <article class="match-community-kpi">
                    <span>{{ $state === 'settled' ? 'Total redistribue' : 'Marches actifs' }}</span>
                    <strong>
                        @if($state === 'settled')
                            {{ $formatPoints($totalRedistributed) }}
                        @else
                            {{ $marketSummaries->count() }}
                        @endif
                    </strong>
                </article>
            </div>

            <div class="match-community-split">
                <article class="match-community-box">
                    <h4>Repartition par camp</h4>
                    @foreach($marketSummaries as $marketSummary)
                        <div class="match-community-market-list">
                            <div class="match-community-market-item-head">
                                <strong>{{ $marketSummary['market_label'] ?? 'Marche' }}</strong>
                                <small>{{ $formatPoints((int) ($marketSummary['total_staked'] ?? 0)) }}</small>
                            </div>

                            @foreach(($marketSummary['selections'] ?? []) as $selection)
                                @php($stakeShareStyle = 'width: '.max(0, min(100, (float) ($selection['stake_share'] ?? 0))).'%;')
                                @php($stakeShareAttr = ' style="'.e($stakeShareStyle).'"')
                                <div class="match-community-market-item">
                                    <div class="match-community-market-item-head">
                                        <strong>{{ $selection['selection_label'] ?? 'Camp' }}</strong>
                                        <small>{{ $selection['bettors_count'] ?? 0 }} parieur(s) | {{ $formatPoints((int) ($selection['total_staked'] ?? 0)) }}</small>
                                    </div>
                                    <div class="match-community-bar">
                                        <span{!! $stakeShareAttr !!}></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </article>

                <article class="match-community-box">
                    <h4>Top mises</h4>
                    <div class="match-community-table-wrap">
                        <table class="match-community-table">
                            <thead>
                                <tr>
                                    <th>Parieur</th>
                                    <th>Camp</th>
                                    <th>Mise</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topStakes as $stakeRow)
                                    <tr>
                                        <td>
                                            @php $stakeProfileUrl = $publicProfileUrl($stakeRow); @endphp
                                            @if($stakeProfileUrl)
                                                <a href="{{ $stakeProfileUrl }}" class="match-bettor match-bettor-link">
                                                    <span class="match-bettor-avatar">
                                                        @if(! blank($stakeRow['user_avatar_url'] ?? null))
                                                            <img src="{{ $stakeRow['user_avatar_url'] }}" alt="{{ $stakeRow['user_name'] ?? 'Parieur' }}">
                                                        @else
                                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr((string) ($stakeRow['user_name'] ?? '?'), 0, 1)) }}
                                                        @endif
                                                    </span>
                                                    <strong>{{ $stakeRow['user_name'] ?? 'Parieur' }}</strong>
                                                </a>
                                            @else
                                                <span class="match-bettor">
                                                    <span class="match-bettor-avatar">
                                                        @if(! blank($stakeRow['user_avatar_url'] ?? null))
                                                            <img src="{{ $stakeRow['user_avatar_url'] }}" alt="{{ $stakeRow['user_name'] ?? 'Parieur' }}">
                                                        @else
                                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr((string) ($stakeRow['user_name'] ?? '?'), 0, 1)) }}
                                                        @endif
                                                    </span>
                                                    <strong>{{ $stakeRow['user_name'] ?? 'Parieur' }}</strong>
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $stakeRow['selection_label'] ?? '-' }}</td>
                                        <td><strong>{{ $formatPoints((int) ($stakeRow['stake_points'] ?? 0)) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            @if($participants instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <article class="match-community-box">
                    <div class="match-market-head">
                        <div>
                            <h4>Participants</h4>
                            <p class="match-community-subtext">Qui a mise, sur quel camp, et pour quel montant.</p>
                        </div>
                        <form method="GET" action="{{ route($matchShowRouteName, $match->id) }}" class="match-community-filter">
                            <select name="bettors_market" class="tt-form-control" onchange="this.form.submit()">
                                @foreach($marketFilterOptions as $option)
                                    <option value="{{ $option['key'] }}" @selected($marketFilter === (string) $option['key'])>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="match-community-table-wrap">
                        <table class="match-community-table">
                            <thead>
                                <tr>
                                    <th>Parieur</th>
                                    <th>Marche</th>
                                    <th>Camp choisi</th>
                                    <th>Mise</th>
                                    <th>Potentiel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($participants as $participant)
                                    <tr>
                                        <td>
                                            @php $participantProfileUrl = $publicProfileUrl($participant); @endphp
                                            @if($participantProfileUrl)
                                                <a href="{{ $participantProfileUrl }}" class="match-bettor match-bettor-link">
                                                    <span class="match-bettor-avatar">
                                                        @if(! blank($participant['user_avatar_url'] ?? null))
                                                            <img src="{{ $participant['user_avatar_url'] }}" alt="{{ $participant['user_name'] ?? 'Parieur' }}">
                                                        @else
                                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr((string) ($participant['user_name'] ?? '?'), 0, 1)) }}
                                                        @endif
                                                    </span>
                                                    <strong>{{ $participant['user_name'] ?? 'Parieur' }}</strong>
                                                </a>
                                            @else
                                                <span class="match-bettor">
                                                    <span class="match-bettor-avatar">
                                                        @if(! blank($participant['user_avatar_url'] ?? null))
                                                            <img src="{{ $participant['user_avatar_url'] }}" alt="{{ $participant['user_name'] ?? 'Parieur' }}">
                                                        @else
                                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr((string) ($participant['user_name'] ?? '?'), 0, 1)) }}
                                                        @endif
                                                    </span>
                                                    <strong>{{ $participant['user_name'] ?? 'Parieur' }}</strong>
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $participant['market_label'] ?? '-' }}</td>
                                        <td>{{ $participant['selection_label'] ?? '-' }}</td>
                                        <td><strong>{{ $formatPoints((int) ($participant['stake_points'] ?? 0)) }}</strong></td>
                                        <td>{{ $formatPoints((int) ($participant['potential_payout'] ?? 0)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($participants->hasPages())
                        @php
                            $windowStart = max(1, $participants->currentPage() - 1);
                            $windowEnd = min($participants->lastPage(), $participants->currentPage() + 1);
                        @endphp
                        <div class="tt-pagination tt-pagin-center padding-top-30">
                            <div class="tt-pagin-prev">
                                <a href="{{ $participants->previousPageUrl() ?: '#' }}" class="tt-pagin-item tt-magnetic-item {{ $participants->onFirstPage() ? 'bet-pagin-item-disabled' : '' }}">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                            <div class="tt-pagin-numbers">
                                @for($page = $windowStart; $page <= $windowEnd; $page++)
                                    <a href="{{ $participants->url($page) }}" class="tt-pagin-item tt-magnetic-item {{ $participants->currentPage() === $page ? 'active' : '' }}">{{ $page }}</a>
                                @endfor
                            </div>
                            <div class="tt-pagin-next">
                                <a href="{{ $participants->nextPageUrl() ?: '#' }}" class="tt-pagin-item tt-pagin-next tt-magnetic-item {{ $participants->hasMorePages() ? '' : 'bet-pagin-item-disabled' }}">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </article>
            @endif

            @if($state === 'settled')
                <article class="match-community-box">
                    <div class="match-market-head">
                        <div>
                            <h4>Resultats des paris</h4>
                            <p class="match-community-subtext">Lecture immediate des gagnants, des perdants et des plus gros gains apres reglement.</p>
                        </div>
                        <span class="match-pill">{{ $resultLabel }}</span>
                    </div>

                    <div class="match-community-results-grid">
                        <article class="match-community-result-card">
                            <span>Camp gagnant</span>
                            <strong>{{ $winnerSideLabel }}</strong>
                        </article>
                        <article class="match-community-result-card">
                            <span>Camp perdant</span>
                            <strong>{{ $loserSideLabel }}</strong>
                        </article>
                        <article class="match-community-result-card">
                            <span>Parieurs gagnants</span>
                            <strong>{{ $winnerCount }}</strong>
                        </article>
                        <article class="match-community-result-card">
                            <span>Parieurs perdants</span>
                            <strong>{{ $loserCount }}</strong>
                        </article>
                        <article class="match-community-result-card">
                            <span>Paris rembourses (void)</span>
                            <strong>{{ $voidCount }}</strong>
                        </article>
                        <article class="match-community-result-card">
                            <span>Total redistribue</span>
                            <strong>{{ $formatPoints((int) ($results['total_redistributed'] ?? 0)) }}</strong>
                        </article>
                    </div>

                    <div class="match-community-split">
                        <div class="match-community-box">
                            <h4>Top gains</h4>
                            <div class="match-community-ranked-list">
                                @forelse($topWinnings as $winning)
                                    <div class="match-community-market-item-head">
                                        @php $winningProfileUrl = $publicProfileUrl($winning); @endphp
                                        @if($winningProfileUrl)
                                            <a href="{{ $winningProfileUrl }}" class="match-community-user-link"><strong>{{ $winning['user_name'] ?? 'Parieur' }}</strong></a>
                                        @else
                                            <strong>{{ $winning['user_name'] ?? 'Parieur' }}</strong>
                                        @endif
                                        <small>+{{ $formatPoints((int) ($winning['settlement_points'] ?? 0)) }}</small>
                                    </div>
                                @empty
                                    <p class="match-community-subtext">Aucun gain enregistre.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="match-community-box">
                            <h4>Gagnants / perdants</h4>
                            <div class="match-community-ranked-list">
                                <strong>Gagnants</strong>
                                @forelse($winners as $winner)
                                    <div class="match-community-market-item-head">
                                        @php $winnerProfileUrl = $publicProfileUrl($winner); @endphp
                                        <span>
                                            @if($winnerProfileUrl)
                                                <a href="{{ $winnerProfileUrl }}" class="match-community-user-link">{{ $winner['user_name'] ?? 'Parieur' }}</a>
                                            @else
                                                {{ $winner['user_name'] ?? 'Parieur' }}
                                            @endif
                                            ({{ $winner['selection_label'] ?? '-' }})
                                        </span>
                                        <small>+{{ $formatPoints((int) ($winner['settlement_points'] ?? 0)) }}</small>
                                    </div>
                                @empty
                                    <p class="match-community-subtext">Aucun gagnant.</p>
                                @endforelse

                                <strong>Perdants</strong>
                                @forelse($losers as $loser)
                                    <div class="match-community-market-item-head">
                                        @php $loserProfileUrl = $publicProfileUrl($loser); @endphp
                                        <span>
                                            @if($loserProfileUrl)
                                                <a href="{{ $loserProfileUrl }}" class="match-community-user-link">{{ $loser['user_name'] ?? 'Parieur' }}</a>
                                            @else
                                                {{ $loser['user_name'] ?? 'Parieur' }}
                                            @endif
                                            ({{ $loser['selection_label'] ?? '-' }})
                                        </span>
                                        <small>-{{ $formatPoints((int) ($loser['stake_points'] ?? 0)) }}</small>
                                    </div>
                                @empty
                                    <p class="match-community-subtext">Aucun perdant.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </article>
            @endif
        </div>
    @endif
</section>
