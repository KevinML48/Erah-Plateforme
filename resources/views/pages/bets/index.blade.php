@extends('layouts.app')

@section('title', 'Mes paris')

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.bets.index' : 'bets.index';
        $matchShowRouteName = $isPublicApp ? 'app.matches.show' : 'matches.show';
        $cancelRouteName = $isPublicApp ? 'app.bets.cancel' : 'bets.cancel';
    @endphp

    <section class="section">
        <h1>Mes paris</h1>
        <div class="actions">
            <x-ui.button :href="route($indexRouteName, ['tab' => 'active'])" variant="secondary" magnetic>En cours</x-ui.button>
            <x-ui.button :href="route($indexRouteName, ['tab' => 'settled'])" variant="secondary" magnetic>Regles</x-ui.button>
        </div>
    </section>

    <section class="section">
        @if(($bets ?? null) && $bets->count())
            <x-ui.table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Match</th>
                    <th>Selection</th>
                    <th>Mise</th>
                    <th>Status</th>
                    <th>Payout</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bets as $bet)
                    <tr>
                        <td>{{ $bet->id }}</td>
                        <td>
                            {{ $bet->match->team_a_name ?? $bet->match->home_team }} vs {{ $bet->match->team_b_name ?? $bet->match->away_team }}
                            <br>
                            <x-ui.button :href="route($matchShowRouteName, $bet->match_id)" variant="outline" size="sm">Ouvrir match</x-ui.button>
                        </td>
                        <td>{{ $bet->prediction }}</td>
                        <td>{{ $bet->stake_points }}</td>
                        <td><span class="badge">{{ $bet->status }}</span></td>
                        <td>{{ (int) ($bet->settlement->payout_points ?? 0) }}</td>
                        <td>
                            @if(in_array($bet->status, [\App\Models\Bet::STATUS_PENDING, \App\Models\Bet::STATUS_PLACED], true))
                                <form method="POST" action="{{ route($cancelRouteName, $bet->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="idempotency_key" value="web-cancel-{{ $bet->id }}-{{ now()->timestamp }}">
                                    <x-ui.button type="submit" variant="danger" size="sm">Annuler</x-ui.button>
                                </form>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </x-ui.table>

            <div class="actions">{{ $bets->links() }}</div>
        @else
            <x-ui.empty-state title="Aucun pari a afficher" message="Placez un pari depuis la page matches pour le voir ici." />
        @endif
    </section>
@endsection
