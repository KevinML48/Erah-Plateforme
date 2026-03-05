@extends('marketing.layouts.template')

@section('title', 'Admin Wallets | ERAH Plateforme')
@section('meta_description', 'Attribution manuelle de points wallet en console admin.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $search = $search ?? '';
        $users = collect($users ?? []);
        $usersCount = $users->count();
        $totalBalance = (int) $users->sum(fn ($user) => (int) ($user->wallet->balance ?? 0));
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Admin Wallets',
        'heroDescription' => 'Recherche, selection et credit manuel des wallets bet_points.',
        'heroMaskDescription' => 'Operations auditees avec idempotency key.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Recherche utilisateur</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Filtrez par nom ou email avant de crediter un wallet.</p>
                        </div>

                        <form method="GET" action="{{ route('admin.wallets.grant.create') }}" class="tt-form tt-form-creative adm-form">
                            <div class="adm-form-grid">
                                <div class="tt-form-group">
                                    <label for="q">Nom ou email</label>
                                    <input class="tt-form-control" id="q" name="q" value="{{ $search }}" placeholder="Ex: erah, admin@...">
                                </div>

                                <div class="tt-form-group" style="align-self:end;">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Rechercher">Rechercher</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="adm-compact-kpis margin-top-20">
                            <article class="adm-compact-kpi">
                                <strong>{{ $usersCount }}</strong>
                                <span>Utilisateurs trouves</span>
                            </article>
                            <article class="adm-compact-kpi">
                                <strong>{{ $totalBalance }}</strong>
                                <span>Solde cumule</span>
                            </article>
                            <article class="adm-compact-kpi">
                                <strong>{{ auth()->user()?->name }}</strong>
                                <span>Operateur</span>
                            </article>
                        </div>
                    </section>

                    <div class="adm-sub-grid">
                        <section class="adm-surface">
                            <div class="tt-heading tt-heading-lg margin-bottom-20">
                                <h2 class="tt-heading-title tt-text-reveal">Crediter un wallet</h2>
                                <p class="max-width-700 tt-anim-fadeinup text-gray">Selectionnez un utilisateur, definissez le montant puis validez.</p>
                            </div>

                            <form method="POST" action="{{ route('admin.wallets.grant.store') }}" class="tt-form tt-form-creative adm-form">
                                @csrf

                                <div class="tt-form-group">
                                    <label for="user_id">Utilisateur cible</label>
                                    <select class="tt-form-control" id="user_id" name="user_id" required data-lenis-prevent>
                                        <option value="">-- choisir --</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ (string) old('user_id') === (string) $u->id ? 'selected' : '' }}>
                                                {{ $u->name }} ({{ $u->email }}) - solde {{ (int) ($u->wallet->balance ?? 0) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="adm-form-grid">
                                    <div class="tt-form-group">
                                        <label for="amount">Montant</label>
                                        <input class="tt-form-control" id="amount" name="amount" type="number" min="1" step="1" value="{{ old('amount', 100) }}" required>
                                    </div>

                                    <div class="tt-form-group">
                                        <label for="reason">Raison</label>
                                        <input class="tt-form-control" id="reason" name="reason" value="{{ old('reason', 'manual_grant') }}" required>
                                    </div>
                                </div>

                                <div class="tt-form-group">
                                    <label for="idempotency_key">Idempotency key</label>
                                    <input class="tt-form-control" id="idempotency_key" name="idempotency_key" value="{{ old('idempotency_key', 'grant-'.auth()->id().'-'.now()->timestamp) }}" required>
                                </div>

                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Crediter le wallet">Crediter le wallet</span>
                                </button>
                            </form>
                        </section>

                        <section class="adm-surface">
                            <div class="tt-heading tt-heading-lg margin-bottom-20">
                                <h2 class="tt-heading-title tt-text-reveal">Resultats recherche</h2>
                                <p class="max-width-700 tt-anim-fadeinup text-gray">Apercu rapide des utilisateurs trouves avec leurs soldes actuels.</p>
                            </div>

                            @if($usersCount)
                                <div class="adm-user-list">
                                    @foreach($users as $u)
                                        <article class="adm-user-item">
                                            <strong>{{ $u->name }}</strong>
                                            <small>{{ $u->email }}</small>
                                            <div class="adm-row-actions margin-top-10">
                                                <span class="adm-pill">ID #{{ $u->id }}</span>
                                                <span class="adm-pill">Solde {{ (int) ($u->wallet->balance ?? 0) }}</span>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="adm-empty">Aucun utilisateur ne correspond a ce filtre.</div>
                            @endif
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection

