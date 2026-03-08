@extends('layouts.app')

@section('title', 'Mes paris')

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.bets.index' : 'bets.index';
        $matchShowRouteName = $isPublicApp ? 'app.matches.show' : 'matches.show';
        $cancelRouteName = $isPublicApp ? 'app.bets.cancel' : 'bets.cancel';
        $matchLabelResolver = $matchLabelResolver ?? null;
    @endphp

    <section class="section">
        <h1>Mes paris</h1>
        <div class="actions">
            <x-ui.button :href="route($indexRouteName, ['tab' => 'active'])" variant="secondary" magnetic>Ouverts</x-ui.button>
            <x-ui.button :href="route($indexRouteName, ['tab' => 'settled'])" variant="secondary" magnetic>Termines</x-ui.button>
        </div>
    </section>

    <section class="section">
        @if(($bets ?? null) && $bets->count())
            <x-ui.table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Match</th>
                    <th>Choix</th>
                    <th>Mise</th>
                    <th>Etat</th>
                    <th>Gain</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bets as $bet)
                    @php
                        $betMarket = $bet->match?->markets->firstWhere('key', $bet->market_key);
                        $betSelection = $betMarket?->selections->firstWhere('key', $bet->selection_key);
                        $selectionLabel = $betSelection?->label ?? $bet->selection_key ?? $bet->prediction;
                    @endphp
                    <tr>
                        <td data-label="ID">{{ $bet->id }}</td>
                        <td data-label="Match">
                            {{ $bet->match->displayTitle() }}
                            <br>
                            <x-ui.button :href="route($matchShowRouteName, $bet->match_id)" variant="outline" size="sm">Ouvrir match</x-ui.button>
                        </td>
                        <td data-label="Choix">{{ $matchLabelResolver->labelForMarketKey($bet->market_key) }} / {{ $selectionLabel }}</td>
                        <td data-label="Mise">{{ $bet->stake_points }}</td>
                        <td data-label="Etat"><span class="badge">{{ $matchLabelResolver->labelForBetStatus($bet->status) }}</span></td>
                        <td data-label="Gain">{{ (int) ($bet->settlement->payout_points ?? 0) }}</td>
                        <td data-label="Actions">
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
            <x-ui.empty-state title="Aucun pari a afficher" message="Placez un pronostic depuis la page matchs pour le voir ici." />
        @endif
    </section>
@endsection
