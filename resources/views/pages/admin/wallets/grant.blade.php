@extends('layouts.app')

@section('title', 'Admin wallets grant')

@section('content')
    <section class="section">
        <h1>Admin wallet grant (bet_points)</h1>

        <form method="GET" action="{{ route('admin.wallets.grant.create') }}" class="grid">
            <div>
                <label for="q">Recherche utilisateur</label>
                <input id="q" name="q" value="{{ $search ?? '' }}" placeholder="nom ou email">
            </div>
            <div class="actions">
                <button type="submit">Rechercher</button>
            </div>
        </form>
    </section>

    <section class="section">
        <h2>Formulaire grant</h2>
        <form method="POST" action="{{ route('admin.wallets.grant.store') }}" class="grid">
            @csrf

            <div>
                <label for="user_id">Utilisateur</label>
                <select id="user_id" name="user_id" required>
                    <option value="">-- choisir --</option>
                    @foreach($users ?? [] as $u)
                        <option value="{{ $u->id }}" {{ (string) old('user_id') === (string) $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }}) - solde {{ (int) ($u->wallet->balance ?? 0) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="amount">Montant</label>
                <input id="amount" name="amount" type="number" min="1" step="1" value="{{ old('amount', 100) }}" required>
            </div>

            <div>
                <label for="reason">Raison</label>
                <input id="reason" name="reason" value="{{ old('reason', 'manual_grant') }}" required>
            </div>

            <div>
                <label for="idempotency_key">Idempotency key</label>
                <input id="idempotency_key" name="idempotency_key" value="grant-{{ auth()->id() }}-{{ now()->timestamp }}" required>
            </div>

            <div class="actions">
                <button type="submit">Crediter wallet</button>
            </div>
        </form>
    </section>
@endsection
