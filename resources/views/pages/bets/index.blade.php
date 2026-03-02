@extends('layouts.app')

@section('title', 'Mes paris')

@section('content')
    <section class="section">
        <h1>Mes paris</h1>
        <div class="actions">
            <a class="button-link" href="{{ route('bets.index', ['tab' => 'active']) }}">En cours</a>
            <a class="button-link" href="{{ route('bets.index', ['tab' => 'settled']) }}">Regles</a>
        </div>
    </section>

    <section class="section">
        @if(($bets ?? null) && $bets->count())
            <div class="table-wrap">
                <table>
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
                                <a href="{{ route('matches.show', $bet->match_id) }}">Ouvrir match</a>
                            </td>
                            <td>{{ $bet->prediction }}</td>
                            <td>{{ $bet->stake_points }}</td>
                            <td><span class="badge">{{ $bet->status }}</span></td>
                            <td>{{ (int) ($bet->settlement->payout_points ?? 0) }}</td>
                            <td>
                                @if(in_array($bet->status, [\App\Models\Bet::STATUS_PENDING, \App\Models\Bet::STATUS_PLACED], true))
                                    <form method="POST" action="{{ route('bets.cancel', $bet->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="idempotency_key" value="web-cancel-{{ $bet->id }}-{{ now()->timestamp }}">
                                        <button type="submit">Annuler</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="actions">{{ $bets->links() }}</div>
        @else
            <p class="meta">Aucun pari a afficher.</p>
        @endif
    </section>
@endsection
