@extends('layouts.app')

@section('title', 'Ma ligue')

@section('content')
    <section class="section">
        <h1>Ma ligue</h1>
        <p>Ligue actuelle: <strong>{{ $progress->league->name ?? 'N/A' }}</strong></p>
        <p>Rank points: {{ $progress->total_rank_points ?? 0 }}</p>
        <p>XP total: {{ $progress->xp_total ?? 0 }}</p>
        <p>Prochaine ligue: {{ $nextLeague->name ?? 'Aucune' }}</p>
    </section>

    <section class="section">
        <h2>Leaderboard</h2>
        @if(empty($leaderboard) || empty($leaderboard['entries']))
            <x-ui.empty-state title="Aucune entree" message="Aucun joueur n'est encore classe dans votre ligue." />
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
                        <td>{{ $entry['name'] }} @if(($user->id ?? auth()->id()) === $entry['user_id']) <strong>(vous)</strong> @endif</td>
                        <td>{{ $entry['total_rank_points'] ?? $entry['rank_points'] ?? 0 }}</td>
                    </tr>
                @endforeach
                </tbody>
            </x-ui.table>
        @endif
    </section>
@endsection
