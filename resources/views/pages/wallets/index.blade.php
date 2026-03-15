@extends('layouts.app')

@section('title', 'Centre des points')

@section('content')
    <div class="page-shell">
        <section class="section page-hero">
            <span class="section-kicker">Soldes et historique</span>
            <h1 class="page-title">Centre des points</h1>
            <p class="page-description">
                Cette vue rassemble les soldes encore utiles a l exploitation et les mouvements visibles sur votre compte. Le solde plateforme reste le repère principal pour l utilisateur.
            </p>

            <div class="metric-grid">
                <article class="metric-card">
                    <span>Points plateforme</span>
                    <strong>{{ (int) ($rewardWallet->balance ?? 0) }}</strong>
                    <p>Solde principal utilise pour les cadeaux, les usages plateforme et les ajustements recents.</p>
                </article>
                <article class="metric-card">
                    <span>Solde paris historique</span>
                    <strong>{{ (int) ($betWallet->balance ?? 0) }}</strong>
                    <p>Repere conserve pour les anciens flux de mise et les operations de compatibilite.</p>
                </article>
                <article class="metric-card">
                    <span>Lecture rapide</span>
                    <strong>{{ auth()->user()->role === 'admin' ? 'Admin' : 'Membre' }}</strong>
                    <p>{{ auth()->user()->role === 'admin' ? 'Les formulaires d\'ajustement restent disponibles ci-dessous.' : 'Vous consultez ici vos mouvements et vos soldes visibles.' }}</p>
                </article>
            </div>
        </section>

        @if(auth()->user()->role === 'admin')
            <section class="section">
                <span class="section-kicker">Administration</span>
                <h2>Ajuster un solde</h2>
                <p class="page-description">La recherche et les formulaires restent en place, mais les libelles sont clarifies pour eviter les termes trop bruts cote produit.</p>

                <form method="GET" action="{{ route('wallets.index') }}" class="grid" style="max-width: 420px;">
                    <label for="q">Rechercher un membre</label>
                    <input id="q" name="q" value="{{ $search ?? '' }}" placeholder="Nom ou email">
                    <button type="submit" class="tt-btn tt-btn-primary">
                        <span data-hover="Filtrer">Filtrer</span>
                    </button>
                </form>

                <div class="surface-grid">
                    <form method="POST" action="{{ route('wallets.grant-bet') }}" class="surface-card grid">
                        @csrf
                        <span class="surface-card-title">Compatibilite paris</span>
                        <h3>Crediter le solde paris historique</h3>
                        <div>
                            <label>Membre</label>
                            <select name="user_id" required>
                                <option value="">-- choisir --</option>
                                @foreach($grantUsers as $u)
                                    <option value="{{ $u->id }}">
                                        #{{ $u->id }} - {{ $u->name }} (solde {{ (int) ($u->wallet?->balance ?? 0) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Montant</label>
                            <input name="amount" type="number" min="1" step="1" value="100" required>
                        </div>
                        <div>
                            <label>Motif</label>
                            <input name="reason" value="console_grant" required>
                        </div>
                        <div>
                            <label>Cle idempotente</label>
                            <input name="idempotency_key" value="bet-grant-{{ auth()->id() }}-{{ now()->timestamp }}" required>
                        </div>
                        <div class="actions">
                            <button type="submit" class="tt-btn tt-btn-primary">
                                <span data-hover="Crediter le solde paris">Crediter le solde paris</span>
                            </button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('wallets.grant-reward') }}" class="surface-card grid">
                        @csrf
                        <span class="surface-card-title">Points plateforme</span>
                        <h3>Crediter les points plateforme</h3>
                        <div>
                            <label>Membre</label>
                            <select name="user_id" required>
                                <option value="">-- choisir --</option>
                                @foreach($grantUsers as $u)
                                    <option value="{{ $u->id }}">
                                        #{{ $u->id }} - {{ $u->name }} (solde {{ (int) ($u->rewardWallet?->balance ?? 0) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Montant</label>
                            <input name="amount" type="number" min="1" step="1" value="100" required>
                        </div>
                        <div>
                            <label>Motif</label>
                            <input name="reason" value="console_grant" required>
                        </div>
                        <div>
                            <label>Cle idempotente</label>
                            <input name="idempotency_key" value="reward-grant-{{ auth()->id() }}-{{ now()->timestamp }}" required>
                        </div>
                        <div class="actions">
                            <button type="submit" class="tt-btn tt-btn-primary">
                                <span data-hover="Crediter les points">Crediter les points</span>
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        @endif

        <section class="section">
            <span class="section-kicker">Compatibilite paris</span>
            <h2>Derniers mouvements du solde paris</h2>
            @if($betTransactions->count())
                <div class="table-wrap" data-responsive="cards">
                    <table>
                        <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Solde apres</th><th>Reference</th></tr></thead>
                        <tbody>
                        @foreach($betTransactions as $tx)
                            <tr>
                                <td data-label="Date">{{ optional($tx->created_at)->format('Y-m-d H:i') }}</td>
                                <td data-label="Type">{{ \Illuminate\Support\Str::headline((string) $tx->type) }}</td>
                                <td data-label="Montant">{{ $tx->amount }}</td>
                                <td data-label="Solde apres">{{ $tx->balance_after }}</td>
                                <td data-label="Reference">{{ $tx->ref_type }}#{{ $tx->ref_id }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="meta">Aucun mouvement recent sur le solde paris.</p>
            @endif
        </section>

        <section class="section">
            <span class="section-kicker">Solde principal</span>
            <h2>Derniers mouvements des points plateforme</h2>
            @if($rewardTransactions->count())
                <div class="table-wrap" data-responsive="cards">
                    <table>
                        <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Solde apres</th><th>Reference</th></tr></thead>
                        <tbody>
                        @foreach($rewardTransactions as $tx)
                            <tr>
                                <td data-label="Date">{{ optional($tx->created_at)->format('Y-m-d H:i') }}</td>
                                <td data-label="Type">{{ \Illuminate\Support\Str::headline((string) $tx->type) }}</td>
                                <td data-label="Montant">{{ $tx->amount }}</td>
                                <td data-label="Solde apres">{{ $tx->balance_after }}</td>
                                <td data-label="Reference">{{ $tx->ref_type }}#{{ $tx->ref_id }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="meta">Aucun mouvement recent sur les points plateforme.</p>
            @endif
        </section>
    </div>
@endsection
