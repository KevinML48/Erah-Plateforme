@extends('layouts.app')

@section('title', 'Ranking')

@section('content')
    <section class="section">
        <h1>Ranking</h1>
        <div class="grid grid-3">
            <div>
                <h3>Mon etat</h3>
                <p>Ligue: <strong>{{ $progress->league->name ?? 'N/A' }}</strong></p>
                <p>Rank points: {{ $progress->total_rank_points ?? 0 }}</p>
                <p>XP total: {{ $progress->total_xp ?? 0 }}</p>
            </div>
            <div>
                <h3>Filtre leaderboard</h3>
                <form method="GET" action="{{ route('ranking.index') }}" class="inline-form">
                    <select name="league" required>
                        @foreach($leagues as $league)
                            <option value="{{ $league->key }}" {{ $leagueKey === $league->key ? 'selected' : '' }}>
                                {{ $league->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit">Afficher</button>
                </form>
            </div>
        </div>
    </section>

    @if(auth()->user()->role === 'admin')
        <section class="section">
            <h2>Admin grant points</h2>
            <form method="POST" action="{{ route('ranking.grant') }}" class="grid grid-4">
                @csrf
                <div>
                    <label for="grant_user_id">User</label>
                    <select id="grant_user_id" name="user_id" required>
                        <option value="">-- choisir --</option>
                        @foreach($grantUsers as $u)
                            <option value="{{ $u->id }}" {{ (string) old('user_id') === (string) $u->id ? 'selected' : '' }}>
                                #{{ $u->id }} - {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="kind">Type</label>
                    <select id="kind" name="kind" required>
                        <option value="rank" {{ old('kind', 'rank') === 'rank' ? 'selected' : '' }}>rank</option>
                        <option value="xp" {{ old('kind') === 'xp' ? 'selected' : '' }}>xp</option>
                    </select>
                </div>
                <div>
                    <label for="amount">Montant</label>
                    <input id="amount" name="amount" type="number" min="1" step="1" value="{{ old('amount', 10) }}" required>
                </div>
                <div>
                    <label for="source_type">source_type</label>
                    <input id="source_type" name="source_type" value="{{ old('source_type', 'console.manual_grant') }}" required>
                </div>
                <div>
                    <label for="source_id">source_id</label>
                    <input id="source_id" name="source_id" value="{{ old('source_id', 'admin-'.auth()->id().'-'.now()->timestamp) }}" required>
                </div>
                <div class="actions">
                    <button type="submit">Accorder points</button>
                </div>
            </form>
        </section>
    @endif

    <section class="section">
        <h2>Leaderboard</h2>
        @if(empty($leaderboard) || empty($leaderboard['entries']))
            <p class="meta">Aucune entree.</p>
        @else
            <div class="table-wrap" data-responsive="cards">
                <table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leaderboard['entries'] as $entry)
                        <tr>
                            <td data-label="#">{{ $entry['position'] }}</td>
                            <td data-label="User">{{ $entry['name'] }} @if($entry['user_id'] === auth()->id()) <strong>(vous)</strong> @endif</td>
                            <td data-label="Points">{{ $entry['total_rank_points'] ?? $entry['rank_points'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="section">
        <h2>Debug transactions recentes (moi)</h2>
        @if($recentTransactions->count())
            <div class="table-wrap" data-responsive="cards">
                <table>
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Kind</th>
                        <th>Points</th>
                        <th>Source</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($recentTransactions as $tx)
                        <tr>
                            <td data-label="Date">{{ optional($tx->created_at)->format('Y-m-d H:i') }}</td>
                            <td data-label="Kind">{{ $tx->kind }}</td>
                            <td data-label="Points">{{ $tx->points }}</td>
                            <td data-label="Source">{{ $tx->source_type }}#{{ $tx->source_id }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="meta">Aucune transaction.</p>
        @endif
    </section>
@endsection
