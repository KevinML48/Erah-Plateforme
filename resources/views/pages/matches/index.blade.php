@extends('layouts.app')

@section('title', 'Matches')

@section('content')
    <section class="section">
        <h1>Matches</h1>
        <div class="actions">
            <a class="button-link" href="{{ route('matches.index', ['tab' => 'upcoming']) }}">A venir</a>
            <a class="button-link" href="{{ route('matches.index', ['tab' => 'live']) }}">Live</a>
            <a class="button-link" href="{{ route('matches.index', ['tab' => 'finished']) }}">Termines</a>
        </div>
    </section>

    <section class="section">
        @if(($matches ?? null) && $matches->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Match</th>
                            <th>Status</th>
                            <th>Debut</th>
                            <th>Lock</th>
                            <th>Bets</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($matches as $match)
                        <tr>
                            <td>{{ $match->id }}</td>
                            <td>{{ $match->team_a_name ?? $match->home_team }} vs {{ $match->team_b_name ?? $match->away_team }}</td>
                            <td><span class="badge">{{ $match->status }}</span></td>
                            <td>{{ optional($match->starts_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($match->locked_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $match->bets_count ?? 0 }}</td>
                            <td><a href="{{ route('matches.show', $match->id) }}">Voir</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="actions">{{ $matches->links() }}</div>
        @else
            <p class="meta">Aucun match dans cet onglet.</p>
        @endif
    </section>
@endsection
