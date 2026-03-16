@extends('marketing.layouts.template')

@section('title', 'Gestion des membres | ERAH Plateforme')
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
        'heroSubtitle' => 'Pilotage ERAH',
        'heroTitle' => 'Gestion des membres',
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
                                <div class="tt-form-group adm-form-cta" style="align-self:end;">
                                    <p class="adm-form-cta-copy">Filtrez la liste puis gerez les roles et les acces sans changer de page.</p>
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
                                <span>Points classement moyens</span>
                            </article>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Liste utilisateurs</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Le role, la progression et les soldes sont visibles au meme endroit.</p>
                        </div>

                        @if($usersCount)
                            <div class="adm-user-directory">
                                @foreach($users as $u)
                                    <article class="adm-user-card">
                                        <div class="adm-user-card-head">
                                            <div class="adm-user-card-title">
                                                <strong>{{ $u->name }}</strong>
                                                <span class="adm-meta">{{ $u->email }}</span>
                                            </div>

                                            <div class="adm-row-actions">
                                                <span class="adm-pill">ID #{{ $u->id }}</span>
                                                <span class="adm-pill">{{ $u->role }}</span>
                                                <span class="adm-pill">Ligue {{ $u->progress?->league?->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>

                                        <div class="adm-user-card-grid">
                                            <div class="adm-user-stat">
                                                <span class="adm-user-stat-title">Progression</span>
                                                <div class="adm-user-stat-value">
                                                    Points classement: {{ (int) ($u->progress?->total_rank_points ?? 0) }}<br>
                                                    XP: {{ (int) ($u->progress?->total_xp ?? 0) }}
                                                </div>
                                            </div>

                                            <div class="adm-user-stat">
                                                <span class="adm-user-stat-title">Soldes</span>
                                                <div class="adm-user-stat-value">
                                                    Paris: {{ (int) ($u->wallet?->balance ?? 0) }}<br>
                                                    Points plateforme: {{ (int) ($u->rewardWallet?->balance ?? 0) }}
                                                </div>
                                            </div>

                                            <div class="adm-user-stat">
                                                <span class="adm-user-stat-title">Moderation</span>
                                                <div class="adm-user-stat-value">Acces rapide au profil, aux favoris et a la mise a jour du role.</div>
                                            </div>
                                        </div>

                                        <div class="adm-user-card-actions">
                                            <div class="adm-row-actions">
                                                @php
                                                    $adminUserDetailUrl = auth()->user()?->role === 'admin'
                                                        ? route('admin.users.show', $u->id)
                                                        : route('users.index', array_filter(['user_id' => $u->id, 'q' => $search ?: null]));
                                                @endphp
                                                <a class="tt-btn tt-btn-outline tt-magnetic-item" href="{{ $adminUserDetailUrl }}">
                                                    <span data-hover="Voir">Voir</span>
                                                </a>
                                                <a class="tt-btn tt-btn-secondary tt-magnetic-item" href="{{ route('users.public', $u->id) }}" target="_blank" rel="noopener">
                                                    <span data-hover="Profil public">Profil public</span>
                                                </a>
                                            </div>

                                            @if(auth()->user()?->role === 'admin')
                                                <form method="POST" action="{{ route('users.role.update') }}" class="adm-inline-form">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $u->id }}">
                                                    <select name="role" class="adm-inline-select" data-lenis-prevent>
                                                        <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>Membre</option>
                                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                        <span data-hover="Mettre a jour">Mettre a jour</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $users->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun utilisateur pour ce filtre.</div>
                        @endif
                    </section>

                    <section class="adm-surface" id="user-focus">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Favoris utilisateur</h2>
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
                                <span class="adm-pill">Classement {{ (int) ($selectedUser->progress?->total_rank_points ?? 0) }}</span>
                                <span class="adm-pill">Solde paris {{ (int) ($selectedUser->wallet?->balance ?? 0) }}</span>
                                <span class="adm-pill">Points plateforme {{ (int) ($selectedUser->rewardWallet?->balance ?? 0) }}</span>
                                @if(auth()->user()?->role === 'admin')
                                    <a class="tt-btn tt-btn-secondary tt-magnetic-item" href="{{ route('admin.users.show', $selectedUser->id) }}">
                                        <span data-hover="Ouvrir le detail admin">Ouvrir le detail admin</span>
                                    </a>
                                @endif
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
