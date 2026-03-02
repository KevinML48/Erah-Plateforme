@extends('layouts.app')

@section('title', 'Admin matches')

@section('content')
    <section class="section">
        <h1>Admin matches</h1>
        <div class="actions">
            <a class="button-link" href="{{ route('admin.matches.create') }}">Creer match</a>
            <a class="button-link" href="{{ route('admin.matches.index', ['status' => 'all']) }}">Tous</a>
            @foreach(($statuses ?? []) as $statusName)
                <a class="button-link" href="{{ route('admin.matches.index', ['status' => $statusName]) }}">{{ $statusName }}</a>
            @endforeach
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
                        <th>Bets</th>
                        <th>Resultat</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($matches as $match)
                        <tr>
                            <td>{{ $match->id }}</td>
                            <td>{{ $match->team_a_name ?? $match->home_team }} vs {{ $match->team_b_name ?? $match->away_team }}</td>
                            <td>{{ $match->status }}</td>
                            <td>{{ optional($match->starts_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $match->bets_count ?? 0 }}</td>
                            <td>{{ $match->result ?: '-' }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button-link" href="{{ route('admin.matches.edit', $match->id) }}">Edit</a>
                                    <a class="button-link" href="{{ route('admin.matches.manage', $match->id) }}">Manage</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="actions">{{ $matches->links() }}</div>
        @else
            <p class="meta">Aucun match.</p>
        @endif
    </section>
@endsection
