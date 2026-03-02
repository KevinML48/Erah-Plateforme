@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <section class="section">
        <h1>Console Dashboard</h1>
        <p class="meta">Vue globale de controle pour tester rapidement toutes les fonctionnalites backend.</p>

        <div class="grid grid-3">
            <div>
                <h3>Utilisateur connecte</h3>
                <p><strong>{{ $user->name ?? auth()->user()->name }}</strong></p>
                <p>{{ $user->email ?? auth()->user()->email }}</p>
                <p class="meta">Role: {{ $user->role ?? auth()->user()->role }}</p>
            </div>
            <div>
                <h3>Progression</h3>
                <p>Rank points: {{ $progress->total_rank_points ?? 0 }}</p>
                <p>XP total: {{ $progress->xp_total ?? 0 }}</p>
                <p>Ligue: {{ $progress->league->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3>Wallets</h3>
                <p>bet_points: {{ (int) ($betWalletBalance ?? 0) }}</p>
                <p>reward_points: {{ (int) ($rewardWalletBalance ?? 0) }}</p>
                <p>Prochaine ligue: {{ $nextLeague->name ?? 'Aucune' }}</p>
                <p>Progression: {{ $progressPercent ?? 0 }}%</p>
            </div>
        </div>

        <div class="actions">
            <a class="button-link" href="{{ route('users.index') }}">Users</a>
            <a class="button-link" href="{{ route('ranking.index') }}">Ranking</a>
            <a class="button-link" href="{{ route('matches.index') }}">Voir matches</a>
            <a class="button-link" href="{{ route('bets.index') }}">Mes paris</a>
            <a class="button-link" href="{{ route('wallets.index') }}">Wallets</a>
            <a class="button-link" href="{{ route('gifts.wallet') }}">Wallet reward</a>
            <a class="button-link" href="{{ route('clips.index') }}">Clips</a>
            <a class="button-link" href="{{ route('missions.index') }}">Missions</a>
            <a class="button-link" href="{{ route('gifts.index') }}">Gifts</a>
            <a class="button-link" href="{{ route('notifications.index') }}">Notifications</a>
            <a class="button-link" href="{{ route('duels.index') }}">Duels</a>
        </div>
    </section>

    <section class="section">
        <h2>Top ligue</h2>
        @php
            $leaderboardEntries = collect(data_get($leaderboard, 'entries', []));
        @endphp
        @if($leaderboardEntries->isEmpty())
            <p class="meta">Leaderboard indisponible.</p>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Joueur</th>
                        <th>Points</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leaderboardEntries->take(5) as $entry)
                        <tr>
                            <td>{{ data_get($entry, 'position') }}</td>
                            <td>{{ data_get($entry, 'name') }} @if(data_get($entry, 'user_id') === auth()->id()) <strong>(vous)</strong> @endif</td>
                            <td>{{ data_get($entry, 'total_rank_points', data_get($entry, 'rank_points', 0)) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="section">
        <h2>Recents (5)</h2>
        <div class="grid grid-2">
            <div>
                <h3>Clips</h3>
                @if(($clips ?? collect())->count())
                    <ul>
                        @foreach($clips as $clip)
                            <li>
                                <a href="{{ route('clips.show', $clip->slug) }}">{{ $clip->title }}</a>
                                <span class="meta">likes {{ $clip->likes_count }} / comments {{ $clip->comments_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="meta">Aucun clip.</p>
                @endif
            </div>
            <div>
                <h3>Notifications</h3>
                @if(($recentNotifications ?? collect())->count())
                    <ul>
                        @foreach($recentNotifications as $notification)
                            <li>
                                {{ $notification->title }}
                                <span class="badge">{{ $notification->category }}</span>
                                @if(!$notification->read_at)<span class="badge">unread</span>@endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="meta">Aucune notification.</p>
                @endif
            </div>
            <div>
                <h3>Matches</h3>
                @if(($upcomingMatches ?? collect())->count())
                    <ul>
                        @foreach($upcomingMatches as $match)
                            <li>
                                <a href="{{ route('matches.show', $match->id) }}">
                                    {{ $match->team_a_name ?? $match->home_team }} vs {{ $match->team_b_name ?? $match->away_team }}
                                </a>
                                <span class="meta">{{ optional($match->starts_at)->format('Y-m-d H:i') }} - {{ $match->status }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="meta">Aucun match.</p>
                @endif
            </div>
            <div>
                <h3>Bets</h3>
                @if(($recentBets ?? collect())->count())
                    <ul>
                        @foreach($recentBets as $bet)
                            <li>
                                <a href="{{ route('matches.show', $bet->match_id) }}">
                                    {{ $bet->match->team_a_name ?? $bet->match->home_team ?? 'Team A' }}
                                    vs
                                    {{ $bet->match->team_b_name ?? $bet->match->away_team ?? 'Team B' }}
                                </a>
                                <span class="meta">stake {{ $bet->stake_points }} - {{ $bet->status }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="meta">Aucun pari.</p>
                @endif
            </div>
        </div>
    </section>

    <section class="section">
        <h2>Etat rapide</h2>
        <ul>
            <li>Duels pending: {{ ($pendingDuels ?? collect())->count() }}</li>
            <li>Missions du jour: {{ ($dailyMissions ?? collect())->count() }}</li>
            <li>Position ligue: {{ $rankPosition ?? 'N/A' }}</li>
        </ul>
    </section>
@endsection
