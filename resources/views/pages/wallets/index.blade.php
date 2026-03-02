@extends('layouts.app')

@section('title', 'Wallets')

@section('content')
    <section class="section">
        <h1>Wallets</h1>
        <div class="grid grid-3">
            <div>
                <h3>bet_points</h3>
                <p><strong>{{ (int) ($betWallet->balance ?? 0) }}</strong></p>
            </div>
            <div>
                <h3>reward_points</h3>
                <p><strong>{{ (int) ($rewardWallet->balance ?? 0) }}</strong></p>
            </div>
        </div>
    </section>

    @if(auth()->user()->role === 'admin')
        <section class="section">
            <h2>Admin grants</h2>
            <form method="GET" action="{{ route('wallets.index') }}" class="inline-form">
                <label for="q">Recherche user</label>
                <input id="q" name="q" value="{{ $search ?? '' }}" placeholder="Nom ou email">
                <button type="submit">Filtrer</button>
            </form>

            <div class="grid grid-2">
                <form method="POST" action="{{ route('wallets.grant-bet') }}" class="grid">
                    @csrf
                    <h3>Grant bet_points</h3>
                    <div>
                        <label>User</label>
                        <select name="user_id" required>
                            <option value="">-- choisir --</option>
                            @foreach($grantUsers as $u)
                                <option value="{{ $u->id }}">
                                    #{{ $u->id }} - {{ $u->name }} (bet {{ (int) ($u->wallet?->balance ?? 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Amount</label>
                        <input name="amount" type="number" min="1" step="1" value="100" required>
                    </div>
                    <div>
                        <label>Reason</label>
                        <input name="reason" value="console_grant" required>
                    </div>
                    <div>
                        <label>Idempotency key</label>
                        <input name="idempotency_key" value="bet-grant-{{ auth()->id() }}-{{ now()->timestamp }}" required>
                    </div>
                    <div class="actions"><button type="submit">Crediter bet_points</button></div>
                </form>

                <form method="POST" action="{{ route('wallets.grant-reward') }}" class="grid">
                    @csrf
                    <h3>Grant reward_points</h3>
                    <div>
                        <label>User</label>
                        <select name="user_id" required>
                            <option value="">-- choisir --</option>
                            @foreach($grantUsers as $u)
                                <option value="{{ $u->id }}">
                                    #{{ $u->id }} - {{ $u->name }} (reward {{ (int) ($u->rewardWallet?->balance ?? 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Amount</label>
                        <input name="amount" type="number" min="1" step="1" value="100" required>
                    </div>
                    <div>
                        <label>Reason</label>
                        <input name="reason" value="console_grant" required>
                    </div>
                    <div>
                        <label>Idempotency key</label>
                        <input name="idempotency_key" value="reward-grant-{{ auth()->id() }}-{{ now()->timestamp }}" required>
                    </div>
                    <div class="actions"><button type="submit">Crediter reward_points</button></div>
                </form>
            </div>
        </section>
    @endif

    <section class="section">
        <h2>Dernieres transactions bet_points</h2>
        @if($betTransactions->count())
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Solde apres</th><th>Ref</th></tr></thead>
                    <tbody>
                    @foreach($betTransactions as $tx)
                        <tr>
                            <td>{{ optional($tx->created_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $tx->type }}</td>
                            <td>{{ $tx->amount }}</td>
                            <td>{{ $tx->balance_after }}</td>
                            <td>{{ $tx->ref_type }}#{{ $tx->ref_id }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="meta">Aucune transaction bet_points.</p>
        @endif
    </section>

    <section class="section">
        <h2>Dernieres transactions reward_points</h2>
        @if($rewardTransactions->count())
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Solde apres</th><th>Ref</th></tr></thead>
                    <tbody>
                    @foreach($rewardTransactions as $tx)
                        <tr>
                            <td>{{ optional($tx->created_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $tx->type }}</td>
                            <td>{{ $tx->amount }}</td>
                            <td>{{ $tx->balance_after }}</td>
                            <td>{{ $tx->ref_type }}#{{ $tx->ref_id }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="meta">Aucune transaction reward_points.</p>
        @endif
    </section>
@endsection

