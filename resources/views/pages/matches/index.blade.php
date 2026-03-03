@extends('layouts.app')

@section('title', 'Matches')

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.matches.index' : 'matches.index';
        $showRouteName = $isPublicApp ? 'app.matches.show' : 'matches.show';
    @endphp

    <section class="section">
        <h1>Matches</h1>
        <div class="actions">
            <x-ui.button :href="route($indexRouteName, ['tab' => 'upcoming'])" variant="secondary" magnetic>A venir</x-ui.button>
            <x-ui.button :href="route($indexRouteName, ['tab' => 'live'])" variant="secondary" magnetic>Live</x-ui.button>
            <x-ui.button :href="route($indexRouteName, ['tab' => 'finished'])" variant="secondary" magnetic>Termines</x-ui.button>
        </div>
        @if($isPublicApp)
            <p class="meta">Consultation publique active. Le placement de paris demande une connexion.</p>
        @endif
    </section>

    <section class="section">
        @if(($matches ?? null) && $matches->count())
            <x-ui.table>
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
                        <td>
                            <x-ui.button :href="route($showRouteName, $match->id)" variant="outline" size="sm">Voir</x-ui.button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </x-ui.table>

            <div class="actions">{{ $matches->links() }}</div>
        @else
            <p class="meta">Aucun match dans cet onglet.</p>
        @endif
    </section>
@endsection
