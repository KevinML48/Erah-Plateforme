@extends('layouts.app')

@section('title', 'Classement')

@section('content')
    <div class="page-shell">
        <section class="section page-hero">
            <span class="section-kicker">Progression competitive</span>
            <h1 class="page-title">Classement</h1>
            <p class="page-description">
                Suivez votre ligue actuelle, votre progression et la table des meilleurs profils sans passer par un ecran technique.
            </p>
        </section>

        <section class="section">
            <div class="metric-grid">
                <article class="metric-card">
                    <span>Ligue actuelle</span>
                    <strong>{{ $progress->league->name ?? 'Non definie' }}</strong>
                    <p>Votre rang visible est rattache a votre progression actuelle.</p>
                </article>
                <article class="metric-card">
                    <span>Score classement</span>
                    <strong>{{ (int) ($progress->total_rank_points ?? 0) }}</strong>
                    <p>Un repere utile pour departager les profils les plus actifs.</p>
                </article>
                <article class="metric-card">
                    <span>Experience totale</span>
                    <strong>{{ (int) ($progress->total_xp ?? 0) }} XP</strong>
                    <p>Votre progression cumulee sur les missions et activites deja prises en compte.</p>
                </article>
            </div>

            <div class="actions">
                <form method="GET" action="{{ route('ranking.index') }}" class="grid" style="width: 100%; max-width: 420px;">
                    <label for="league" class="meta">Afficher une ligue</label>
                    <select id="league" name="league" required>
                        @foreach($leagues as $league)
                            <option value="{{ $league->key }}" {{ $leagueKey === $league->key ? 'selected' : '' }}>
                                {{ $league->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="tt-btn tt-btn-primary">
                        <span data-hover="Mettre a jour">Mettre a jour la vue</span>
                    </button>
                </form>
            </div>
        </section>

        @if(auth()->user()->role === 'admin')
            <section class="section">
                <span class="section-kicker">Administration</span>
                <h2>Ajustement manuel</h2>
                <p class="page-description">Reserve ce bloc aux corrections ponctuelles. Les champs techniques restent disponibles, mais le libelle est clarifie pour l equipe admin.</p>

                <form method="POST" action="{{ route('ranking.grant') }}" class="grid grid-4">
                    @csrf
                    <div>
                        <label for="grant_user_id">Membre</label>
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
                        <label for="kind">Type d ajustement</label>
                        <select id="kind" name="kind" required>
                            <option value="rank" {{ old('kind', 'rank') === 'rank' ? 'selected' : '' }}>Score classement</option>
                            <option value="xp" {{ old('kind') === 'xp' ? 'selected' : '' }}>Experience</option>
                        </select>
                    </div>
                    <div>
                        <label for="amount">Montant</label>
                        <input id="amount" name="amount" type="number" min="1" step="1" value="{{ old('amount', 10) }}" required>
                    </div>
                    <div>
                        <label for="source_type">Reference technique</label>
                        <input id="source_type" name="source_type" value="{{ old('source_type', 'console.manual_grant') }}" required>
                    </div>
                    <div>
                        <label for="source_id">Identifiant de suivi</label>
                        <input id="source_id" name="source_id" value="{{ old('source_id', 'admin-'.auth()->id().'-'.now()->timestamp) }}" required>
                    </div>
                    <div class="actions">
                        <button type="submit" class="tt-btn tt-btn-primary">
                            <span data-hover="Enregistrer l ajustement">Enregistrer l ajustement</span>
                        </button>
                    </div>
                </form>
            </section>
        @endif

        <section class="section">
            <span class="section-kicker">Tableau des ligues</span>
            <h2>Classement visible</h2>
            @if(empty($leaderboard) || empty($leaderboard['entries']))
                <p class="meta">Aucune entree disponible pour cette ligue pour le moment.</p>
            @else
                <div class="table-wrap" data-responsive="cards">
                    <table>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Membre</th>
                            <th>Score</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($leaderboard['entries'] as $entry)
                            <tr>
                                <td data-label="#">{{ $entry['position'] }}</td>
                                <td data-label="Membre">{{ $entry['name'] }} @if($entry['user_id'] === auth()->id()) <strong>(vous)</strong> @endif</td>
                                <td data-label="Score">{{ $entry['total_rank_points'] ?? $entry['rank_points'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="section">
            <span class="section-kicker">Historique personnel</span>
            <h2>Derniers mouvements</h2>
            @if($recentTransactions->count())
                <div class="table-wrap" data-responsive="cards">
                    <table>
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Reference</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recentTransactions as $tx)
                            <tr>
                                <td data-label="Date">{{ optional($tx->created_at)->format('Y-m-d H:i') }}</td>
                                <td data-label="Type">{{ \Illuminate\Support\Str::headline((string) $tx->kind) }}</td>
                                <td data-label="Montant">{{ $tx->points }}</td>
                                <td data-label="Reference">{{ $tx->source_type }}#{{ $tx->source_id }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="meta">Aucun mouvement recent a afficher.</p>
            @endif
        </section>
    </div>
@endsection
