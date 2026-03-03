@extends('layouts.app')

@section('title', 'Leaderboard ligue')

@section('content')
    @php($isPublicApp = request()->routeIs('app.*'))

    <section class="section">
        <h1>Leaderboard - {{ strtoupper($leagueKey) }}</h1>

        <div class="actions">
            @foreach($leagues ?? [] as $league)
                <x-ui.button :href="route($isPublicApp ? 'app.leaderboards.show' : 'leaderboards.show', $league->key)" variant="secondary" magnetic>
                    {{ $league->name }}
                </x-ui.button>
            @endforeach
        </div>
    </section>

    <section class="section">
        @if(empty($leaderboard) || empty($leaderboard['entries']))
            <x-ui.empty-state title="Aucune entree" message="Le leaderboard de cette ligue est vide pour le moment." />
        @else
            <x-ui.table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Joueur</th>
                    <th>Points</th>
                </tr>
                </thead>
                <tbody>
                @foreach($leaderboard['entries'] as $entry)
                    <tr>
                        <td>{{ $entry['position'] }}</td>
                        <td>{{ $entry['name'] }} @if(($currentUserId ?? null) === $entry['user_id']) <strong>(vous)</strong> @endif</td>
                        <td>{{ $entry['total_rank_points'] ?? $entry['rank_points'] ?? 0 }}</td>
                    </tr>
                @endforeach
                </tbody>
            </x-ui.table>
        @endif
    </section>
@endsection
