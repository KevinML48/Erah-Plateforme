@extends('marketing.layouts.template')

@section('title', 'Admin Supporters | ERAH Plateforme')
@section('meta_description', 'Pilotage des abonnements supporter, statuts Stripe, visibilite mur public et recompenses mensuelles.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-supporter-list { display: grid; gap: 14px; }
        .adm-supporter-card {
            border: 1px solid var(--adm-border);
            border-radius: 22px;
            padding: 18px;
            background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
            display: grid;
            grid-template-columns: 1.4fr .9fr .9fr auto;
            gap: 14px;
            align-items: start;
        }
        .adm-supporter-card strong { color: var(--adm-text); font-size: 24px; line-height: 1; }
        .adm-supporter-meta { color: var(--adm-text-soft); display: grid; gap: 6px; }
        .adm-supporter-col { display: grid; gap: 8px; }
        @media (max-width: 1199.98px) {
            .adm-supporter-card { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Support Program',
        'heroTitle' => 'Admin Supporters',
        'heroDescription' => 'Abonnements actifs, statuts Stripe, mur public et programme fidelite supporter.',
        'heroMaskDescription' => 'Gestion centralisee des supporters et des benefices premium.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Vue programme supporter</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Suivi du volume d abonnes, de la visibilite publique et des recompenses mensuelles.</p>
                        </div>

                        <div class="adm-kpi-grid">
                            <article class="adm-kpi-card tt-anim-fadeinup"><strong>{{ (int) $totalSupporters }}</strong><span>Supporters actifs</span></article>
                            <article class="adm-kpi-card tt-anim-fadeinup"><strong>{{ (int) $wallVisibleCount }}</strong><span>Mur public actif</span></article>
                            <article class="adm-kpi-card tt-anim-fadeinup"><strong>{{ (int) $monthlyRewardCount }}</strong><span>Rewards emis</span></article>
                            <article class="adm-kpi-card tt-anim-fadeinup"><strong>{{ (int) ($statusCounts['past_due'] ?? 0) }}</strong><span>Past due</span></article>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Filtrer les supporters</h2>
                        </div>

                        <form method="GET" class="adm-form adm-form-grid-3 adm-form tt-form tt-form-creative tt-form-lg">
                            <div class="tt-form-group adm-col-span-2">
                                <label for="supporter-q">Nom ou email</label>
                                <input class="tt-form-control" id="supporter-q" name="q" type="text" value="{{ $search }}" placeholder="Ex: admin, player, supporter...">
                            </div>
                            <div class="tt-form-group">
                                <label for="supporter-status">Statut</label>
                                <select class="tt-form-control" id="supporter-status" name="status">
                                    <option value="all">Tous</option>
                                    @foreach(\App\Models\UserSupportSubscription::statuses() as $statusValue)
                                        <option value="{{ $statusValue }}" @selected($status === $statusValue)>{{ strtoupper($statusValue) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="tt-form-group adm-form-cta adm-col-span-3">
                                <p class="adm-form-cta-copy">Conservez uniquement les statuts utiles avant d ouvrir le detail d un supporter.</p>
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Filtrer">Filtrer</span></button>
                            </div>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Abonnements supporters</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Dernier etat connu par utilisateur, issu du flux Cashier + synchronisation Stripe.</p>
                        </div>

                        @if($subscriptions->count())
                            <div class="adm-supporter-list">
                                @foreach($subscriptions as $subscription)
                                    @php($supporterUser = $subscription->user)
                                    <article class="adm-supporter-card tt-anim-fadeinup">
                                        <div class="adm-supporter-col">
                                            <strong>{{ $supporterUser?->name ?? 'Utilisateur' }}</strong>
                                            <div class="adm-supporter-meta">
                                                <span>{{ $supporterUser?->email }}</span>
                                                <span>Plan: {{ $subscription->plan?->name ?? 'Supporter ERAH' }}</span>
                                                <span>Stripe customer: {{ $subscription->provider_customer_id ?: 'n/a' }}</span>
                                            </div>
                                        </div>
                                        <div class="adm-supporter-col">
                                            <span class="adm-pill">{{ strtoupper($subscription->status) }}</span>
                                            <div class="adm-supporter-meta">
                                                <span>Debut: {{ optional($subscription->started_at)->format('d/m/Y H:i') ?: 'n/a' }}</span>
                                                <span>Fin periode: {{ optional($subscription->current_period_end)->format('d/m/Y H:i') ?: 'n/a' }}</span>
                                            </div>
                                        </div>
                                        <div class="adm-supporter-col">
                                            <div class="adm-supporter-meta">
                                                <span>Ligue: {{ $supporterUser?->progress?->league?->name ?? 'n/a' }}</span>
                                                <span>XP: {{ (int) ($supporterUser?->progress?->total_xp ?? 0) }}</span>
                                                <span>Wall public: {{ ($supporterUser?->supportPublicProfile?->is_visible_on_wall ?? false) ? 'Oui' : 'Non' }}</span>
                                            </div>
                                        </div>
                                        <div class="adm-row-actions">
                                            <a href="{{ route('admin.supporters.show', $supporterUser?->id) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Ouvrir">Ouvrir</span>
                                            </a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $subscriptions->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun supporter pour ce filtre.</div>
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
