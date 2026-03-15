@extends('marketing.layouts.template')

@section('title', 'Espace supporter | ERAH Plateforme')
@section('meta_description', 'Gestion de votre abonnement supporter ERAH, avantages mensuels et preferences de mur public.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <link rel="stylesheet" href="/template/assets/css/blog.css">
    <style>
        .supporter-console-shell { display: grid; gap: 24px; }
        .supporter-console-grid { display: grid; grid-template-columns: 1.1fr .9fr; gap: 18px; align-items: stretch; }
        .supporter-console-card {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 22px;
            padding: 24px;
            background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
            height: 100%;
        }
        .supporter-console-kpis { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .supporter-console-kpi { border: 1px solid rgba(255,255,255,.14); border-radius: 16px; padding: 14px 16px; }
        .supporter-console-kpi strong { display: block; font-size: 30px; line-height: 1; }
        .supporter-console-kpi span { display: block; margin-top: 6px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,.68); }
        .supporter-console-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
        .supporter-reward-list,
        .supporter-mission-list { display: grid; gap: 10px; }
        .supporter-reward-item,
        .supporter-mission-item,
        .supporter-campaign-item {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255,255,255,.03);
        }
        .supporter-inline-form { display: grid; gap: 14px; }
        .supporter-inline-form .tt-form-group { margin: 0; }
        .supporter-campaign-list { display: grid; gap: 12px; }
        .supporter-campaign-options { display: grid; gap: 10px; margin-top: 14px; }
        .supporter-campaign-option {
            display: flex; align-items: center; gap: 12px;
            border: 1px solid rgba(255,255,255,.12); border-radius: 14px; padding: 10px 12px;
        }
        .supporter-campaign-option img { width: 88px; height: 56px; border-radius: 10px; object-fit: cover; }
        .supporter-campaign-option form { margin-left: auto; }
        .supporter-console-muted { color: rgba(255,255,255,.7); }
        .supporter-console-badge-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; align-content: start; }
        .supporter-console-badge-card {
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 16px;
            padding: 16px;
            background: rgba(255,255,255,.03);
            min-height: 138px;
            display: grid;
            align-content: start;
            gap: 8px;
        }
        .supporter-console-badge-label {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.76);
        }
        .supporter-console-badge-value {
            font-size: 26px;
            line-height: 1.05;
            font-weight: 700;
            color: #fff;
        }
        .supporter-console-highlight-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; align-content: start; }
        .supporter-console-highlight-item {
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255,255,255,.03);
            min-height: 120px;
        }
        .supporter-console-highlight-item strong {
            display: block;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: 12px;
            color: #ffd86b;
        }
        body.tt-lightmode-on .supporter-console-card,
        body.tt-lightmode-on .supporter-console-kpi,
        body.tt-lightmode-on .supporter-reward-item,
        body.tt-lightmode-on .supporter-mission-item,
        body.tt-lightmode-on .supporter-campaign-item,
        body.tt-lightmode-on .supporter-campaign-option,
        body.tt-lightmode-on .supporter-console-badge-card,
        body.tt-lightmode-on .supporter-console-highlight-item {
            border-color: rgba(148, 163, 184, .26);
            background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(244,247,252,.94));
            box-shadow: 0 18px 36px rgba(148, 163, 184, .16);
        }
        body.tt-lightmode-on .supporter-console-kpi span,
        body.tt-lightmode-on .supporter-console-badge-label,
        body.tt-lightmode-on .supporter-console-muted {
            color: rgba(51, 65, 85, .78);
        }
        body.tt-lightmode-on .supporter-console-badge-value,
        body.tt-lightmode-on .supporter-console-kpi strong {
            color: #0f172a;
        }
        body.tt-lightmode-on .supporter-console-highlight-item strong {
            color: #9a3412;
        }
        @media (max-width: 1199.98px) {
            .supporter-console-grid { grid-template-columns: 1fr; }
            .supporter-console-kpis { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .supporter-console-badge-grid,
            .supporter-console-highlight-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 767.98px) {
            .supporter-console-kpis { grid-template-columns: 1fr; }
            .supporter-console-actions .tt-btn { width: 100%; justify-content: center; }
            .supporter-campaign-option { flex-direction: column; align-items: flex-start; }
            .supporter-campaign-option form { margin-left: 0; width: 100%; }
            .supporter-campaign-option .tt-btn { width: 100%; justify-content: center; }
            .supporter-console-badge-card,
            .supporter-console-highlight-item { min-height: 0; }
        }
    </style>
@endsection

@section('content')
    @php
        $currentSubscription = $currentSubscription ?? null;
        $isActive = (bool) ($supporterSummary['is_active'] ?? false);
        $supporterBonusPercent = max(0, (int) round((config('supporter.xp_multiplier', 1) - 1) * 100));
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Supporter ERAH</h2>
                    <h1 class="ph-caption-title">Espace supporter</h1>
                    <div class="ph-caption-description max-width-800">
                        Suivez votre abonnement, vos recompenses mensuelles, vos votes clips et votre visibilite sur le mur des supporters.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Supporter ERAH</h2>
                        <h1 class="ph-caption-title">Espace supporter</h1>
                        <div class="ph-caption-description max-width-800">
                            Espace dedie a la gestion de votre statut supporter et de vos interactions premium sur la plateforme.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-scroll-down">
            <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
                <div class="tt-scrd-icon"></div>
                <svg viewBox="0 0 500 500">
                    <defs>
                        <path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="textcircle"></path>
                    </defs>
                    <text dy="30">
                        <textPath xlink:href="#textcircle">Espace Supporter - Espace Supporter -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120 border-bottom">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="supporter-console-shell">
                    <section class="supporter-console-card tt-anim-fadeinup">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h3 class="tt-heading-subtitle">Abonnement</h3>
                            <h2 class="tt-heading-title">{{ $isActive ? 'Supporter actif' : 'Supporter inactif' }}</h2>
                        </div>

                        <div class="supporter-console-kpis">
                            <div class="supporter-console-kpi">
                                <strong>{{ (int) ($progress->total_xp ?? 0) }}</strong>
                                <span>XP total</span>
                            </div>
                            <div class="supporter-console-kpi">
                                <strong>{{ (int) ($progress->total_rank_points ?? 0) }}</strong>
                                <span>Points classement</span>
                            </div>
                            <div class="supporter-console-kpi">
                                <strong>{{ (int) ($supporterSummary['months'] ?? 0) }}</strong>
                                <span>Mois de soutien</span>
                            </div>
                            <div class="supporter-console-kpi">
                                <strong>{{ (int) ($supporterSummary['active_rewards_count'] ?? 0) }}</strong>
                                <span>Recompenses</span>
                            </div>
                        </div>

                        <div class="supporter-console-actions">
                            @if($isActive)
                                <form method="POST" action="{{ route('supporter.portal') }}">
                                    @csrf
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Gérer dans Stripe">Gérer dans Stripe</span>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('supporter.checkout') }}">
                                    @csrf
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Activer Supporter">Activer Supporter</span>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('supporter.show') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Voir la page supporter">Voir la page supporter</span>
                            </a>
                        </div>

                        <p class="supporter-console-muted margin-top-20 no-margin">
                            Statut: {{ strtoupper((string) ($supporterSummary['status'] ?? 'inactive')) }}
                            @if($currentSubscription?->current_period_end)
                                  - renouvellement / fin de période: {{ $currentSubscription->current_period_end->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </section>

                    <section class="supporter-console-grid">
                        @if($isActive)
                            <div class="supporter-console-card tt-anim-fadeinup">
                                <div class="tt-heading tt-heading-sm margin-bottom-20">
                                    <h3 class="tt-heading-subtitle">Mur supporters</h3>
                                    <h2 class="tt-heading-title">Visibilité publique</h2>
                                </div>

                                <form method="POST" action="{{ route('profile.update') }}" class="tt-form tt-form-creative tt-form-lg supporter-inline-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ old('name', $user->name) }}">
                                    <input type="hidden" name="bio" value="{{ old('bio', $user->bio) }}">
                                    <input type="hidden" name="twitter_url" value="{{ old('twitter_url', $user->twitter_url) }}">
                                    <input type="hidden" name="instagram_url" value="{{ old('instagram_url', $user->instagram_url) }}">
                                    <input type="hidden" name="tiktok_url" value="{{ old('tiktok_url', $user->tiktok_url) }}">
                                    <input type="hidden" name="discord_url" value="{{ old('discord_url', $user->discord_url) }}">
                                    <input type="hidden" name="_profile_return" value="console">

                                    <div class="tt-form-group">
                                        <label for="supporter_display_name">Nom affiche sur le mur</label>
                                        <input class="tt-form-control" id="supporter_display_name" name="supporter_display_name" type="text" value="{{ old('supporter_display_name', $supporterProfile->display_name ?? $user->name) }}">
                                    </div>

                                    <div class="tt-form-group">
                                        <label class="tt-form-check">
                                            <input type="hidden" name="show_in_supporter_wall" value="0">
                                            <input type="checkbox" name="show_in_supporter_wall" value="1" @checked(old('show_in_supporter_wall', $supporterProfile->is_visible_on_wall ?? true))>
                                            <span>Afficher mon profil dans le mur des supporters</span>
                                        </label>
                                    </div>

                                    <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                        <span data-hover="Enregistrer">Enregistrer</span>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="supporter-console-card tt-anim-fadeinup">
                                <div class="tt-heading tt-heading-sm margin-bottom-20">
                                    <h3 class="tt-heading-subtitle">Activation</h3>
                                    <h2 class="tt-heading-title">Ce que vous débloquez</h2>
                                </div>

                                <div class="supporter-console-highlight-grid">
                                    <article class="supporter-console-highlight-item">
                                        <strong>Clips</strong>
                                        <div class="supporter-console-muted">Votes premium, réactions supporter et commentaires prioritaires sur les clips admin.</div>
                                    </article>
                                    <article class="supporter-console-highlight-item">
                                        <strong>Missions</strong>
                                        <div class="supporter-console-muted">Missions exclusives, bonus XP mensuel et progression fidélité réservée aux supporters.</div>
                                    </article>
                                    <article class="supporter-console-highlight-item">
                                        <strong>Communaute</strong>
                                        <div class="supporter-console-muted">Badge visible, mur supporter public et avantages club/IRL mis en avant.</div>
                                    </article>
                                    <article class="supporter-console-highlight-item">
                                        <strong>Classement</strong>
                                        <div class="supporter-console-muted">Badge supporter dans les leaderboards et profil mieux exposé dans les espaces compétitifs.</div>
                                    </article>
                                    <article class="supporter-console-highlight-item">
                                        <strong>Profil</strong>
                                        <div class="supporter-console-muted">Clips favoris visibles, espace dédié et options supporter centralisées dans votre compte.</div>
                                    </article>
                                    <article class="supporter-console-highlight-item">
                                        <strong>Club / IRL</strong>
                                        <div class="supporter-console-muted">Accès anticipé aux drops, réductions merchandising et activations communauté ERAH.</div>
                                    </article>
                                </div>
                            </div>
                        @endif

                        <div class="supporter-console-card tt-anim-fadeinup">
                            <div class="tt-heading tt-heading-sm margin-bottom-20">
                                <h3 class="tt-heading-subtitle">Badges</h3>
                                <h2 class="tt-heading-title">Reconnaissance</h2>
                            </div>

                            <div class="supporter-console-badge-grid">
                                <article class="supporter-console-badge-card">
                                    <span class="supporter-console-badge-label">Badge fidélité</span>
                                    <div class="supporter-console-badge-value">{{ $supporterSummary['loyalty_badge'] ?? 'Aucun' }}</div>
                                    <div class="supporter-console-muted">Progression selon votre ancinneté supporter.</div>
                                </article>
                                <article class="supporter-console-badge-card">
                                    <span class="supporter-console-badge-label">Badge fondateur</span>
                                    <div class="supporter-console-badge-value">{{ ($supporterSummary['is_founder'] ?? false) ? 'Attribue' : 'Non attribue' }}</div>
                                    <div class="supporter-console-muted">Reserve aux premiers supporters ou a la fenetre de lancement.</div>
                                </article>
                                <article class="supporter-console-badge-card">
                                    <span class="supporter-console-badge-label">Ligue actuelle</span>
                                    <div class="supporter-console-badge-value">{{ $progress->league->name ?? 'Aucune' }}</div>
                                    <div class="supporter-console-muted">Votre positionnement actuel sur la plateforme.</div>
                                </article>
                                <article class="supporter-console-badge-card">
                                    <span class="supporter-console-badge-label">{{ $isActive ? 'Mur supporter' : 'Bonus XP' }}</span>
                                    <div class="supporter-console-badge-value">{{ $isActive ? (($supporterProfile->is_visible_on_wall ?? false) ? 'Visible' : 'Prive') : ('+'.$supporterBonusPercent.'%') }}</div>
                                    <div class="supporter-console-muted">
                                        {{ $isActive ? 'Controle de votre presence sur le mur public des supporters.' : 'Multiplicateur supporter applique sur les gains XP eligibles.' }}
                                    </div>
                                </article>
                            </div>
                        </div>
                    </section>

                    <section class="supporter-console-grid">
                        <div class="supporter-console-card tt-anim-fadeinup">
                            <div class="tt-heading tt-heading-sm margin-bottom-20">
                                <h3 class="tt-heading-subtitle">Recompenses mensuelles</h3>
                                <h2 class="tt-heading-title">Timeline supporter</h2>
                            </div>

                            <div class="supporter-reward-list">
                                @forelse($monthlyRewards as $reward)
                                    <article class="supporter-reward-item">
                                        <strong>{{ str($reward->reward_key)->replace('_', ' ')->title() }}</strong>
                                        <div class="supporter-console-muted">
                                            {{ $reward->reward_month?->format('m/Y') }} - accorde le {{ optional($reward->granted_at)->format('d/m/Y H:i') }}
                                        </div>
                                    </article>
                                @empty
                                    <p class="no-margin">Aucune recompense supporter pour le moment.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="supporter-console-card tt-anim-fadeinup">
                            <div class="tt-heading tt-heading-sm margin-bottom-20">
                                <h3 class="tt-heading-subtitle">Missions exclusives</h3>
                                <h2 class="tt-heading-title">Mission supporter</h2>
                            </div>

                            <div class="supporter-mission-list">
                                @forelse($exclusiveMissions as $mission)
                                    @php($template = $mission->instance?->template)
                                    <article class="supporter-mission-item">
                                        <strong>{{ $template?->title ?? 'Mission supporter' }}</strong>
                                        <div class="supporter-console-muted">
                                            {{ $template?->description ?? 'Mission reservee aux supporters.' }}
                                        </div>
                                        <div class="supporter-console-muted margin-top-10">
                                            Progression: {{ (int) $mission->progress_count }} / {{ (int) ($template?->target_count ?? 1) }}
                                        </div>
                                    </article>
                                @empty
                                    <p class="no-margin">Aucune mission supporter disponible pour le moment.</p>
                                @endforelse
                            </div>
                        </div>
                    </section>

                    <section class="supporter-console-card tt-anim-fadeinup">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h3 class="tt-heading-subtitle">Votes clips</h3>
                            <h2 class="tt-heading-title">Campagnes en cours</h2>
                        </div>

                        <div class="supporter-campaign-list">
                            @forelse($campaigns as $campaign)
                                <article class="supporter-campaign-item">
                                    <strong>{{ $campaign->title }}</strong>
                                    <div class="supporter-console-muted margin-top-6">
                                        {{ strtoupper($campaign->type) }} - fin {{ optional($campaign->ends_at)->format('d/m/Y H:i') }} - {{ (int) $campaign->votes_count }} vote(s)
                                    </div>

                                    <div class="supporter-campaign-options">
                                        @foreach($campaign->entries as $entry)
                                            @if($entry->clip)
                                                <div class="supporter-campaign-option">
                                                    <img src="{{ $entry->clip->thumbnail_url ?: '/template/assets/img/logo.png' }}" alt="{{ $entry->clip->title }}">
                                                    <div>
                                                        <strong>{{ $entry->clip->title }}</strong>
                                                        <div class="supporter-console-muted">{{ optional($entry->clip->published_at)->format('d/m/Y H:i') }}</div>
                                                    </div>
                                                    @if($isActive)
                                                        <form method="POST" action="{{ route('clips.campaigns.vote', $campaign->id) }}">
                                                            @csrf
                                                            <input type="hidden" name="clip_id" value="{{ $entry->clip->id }}">
                                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                                <span data-hover="Voter">Voter</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </article>
                            @empty
                                <p class="no-margin">Aucune campagne de vote ouverte actuellement.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="supporter-console-card tt-anim-fadeinup">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h3 class="tt-heading-subtitle">Clips favoris</h3>
                            <h2 class="tt-heading-title">Vos reperes clips</h2>
                        </div>

                        <div id="blog-list" class="bli-image-cropped">
                            @forelse($favoriteClips as $clip)
                                <article class="blog-list-item">
                                    <a href="{{ route('clips.show', $clip->slug) }}" class="bli-image-wrap" data-cursor="Read<br>More">
                                        <figure class="bli-image tt-anim-zoomin">
                                            <img src="{{ $clip->thumbnail_url ?: '/template/assets/img/logo.png' }}" loading="lazy" alt="{{ $clip->title }}">
                                        </figure>
                                    </a>
                                    <div class="bli-info">
                                        <div class="bli-categories"><a href="{{ route('clips.favorites') }}">Favori</a></div>
                                        <h2 class="bli-title"><a href="{{ route('clips.show', $clip->slug) }}">{{ $clip->title }}</a></h2>
                                        <div class="bli-meta">
                                            <span class="published">{{ optional($clip->published_at)->format('d/m/Y H:i') }}</span>
                                            <span class="posted-by">- clip ERAH</span>
                                        </div>
                                        <div class="bli-desc">{{ \Illuminate\Support\Str::limit((string) ($clip->description ?? 'Clip favori supporter.'), 150) }}</div>
                                        <a href="{{ route('clips.show', $clip->slug) }}" class="tt-btn tt-btn-outline">
                                            <span data-hover="En savoir plus">En savoir plus</span>
                                        </a>
                                    </div>
                                </article>
                            @empty
                                <p class="no-margin">Aucun clip favori pour le moment.</p>
                            @endforelse
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="/template/assets/vendor/jquery/jquery.min.js" defer></script>
    <script src="/template/assets/vendor/gsap/gsap.min.js" defer></script>
    <script src="/template/assets/vendor/gsap/ScrollToPlugin.min.js" defer></script>
    <script src="/template/assets/vendor/gsap/ScrollTrigger.min.js" defer></script>
    <script src="/template/assets/vendor/lenis.min.js" defer></script>
    <script src="/template/assets/vendor/isotope/imagesloaded.pkgd.min.js" defer></script>
    <script src="/template/assets/vendor/isotope/isotope.pkgd.min.js" defer></script>
    <script src="/template/assets/vendor/isotope/packery-mode.pkgd.min.js" defer></script>
    <script src="/template/assets/vendor/fancybox/js/fancybox.umd.js" defer></script>
    <script src="/template/assets/vendor/swiper/js/swiper-bundle.min.js" defer></script>
    <script src="/template/assets/js/theme.js" defer></script>
    <script src="/template/assets/js/cookies.js" defer></script>
@endsection
