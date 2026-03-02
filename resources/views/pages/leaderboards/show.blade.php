@extends('layouts.app')

@section('title', 'Leaderboard ligue')

@section('content')
    <section class="section">
        <h1>Leaderboard - {{ strtoupper($leagueKey) }}</h1>

        <div class="actions">
            @foreach($leagues ?? [] as $league)
                <a class="button-link" href="{{ route('leaderboards.show', $league->key) }}">{{ $league->name }}</a>
            @endforeach
        </div>
    </section>

    <section class="section">
        @if(empty($leaderboard) || empty($leaderboard['entries']))
            <p class="meta">Aucune entree.</p>
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
                    @foreach($leaderboard['entries'] as $entry)
                        <tr>
                            <td>{{ $entry['position'] }}</td>
                            <td>{{ $entry['name'] }} @if(($currentUserId ?? null) === $entry['user_id']) <strong>(vous)</strong> @endif</td>
                            <td>{{ $entry['total_rank_points'] ?? $entry['rank_points'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
