@extends('layouts.app')

@section('title', $gift->title ?? 'Gift')

@section('content')
    <section class="section">
        <h1>{{ $gift->title }}</h1>
        <p>{{ $gift->description }}</p>
        <p>Cout: <strong>{{ $gift->cost_points }}</strong> reward_points</p>
        <p>Stock: {{ $gift->stock }}</p>
        <p>Solde wallet: {{ (int) ($wallet->balance ?? 0) }}</p>

        <form method="POST" action="{{ route('gifts.redeem', $gift->id) }}" class="actions">
            @csrf
            <input type="hidden" name="idempotency_key" value="redeem-{{ auth()->id() }}-{{ $gift->id }}-{{ now()->timestamp }}">
            <button type="submit" {{ !$gift->is_active || $gift->stock < 1 ? 'disabled' : '' }}>Redeem</button>
            <a class="button-link" href="{{ route('gifts.index') }}">Retour catalogue</a>
        </form>
    </section>

    <section class="section">
        <h2>Mes redemptions pour ce cadeau</h2>
        @if(($myRecentRedemptions ?? null) && $myRecentRedemptions->count())
            <ul>
                @foreach($myRecentRedemptions as $redemption)
                    <li>
                        {{ optional($redemption->requested_at)->format('Y-m-d H:i') }} -
                        <span class="badge">{{ $redemption->status }}</span>
                        @if($redemption->reason)
                            <br><span class="meta">Raison: {{ $redemption->reason }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @else
            <p class="meta">Aucune redemption pour ce cadeau.</p>
        @endif
    </section>
@endsection
