@extends('marketing.layouts.template')

@section('title', 'Detail Supporter | ERAH Plateforme')
@section('meta_description', 'Vue détaillée d'un supporter ERAH avec historique d abonnement, rewards et interactions premium.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Supporter Detail',
        'heroTitle' => $user->name,
        'heroDescription' => 'Historique d abonnement, badges, rewards mensuelles et signaux premium clips.',
        'heroMaskDescription' => 'Fiche supporter complète pour moderation et suivi Stripe.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="adm-kpi-grid">
                            <article class="adm-kpi-card tt-anim-fadeinup"><strong>{{ (int) ($supporterSummary['months'] ?? 0) }}</strong><span>Mois de soutien</span></article>
                            <article class="adm-kpi-card tt-anim-fadeinup"><strong>{{ (int) ($supporterStats['votes'] ?? 0) }}</strong><span>Votes clips</span></article>
                            <article class="adm-kpi-card tt-anim-fadeinup"><strong>{{ (int) ($supporterStats['reactions'] ?? 0) }}</strong><span>Reactions premium</span></article>
                            <article class="adm-kpi-card tt-anim-fadeinup"><strong>{{ (int) ($supporterStats['favorites'] ?? 0) }}</strong><span>Favoris clips</span></article>
                        </div>
                    </section>

                    <section class="adm-sub-grid">
                        <article class="adm-surface">
                            <div class="tt-heading tt-heading-lg margin-bottom-30">
                                <h2 class="tt-heading-title tt-text-reveal">Statut actuel</h2>
                            </div>
                            <div class="adm-user-directory">
                                <div class="adm-user-card">
                                    <div class="adm-user-card-head">
                                        <div class="adm-user-card-title">
                                            <strong>{{ $user->name }}</strong>
                                            <span class="text-gray">{{ $user->email }}</span>
                                        </div>
                                        <span class="adm-pill">{{ strtoupper((string) ($supporterSummary['status'] ?? 'inactive')) }}</span>
                                    </div>
                                    <div class="adm-user-card-grid">
                                        <div class="adm-user-stat"><span class="adm-user-stat-title">Badge fidelite</span><div class="adm-user-stat-value">{{ $supporterSummary['loyalty_badge'] ?? 'Aucun' }}</div></div>
                                        <div class="adm-user-stat"><span class="adm-user-stat-title">Fondateur</span><div class="adm-user-stat-value">{{ ($supporterSummary['is_founder'] ?? false) ? 'Oui' : 'Non' }}</div></div>
                                        <div class="adm-user-stat"><span class="adm-user-stat-title">Mur public</span><div class="adm-user-stat-value">{{ ($user->supportPublicProfile?->is_visible_on_wall ?? false) ? 'Visible' : 'Masque' }}</div></div>
                                        <div class="adm-user-stat"><span class="adm-user-stat-title">Nom public</span><div class="adm-user-stat-value">{{ $user->supportPublicProfile?->display_name ?? $user->name }}</div></div>
                                        <div class="adm-user-stat"><span class="adm-user-stat-title">Ligue</span><div class="adm-user-stat-value">{{ $user->progress?->league?->name ?? 'n/a' }}</div></div>
                                        <div class="adm-user-stat"><span class="adm-user-stat-title">XP / Rank</span><div class="adm-user-stat-value">{{ (int) ($user->progress?->total_xp ?? 0) }} XP / {{ (int) ($user->progress?->total_rank_points ?? 0) }} RP</div></div>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <article class="adm-surface">
                            <div class="tt-heading tt-heading-lg margin-bottom-30">
                                <h2 class="tt-heading-title tt-text-reveal">Historique d abonnement</h2>
                            </div>
                            <div class="adm-user-directory">
                                @forelse($subscriptionHistory as $subscription)
                                    <div class="adm-user-card">
                                        <div class="adm-user-card-head">
                                            <div class="adm-user-card-title">
                                                <strong>{{ $subscription->plan?->name ?? 'Supporter ERAH' }}</strong>
                                                <span class="text-gray">{{ strtoupper($subscription->status) }}</span>
                                            </div>
                                            <span class="adm-pill">{{ $subscription->provider_subscription_id ?: 'pending' }}</span>
                                        </div>
                                        <div class="adm-user-card-grid">
                                            <div class="adm-user-stat"><span class="adm-user-stat-title">Debut</span><div class="adm-user-stat-value">{{ optional($subscription->started_at)->format('d/m/Y H:i') ?: 'n/a' }}</div></div>
                                            <div class="adm-user-stat"><span class="adm-user-stat-title">Periode actuelle</span><div class="adm-user-stat-value">{{ optional($subscription->current_period_start)->format('d/m/Y') ?: 'n/a' }} -> {{ optional($subscription->current_period_end)->format('d/m/Y') ?: 'n/a' }}</div></div>
                                            <div class="adm-user-stat"><span class="adm-user-stat-title">Stripe customer</span><div class="adm-user-stat-value">{{ $subscription->provider_customer_id ?: 'n/a' }}</div></div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="adm-empty">Aucun historique supporter.</div>
                                @endforelse
                            </div>
                        </article>
                    </section>

                    <section class="adm-sub-grid">
                        <article class="adm-surface">
                            <div class="tt-heading tt-heading-lg margin-bottom-30">
                                <h2 class="tt-heading-title tt-text-reveal">Rewards mensuelles</h2>
                            </div>
                            <div class="adm-user-directory">
                                @forelse($monthlyRewards as $reward)
                                    <div class="adm-user-card">
                                        <div class="adm-user-card-head">
                                            <div class="adm-user-card-title">
                                                <strong>{{ str($reward->reward_key)->replace('_', ' ')->title() }}</strong>
                                                <span class="text-gray">{{ $reward->reward_month?->format('m/Y') }}</span>
                                            </div>
                                            <span class="adm-pill">{{ optional($reward->granted_at)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="adm-empty">Aucune reward mensuelle.</div>
                                @endforelse
                            </div>
                        </article>

                        <article class="adm-surface">
                            <div class="tt-heading tt-heading-lg margin-bottom-30">
                                <h2 class="tt-heading-title tt-text-reveal">Actions rapides</h2>
                            </div>
                            <div class="adm-row-actions">
                                <a href="{{ route('admin.supporters.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Retour liste">Retour liste</span></a>
                                <a href="{{ route('users.public', $user) }}" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Profil public">Profil public</span></a>
                                <a href="{{ route('admin.clips.campaigns.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Campagnes clips">Campagnes clips</span></a>
                            </div>
                        </article>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
