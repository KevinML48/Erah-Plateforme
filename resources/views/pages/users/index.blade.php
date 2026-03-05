@extends('marketing.layouts.template')

@section('title', 'Console Users | ERAH Plateforme')
@section('meta_description', 'Gestion des utilisateurs, roles et progression globale.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $search = $search ?? '';
        $users = $users ?? collect();
        $selectedUser = $selectedUser ?? null;
        $usersCount = $users->count();
        $adminsCount = $users->where('role', 'admin')->count();
        $avgRankPoints = $usersCount > 0
            ? (int) round($users->sum(fn ($u) => (int) ($u->progress?->total_rank_points ?? 0)) / $usersCount)
            : 0;
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Console Users',
        'heroDescription' => 'Vue centralisee des comptes, roles et progression de la communaute.',
        'heroMaskDescription' => 'Gestion utilisateurs et roles.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Recherche et vue globale</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Filtrez les utilisateurs et accedez rapidement aux actions de moderation.</p>
                        </div>

                        <form method="GET" action="{{ route('users.index') }}" class="tt-form tt-form-creative adm-form">
                            <div class="adm-form-grid">
                                <div class="tt-form-group">
                                    <label for="q">Nom ou email</label>
                                    <input id="q" name="q" class="tt-form-control" value="{{ $search }}" placeholder="Ex: admin, erah@...">
                                </div>
                                <div class="tt-form-group" style="align-self:end;">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Filtrer">Filtrer</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="adm-compact-kpis margin-top-20">
                            <article class="adm-compact-kpi">
                                <strong>{{ $usersCount }}</strong>
                                <span>Utilisateurs listes</span>
                            </article>
                            <article class="adm-compact-kpi">
                                <strong>{{ $adminsCount }}</strong>
                                <span>Admins visibles</span>
                            </article>
                            <article class="adm-compact-kpi">
                                <strong>{{ $avgRankPoints }}</strong>
                                <span>Rank points moyen</span>
                            </article>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Liste utilisateurs</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Le role, la progression et les soldes sont visibles au meme endroit.</p>
                        </div>

                        @if($usersCount)
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Utilisateur</th>
                                            <th>Role</th>
                                            <th>Progression</th>
                                            <th>Wallets</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $u)
                                            <tr>
                                                <td>#{{ $u->id }}</td>
                                                <td>
                                                    <strong>{{ $u->name }}</strong><br>
                                                    <span class="adm-meta">{{ $u->email }}</span>
                                                </td>
                                                <td><span class="adm-pill">{{ $u->role }}</span></td>
                                                <td>
                                                    Ligue: {{ $u->progress?->league?->name ?? 'N/A' }}<br>
                                                    Rank: {{ (int) ($u->progress?->total_rank_points ?? 0) }}<br>
                                                    XP: {{ (int) ($u->progress?->total_xp ?? 0) }}
                                                </td>
                                                <td>
                                                    Bet: {{ (int) ($u->wallet?->balance ?? 0) }}<br>
                                                    Reward: {{ (int) ($u->rewardWallet?->balance ?? 0) }}
                                                </td>
                                                <td>
                                                    <div class="adm-row-actions">
                                                        <a class="tt-btn tt-btn-outline tt-magnetic-item" href="{{ route('users.index', array_filter(['user_id' => $u->id, 'q' => $search ?: null])) }}">
                                                            <span data-hover="Voir">Voir</span>
                                                        </a>
                                                        <a class="tt-btn tt-btn-secondary tt-magnetic-item" href="{{ route('users.public', $u->id) }}" target="_blank" rel="noopener">
                                                            <span data-hover="Profil public">Profil public</span>
                                                        </a>
                                                    </div>

                                                    @if(auth()->user()?->role === 'admin')
                                                        <form method="POST" action="{{ route('users.role.update') }}" class="adm-inline-form margin-top-10">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $u->id }}">
                                                            <select name="role" class="adm-inline-select" data-lenis-prevent>
                                                                <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>user</option>
                                                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>admin</option>
                                                            </select>
                                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                                <span data-hover="MAJ role">MAJ role</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="adm-pagin">{{ $users->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun utilisateur pour ce filtre.</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Focus utilisateur</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Selectionnez "Voir" pour afficher un resume rapide du compte.</p>
                        </div>

                        @if($selectedUser)
                            <div class="adm-row-actions margin-bottom-10">
                                <span class="adm-pill">ID #{{ $selectedUser->id }}</span>
                                <span class="adm-pill">{{ $selectedUser->name }}</span>
                                <span class="adm-pill">{{ $selectedUser->email }}</span>
                                <span class="adm-pill">{{ $selectedUser->role }}</span>
                                <span class="adm-pill">Ligue {{ $selectedUser->progress?->league?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="adm-row-actions">
                                <span class="adm-pill">XP {{ (int) ($selectedUser->progress?->total_xp ?? 0) }}</span>
                                <span class="adm-pill">Rank {{ (int) ($selectedUser->progress?->total_rank_points ?? 0) }}</span>
                                <span class="adm-pill">Bet {{ (int) ($selectedUser->wallet?->balance ?? 0) }}</span>
                                <span class="adm-pill">Reward {{ (int) ($selectedUser->rewardWallet?->balance ?? 0) }}</span>
                            </div>
                        @else
                            <div class="adm-empty">Aucun utilisateur selectionne.</div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
