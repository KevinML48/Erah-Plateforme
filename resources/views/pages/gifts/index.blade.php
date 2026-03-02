@extends('layouts.app')

@section('title', 'Cadeaux')

@section('content')
    <section class="section">
        <h1>Cadeaux</h1>
        <p>Solde reward_points: <strong>{{ (int) ($wallet->balance ?? 0) }}</strong></p>
        <div class="actions">
            <a class="button-link" href="{{ route('gifts.redemptions') }}">Mes redemptions</a>
            <a class="button-link" href="{{ route('gifts.wallet') }}">Wallet reward</a>
        </div>
    </section>

    <section class="section">
        <h2>Catalogue</h2>
        @if(($gifts ?? null) && $gifts->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Cout</th>
                            <th>Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($gifts as $gift)
                        <tr>
                            <td>{{ $gift->title }}</td>
                            <td>{{ $gift->cost_points }}</td>
                            <td>{{ $gift->stock }}</td>
                            <td><a href="{{ route('gifts.show', $gift->id) }}">Voir</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="actions">{{ $gifts->links() }}</div>
        @else
            <p class="meta">Aucun cadeau actif.</p>
        @endif
    </section>

    <section class="section">
        <h2>Mes dernieres redemptions</h2>
        @if(($recentRedemptions ?? null) && $recentRedemptions->count())
            <ul>
                @foreach($recentRedemptions as $redemption)
                    <li>
                        {{ optional($redemption->requested_at)->format('Y-m-d H:i') }} -
                        {{ $redemption->gift->title ?? 'Gift' }} -
                        <span class="badge">{{ $redemption->status }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="meta">Aucune redemption recente.</p>
        @endif
    </section>
@endsection
