@extends('marketing.layouts.template')

@section('title', 'Matchs | ERAH Plateforme')
@section('meta_description', 'Matchs classiques, tournois Rocket League et matchs enfants TOP 16.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .match-toolbar-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) repeat(2, minmax(180px, .7fr)) auto;
            gap: 12px;
            margin-bottom: 24px;
        }

        .match-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .match-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.84);
        }

        .match-tab.active {
            background: rgba(223,11,11,.16);
            border-color: rgba(223,11,11,.45);
            color: #fff;
        }

        .match-tab-count {
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 999px;
            padding: 1px 8px;
        }

        .match-index-kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 30px;
        }

        .match-index-kpi {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 18px;
            padding: 16px;
            background: rgba(255,255,255,.03);
        }

        .match-index-kpi strong {
            display: block;
            font-size: 32px;
            line-height: 1;
            margin-bottom: 8px;
        }

        .match-section {
            margin-bottom: 36px;
        }

        .match-section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .match-section-title {
            margin: 0;
            font-size: clamp(34px, 4.2vw, 66px);
            line-height: .92;
        }

        .match-card-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .match-event-card {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 24px;
            padding: 20px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                rgba(255,255,255,.02);
            display: grid;
            gap: 16px;
        }

        .match-event-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .match-event-title {
            margin: 0;
            font-size: clamp(26px, 3vw, 42px);
            line-height: .95;
        }

        .match-event-subtitle {
            margin: 6px 0 0;
            color: rgba(255,255,255,.68);
            max-width: 540px;
        }

        .match-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .match-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.82);
        }

        .match-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .match-meta-card {
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 16px;
            padding: 12px 14px;
            background: rgba(255,255,255,.02);
        }

        .match-meta-card span {
            display: block;
            margin-bottom: 6px;
            color: rgba(255,255,255,.6);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .match-meta-card strong {
            color: #fff;
        }

        .match-empty {
            border: 1px dashed rgba(255,255,255,.18);
            border-radius: 18px;
            padding: 26px;
            text-align: center;
            color: rgba(255,255,255,.72);
        }

        .match-pagin-item-disabled {
            opacity: .35;
            pointer-events: none;
        }

        body.tt-lightmode-on .match-tab {
            border-color: rgba(148,163,184,.28);
            background: rgba(255,255,255,.9);
            color: rgba(51,65,85,.9);
            box-shadow: 0 12px 26px rgba(148,163,184,.12);
        }

        body.tt-lightmode-on .match-tab.active {
            background: rgba(216,7,7,.1);
            border-color: rgba(216,7,7,.36);
            color: #991b1b;
        }

        body.tt-lightmode-on .match-tab-count,
        body.tt-lightmode-on .match-pill {
            border-color: rgba(148,163,184,.24);
            background: rgba(255,255,255,.78);
            color: inherit;
        }

        body.tt-lightmode-on .match-index-kpi,
        body.tt-lightmode-on .match-event-card,
        body.tt-lightmode-on .match-meta-card,
        body.tt-lightmode-on .match-empty {
            border-color: rgba(148,163,184,.22);
            background: linear-gradient(180deg, rgba(255,255,255,.94), rgba(248,250,252,.88));
            box-shadow: 0 20px 44px rgba(148,163,184,.16);
        }

        body.tt-lightmode-on .match-index-kpi strong,
        body.tt-lightmode-on .match-section-title,
        body.tt-lightmode-on .match-event-title,
        body.tt-lightmode-on .match-meta-card strong {
            color: #0f172a;
        }

        body.tt-lightmode-on .match-index-kpi span,
        body.tt-lightmode-on .match-event-subtitle,
        body.tt-lightmode-on .match-meta-card span,
        body.tt-lightmode-on .match-empty {
            color: rgba(51,65,85,.82);
        }

        @media (max-width: 1199.98px) {
            .match-toolbar-grid,
            .match-index-kpis,
            .match-card-grid,
            .match-meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .match-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 6px;
            }

            .match-tab {
                flex: 0 0 auto;
                min-height: 44px;
                padding-inline: 16px;
            }

            .match-toolbar-grid,
            .match-index-kpis,
            .match-card-grid,
            .match-meta-grid {
                grid-template-columns: 1fr;
            }

            .match-toolbar-grid .match-pill-row,
            .match-event-head .tt-btn {
                width: 100%;
            }

            .match-toolbar-grid .tt-btn,
            .match-event-head .tt-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 479.98px) {
            .match-event-card,
            .match-index-kpi {
                padding: 16px;
                border-radius: 20px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.matches.index' : 'matches.index';
        $showRouteName = $isPublicApp ? 'app.matches.show' : 'matches.show';
        $tabCounts = array_merge(['upcoming' => 0, 'live' => 0, 'finished' => 0], $tabCounts ?? []);
        $sections = [
            'classic' => ['title' => 'Matchs classiques', 'description' => 'Valorant et autres affiches connues a l avance.'],
            'tournaments' => ['title' => 'Tournois Rocket League', 'description' => 'Prediction de parcours avant que les matchs reels du TOP 16 soient connus.'],
            'rocketLeagueMatches' => ['title' => 'Rocket League TOP 16', 'description' => 'Matchs reels lies a un tournoi parent une fois la phase matchs debloquee.'],
        ];
        $matchLabelResolver = $matchLabelResolver ?? null;
    @endphp

    <div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="ph-video ph-video-cover-6">
            <div class="ph-video-inner">
                <video loop muted autoplay playsinline preload="metadata" poster="/template/assets/vids/1920/video-4-1920.jpg">
                    <source src="/template/assets/vids/placeholder.mp4" data-src="/template/assets/vids/1920/video-4-1920.mp4" type="video/mp4">
                    <source src="/template/assets/vids/placeholder.webm" data-src="/template/assets/vids/1920/video-4-1920.webm" type="video/webm">
                </video>
            </div>
        </div>

        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Match Center</h2>
                    <h1 class="ph-caption-title">Matchs & tournois</h1>
                    <div class="ph-caption-description max-width-900">
                        Parcourez les matchs directs et les tournois Rocket League avec un moteur de prediction adapte a chaque phase de competition.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Match Center</h2>
                        <h1 class="ph-caption-title">Matchs & tournois</h1>
                        <div class="ph-caption-description max-width-900">
                            Match classique, parcours tournoi Rocket League, puis matchs enfants du TOP 16.
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
                        <textPath xlink:href="#textcircle">Scroll To Explore - Scroll To Explore -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top" data-tour="matches-overview">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="match-tabs">
                    @foreach(['upcoming' => 'A venir', 'live' => 'En direct', 'finished' => 'Termines'] as $tabKey => $tabLabel)
                        <a href="{{ route($indexRouteName, array_filter(['tab' => $tabKey, 'game' => $game !== 'all' ? $game : null, 'event_type' => $eventType !== 'all' ? $eventType : null, 'q' => $search ?: null])) }}" class="match-tab {{ $tab === $tabKey ? 'active' : '' }}">
                            {{ $tabLabel }}
                            <span class="match-tab-count">{{ (int) ($tabCounts[$tabKey] ?? 0) }}</span>
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route($indexRouteName) }}" class="match-toolbar-grid">
                    <div>
                        <input class="tt-form-control" name="q" value="{{ $search }}" placeholder="Rechercher un tournoi, une equipe ou une phase...">
                    </div>
                    <div>
                        <select class="tt-form-control" name="game" data-lenis-prevent>
                            <option value="all" {{ $game === 'all' ? 'selected' : '' }}>Tous les jeux</option>
                            @foreach($gameOptions as $gameKey => $gameLabel)
                                <option value="{{ $gameKey }}" {{ $game === $gameKey ? 'selected' : '' }}>{{ $gameLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select class="tt-form-control" name="event_type" data-lenis-prevent>
                            <option value="all" {{ $eventType === 'all' ? 'selected' : '' }}>Tous les types</option>
                            @foreach($eventTypeOptions as $eventTypeKey => $eventTypeLabel)
                                <option value="{{ $eventTypeKey }}" {{ $eventType === $eventTypeKey ? 'selected' : '' }}>{{ $eventTypeLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="match-pill-row">
                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                            <span data-hover="Filtrer">Filtrer</span>
                        </button>
                    </div>
                </form>

                <div class="match-index-kpis">
                    <article class="match-index-kpi">
                        <strong>{{ (int) $totalMatches }}</strong>
                        <span>Total visible</span>
                    </article>
                    <article class="match-index-kpi">
                        <strong>{{ (int) ($tabCounts['upcoming'] ?? 0) }}</strong>
                        <span>A venir</span>
                    </article>
                    <article class="match-index-kpi">
                        <strong>{{ (int) ($tabCounts['live'] ?? 0) }}</strong>
                        <span>En direct</span>
                    </article>
                    <article class="match-index-kpi">
                        <strong>{{ (int) ($tabCounts['finished'] ?? 0) }}</strong>
                        <span>Termines</span>
                    </article>
                </div>

                @if(($matches ?? null) && $matches->count())
                    @foreach($sections as $sectionKey => $sectionMeta)
                        @php($items = $sectionedMatches[$sectionKey] ?? collect())
                        @if($items->count())
                            <section class="match-section">
                                <div class="match-section-head">
                                    <div>
                                        <h2 class="match-section-title tt-text-reveal">{{ $sectionMeta['title'] }}</h2>
                                        <p class="match-event-subtitle">{{ $sectionMeta['description'] }}</p>
                                    </div>
                                    <span class="match-pill">{{ $items->count() }} evenement(s) sur cette page</span>
                                </div>

                                <div class="match-card-grid">
                                    @foreach($items as $match)
                                        <article class="match-event-card tt-anim-fadeinup">
                                            <div class="match-event-head">
                                                <div>
                                                    <div class="match-pill-row margin-bottom-10">
                                                        <span class="match-pill">{{ $matchLabelResolver->labelForGame($match->game_key) }}</span>
                                                        <span class="match-pill">{{ $matchLabelResolver->labelForEventType($match->event_type) }}</span>
                                                        <span class="match-pill">{{ $matchLabelResolver->labelForStatus($match->status, true) }}</span>
                                                        @if($match->best_of)
                                                            <span class="match-pill">BO{{ $match->best_of }}</span>
                                                        @endif
                                                    </div>
                                                    <h3 class="match-event-title">{{ $match->displayTitle() }}</h3>
                                                    <p class="match-event-subtitle">{{ $match->displaySubtitle() ?: ($match->parentMatch?->event_name ? 'Lie au tournoi '.$match->parentMatch->event_name : 'Les predictions restent ouvertes tant que la cloture n est pas atteinte.') }}</p>
                                                </div>
                                                <a href="{{ route($showRouteName, $match->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                    <span data-hover="Voir le detail">Voir le detail</span>
                                                </a>
                                            </div>

                                            <div class="match-meta-grid">
                                                <article class="match-meta-card">
                                                    <span>Debut</span>
                                                    <strong>{{ $match->starts_at?->format('d/m/Y H:i') ?? '-' }}</strong>
                                                </article>
                                                <article class="match-meta-card">
                                                    <span>Cloture des predictions</span>
                                                    <strong>{{ $match->locked_at?->format('d/m/Y H:i') ?? '-' }}</strong>
                                                </article>
                                                <article class="match-meta-card">
                                                    <span>Pronostics</span>
                                                    <strong>{{ (int) $match->bets_count }}</strong>
                                                </article>
                                                <article class="match-meta-card">
                                                    <span>Competition</span>
                                                    <strong>{{ $match->competition_name ?: '-' }}</strong>
                                                </article>
                                                <article class="match-meta-card">
                                                    <span>Phase</span>
                                                    <strong>{{ $match->competition_stage ?: '-' }}</strong>
                                                </article>
                                                <article class="match-meta-card">
                                                    <span>Resultat</span>
                                                    <strong>{{ $matchLabelResolver->labelForResult($match, $match->result) }}</strong>
                                                </article>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    @endforeach

                    @if($matches->hasPages())
                        <div class="tt-pagination tt-pagin-center padding-top-60 tt-anim-fadeinup">
                            <div class="tt-pagin-prev">
                                <a href="{{ $matches->previousPageUrl() ?: '#' }}" class="tt-pagin-item tt-magnetic-item {{ $matches->onFirstPage() ? 'match-pagin-item-disabled' : '' }}">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                            <div class="tt-pagin-numbers">
                                @for($page = $windowStart; $page <= $windowEnd; $page++)
                                    <a href="{{ $matches->url($page) }}" class="tt-pagin-item tt-magnetic-item {{ $matches->currentPage() === $page ? 'active' : '' }}">{{ $page }}</a>
                                @endfor
                            </div>
                            <div class="tt-pagin-next">
                                <a href="{{ $matches->nextPageUrl() ?: '#' }}" class="tt-pagin-item tt-pagin-next tt-magnetic-item {{ $matches->hasMorePages() ? '' : 'match-pagin-item-disabled' }}">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="match-empty">Aucun evenement visible avec ces filtres.</div>
                @endif
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
@endsection
