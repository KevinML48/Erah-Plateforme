@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <section class="section">
        <h1>Users</h1>
        <p class="meta">Liste des utilisateurs et controle du role (admin).</p>

        <form method="GET" action="{{ route('users.index') }}" class="grid grid-3">
            <div>
                <label for="q">Recherche</label>
                <input id="q" name="q" value="{{ $search ?? '' }}" placeholder="Nom ou email">
            </div>
            <div class="actions">
                <button type="submit">Filtrer</button>
            </div>
        </form>
    </section>

    <section class="section">
        <h2>Liste</h2>
        @if(($users ?? null) && $users->count())
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Progress</th>
                        <th>Wallets</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>
                                <strong>{{ $u->name }}</strong><br>
                                <span class="meta">{{ $u->email }}</span>
                            </td>
                            <td><span class="badge">{{ $u->role }}</span></td>
                            <td>
                                League: {{ $u->progress?->league?->name ?? 'N/A' }}<br>
                                rank: {{ $u->progress?->total_rank_points ?? 0 }}<br>
                                xp: {{ $u->progress?->total_xp ?? 0 }}
                            </td>
                            <td>
                                bet: {{ (int) ($u->wallet?->balance ?? 0) }}<br>
                                reward: {{ (int) ($u->rewardWallet?->balance ?? 0) }}
                            </td>
                            <td>
                                <div class="actions">
                                    <a class="button-link" href="{{ route('users.index', array_filter(['user_id' => $u->id, 'q' => $search ?? null])) }}">Voir</a>
                                    <a class="button-link" href="{{ route('users.public', $u->id) }}">Public</a>
                                </div>
                                @if(auth()->user()->role === 'admin')
                                    <form method="POST" action="{{ route('users.role.update') }}" class="inline-form">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $u->id }}">
                                        <select name="role">
                                            <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>user</option>
                                            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>admin</option>
                                        </select>
                                        <button type="submit">MAJ role</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="actions">{{ $users->links() }}</div>
        @else
            <p class="meta">Aucun utilisateur.</p>
        @endif
    </section>

    <section class="section">
        <h2>Debug / User selection</h2>
        @if($selectedUser)
            <pre class="json-box">{{ json_encode($selectedUser->toArray(), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
        @else
            <p class="meta">Selectionnez un utilisateur via "Voir".</p>
        @endif
    </section>
@endsection

