@extends('layouts.app')

@section('title', 'Match detail')

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $placeBetRouteName = $isPublicApp ? 'app.matches.bets.store' : 'matches.bets.store';
        $betsRouteName = $isPublicApp ? 'app.bets.index' : 'bets.index';
        $cancelRouteName = $isPublicApp ? 'app.bets.cancel' : 'bets.cancel';
    @endphp

    <section class="section">
        <h1>Match #{{ $match->id }}</h1>
        <p><strong>{{ $match->team_a_name ?? $match->home_team }} vs {{ $match->team_b_name ?? $match->away_team }}</strong></p>
        <p class="meta">Status: {{ $match->status }} | Debut: {{ optional($match->starts_at)->format('Y-m-d H:i') }} | Lock: {{ optional($match->locked_at)->format('Y-m-d H:i') }}</p>
        <p class="meta">Bets count: {{ $match->bets_count ?? 0 }}</p>
    </section>

    <section class="section">
        <h2>Parier sur le vainqueur</h2>
        <p class="meta">Wallet: {{ $walletBalance ?? 0 }} bet_points</p>

        @if($isPublicApp && auth()->guest())
            <x-ui.button :href="route('login')" variant="primary" magnetic>Se connecter pour placer un pari</x-ui.button>
        @elseif($betIsOpen)
            <form method="POST" action="{{ route($placeBetRouteName, $match->id) }}" class="grid">
                @csrf

                <div>
                    <label>Selection</label>
                    @php($selectedKey = old('selection_key', ($options[0]['key'] ?? null)))
                    @foreach($options ?? [] as $option)
                        <label style="font-weight: normal; display: block; margin-bottom: 0.35rem;">
                            <input
                                type="radio"
                                name="selection_key"
                                value="{{ $option['key'] }}"
                                {{ $selectedKey === $option['key'] ? 'checked' : '' }}
                                required
                            >
                            {{ $option['label'] }} (odds {{ $option['odds'] }})
                        </label>
                    @endforeach
                </div>

                <div>
                    <label for="stake_points">Mise (bet_points)</label>
                    <input id="stake_points" name="stake_points" type="number" min="1" step="1" value="{{ old('stake_points', 100) }}" required>
                </div>

                <input type="hidden" name="idempotency_key" value="web-bet-{{ auth()->id() }}-{{ $match->id }}-{{ now()->timestamp }}">

                <div class="actions">
                    <x-ui.button type="submit" variant="primary" magnetic>Placer mon pari</x-ui.button>
                </div>
            </form>
        @else
            <p class="meta">Les paris sont fermes pour ce match.</p>
        @endif
    </section>

    <section class="section">
        <h2>Mon pari</h2>
        @if($myBet)
            <p>Statut: <span class="badge">{{ $myBet->status }}</span></p>
            <p>Selection: {{ $myBet->prediction }}</p>
            <p>Mise: {{ $myBet->stake_points }}</p>
            <p>Odds: {{ number_format((float) $myBet->odds_snapshot, 3) }}</p>
            @if($myBet->settlement)
                <p>Payout: {{ (int) $myBet->settlement->payout_points }}</p>
            @endif

            <div class="actions">
                <x-ui.button :href="route($betsRouteName)" variant="secondary" magnetic>Voir tous mes paris</x-ui.button>
                @if(in_array($myBet->status, [\App\Models\Bet::STATUS_PENDING, \App\Models\Bet::STATUS_PLACED], true))
                    <form method="POST" action="{{ route($cancelRouteName, $myBet->id) }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="idempotency_key" value="web-cancel-{{ $myBet->id }}-{{ now()->timestamp }}">
                        <x-ui.button type="submit" variant="danger">Annuler ce pari</x-ui.button>
                    </form>
                @endif
            </div>
        @else
            <p class="meta">
                @if($isPublicApp)
                    @guest
                        Le detail public n'affiche pas les paris personnels. Connectez-vous pour placer un pari.
                    @else
                        Aucun pari actif sur ce match.
                    @endguest
                @else
                    Aucun pari actif sur ce match.
                @endif
            </p>
        @endif
    </section>
@endsection
