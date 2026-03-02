@extends('layouts.app')

@section('title', 'Match detail')

@section('content')
    <section class="section">
        <h1>Match #{{ $match->id }}</h1>
        <p><strong>{{ $match->team_a_name ?? $match->home_team }} vs {{ $match->team_b_name ?? $match->away_team }}</strong></p>
        <p class="meta">Status: {{ $match->status }} | Debut: {{ optional($match->starts_at)->format('Y-m-d H:i') }} | Lock: {{ optional($match->locked_at)->format('Y-m-d H:i') }}</p>
        <p class="meta">Bets count: {{ $match->bets_count ?? 0 }}</p>
    </section>

    <section class="section">
        <h2>Parier sur le vainqueur</h2>
        <p class="meta">Wallet: {{ $walletBalance ?? 0 }} bet_points</p>

        @if($betIsOpen)
            <form method="POST" action="{{ route('matches.bets.store', $match->id) }}" class="grid">
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
                    <button type="submit">Placer mon pari</button>
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
                <a class="button-link" href="{{ route('bets.index') }}">Voir tous mes paris</a>
                @if(in_array($myBet->status, [\App\Models\Bet::STATUS_PENDING, \App\Models\Bet::STATUS_PLACED], true))
                    <form method="POST" action="{{ route('bets.cancel', $myBet->id) }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="idempotency_key" value="web-cancel-{{ $myBet->id }}-{{ now()->timestamp }}">
                        <button type="submit">Annuler ce pari</button>
                    </form>
                @endif
            </div>
        @else
            <p class="meta">Aucun pari actif sur ce match.</p>
        @endif
    </section>
@endsection
