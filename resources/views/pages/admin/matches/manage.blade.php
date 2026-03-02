@extends('layouts.app')

@section('title', 'Manage match')

@section('content')
    <section class="section">
        <h1>Manage match #{{ $match->id }}</h1>
        <p><strong>{{ $match->team_a_name ?? $match->home_team }} vs {{ $match->team_b_name ?? $match->away_team }}</strong></p>
        <p class="meta">Status: {{ $match->status }} | Resultat: {{ $match->result ?: '-' }}</p>
        <p class="meta">Starts at: {{ optional($match->starts_at)->format('Y-m-d H:i') }} | Lock: {{ optional($match->locked_at)->format('Y-m-d H:i') }}</p>

        <div class="actions">
            <a class="button-link" href="{{ route('admin.matches.edit', $match->id) }}">Edit</a>
            <a class="button-link" href="{{ route('matches.show', $match->id) }}">Voir cote user</a>
            <a class="button-link" href="{{ route('admin.matches.index') }}">Retour</a>
        </div>
    </section>

    <section class="section">
        <h2>Changer statut</h2>
        <form method="POST" action="{{ route('admin.matches.status', $match->id) }}" class="grid">
            @csrf
            <div>
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    @foreach($statuses as $statusValue)
                        <option value="{{ $statusValue }}" {{ $match->status === $statusValue ? 'selected' : '' }}>{{ $statusValue }}</option>
                    @endforeach
                </select>
            </div>
            <div class="actions">
                <button type="submit">Mettre a jour statut</button>
            </div>
        </form>
    </section>

    <section class="section">
        <h2>Definir resultat</h2>
        <form method="POST" action="{{ route('admin.matches.result', $match->id) }}" class="grid">
            @csrf
            <div>
                <label for="result">Resultat</label>
                <select id="result" name="result" required>
                    @foreach($resultOptions as $resultValue => $resultLabel)
                        <option value="{{ $resultValue }}" {{ $match->result === $resultValue ? 'selected' : '' }}>{{ $resultLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="actions">
                <button type="submit">Appliquer resultat</button>
            </div>
        </form>
    </section>

    <section class="section">
        <h2>Settlement idempotent</h2>
        <form method="POST" action="{{ route('admin.matches.settle', $match->id) }}" class="grid">
            @csrf
            <div>
                <label for="settle_result">Resultat settlement</label>
                <select id="settle_result" name="result" required>
                    @foreach($resultOptions as $resultValue => $resultLabel)
                        <option value="{{ $resultValue }}">{{ $resultLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="idempotency_key">Idempotency key</label>
                <input id="idempotency_key" name="idempotency_key" value="settle-{{ $match->id }}-{{ now()->timestamp }}" required>
            </div>

            <div class="actions">
                <button type="submit">Executer settlement</button>
            </div>
        </form>
    </section>

    <section class="section">
        <h2>Derniers bets (30)</h2>
        @if($match->bets->count())
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Bet ID</th>
                        <th>User</th>
                        <th>Prediction</th>
                        <th>Stake</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($match->bets as $bet)
                        <tr>
                            <td>{{ $bet->id }}</td>
                            <td>{{ $bet->user->name ?? 'N/A' }}</td>
                            <td>{{ $bet->prediction }}</td>
                            <td>{{ $bet->stake_points }}</td>
                            <td>{{ $bet->status }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="meta">Aucun bet sur ce match.</p>
        @endif
    </section>
@endsection
