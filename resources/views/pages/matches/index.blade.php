@extends('marketing.layouts.template')

@section('title', 'Matchs | ERAH Plateforme')
@section('meta_description', 'Programme esport des matchs, etat des rencontres et acces detail par match.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .match-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .match-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .match-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .88);
        }

        .match-tab.active {
            border-color: rgba(88, 198, 255, .7);
            color: #d7f4ff;
            background: rgba(88, 198, 255, .12);
        }

        .match-tab-count {
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 999px;
            padding: 1px 7px;
            font-size: 11px;
            line-height: 1.3;
        }

        .match-toolbar-note {
            color: rgba(255, 255, 255, .68);
            font-size: 13px;
        }

        .match-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }

        .match-kpi-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 14px 16px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .match-kpi-card strong {
            display: block;
            font-size: 32px;
            line-height: 1;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .match-kpi-card span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .7);
        }

        .match-pcli-item .pcli-item-inner {
            align-items: stretch;
        }

        .match-pcli-item .pcli-image {
            height: 100%;
        }

        .match-pcli-item .pcli-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .match-status-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .match-status-pill.is-live {
            border-color: rgba(255, 118, 118, .65);
            color: #ffd3d3;
        }

        .match-status-pill.is-upcoming {
            border-color: rgba(96, 214, 255, .62);
            color: #d8f5ff;
        }

        .match-status-pill.is-finished {
            border-color: rgba(149, 149, 149, .55);
            color: #e2e2e2;
        }

        .match-card-meta {
            margin: 12px 0 0;
            color: rgba(255, 255, 255, .72);
            font-size: 13px;
            line-height: 1.45;
        }

        .match-empty {
            border: 1px dashed rgba(255, 255, 255, .2);
            border-radius: 14px;
            padding: 28px;
            text-align: center;
            color: rgba(255, 255, 255, .72);
        }

        .match-pagin-item-disabled {
            opacity: .35;
            pointer-events: none;
        }

        @media (max-width: 1199.98px) {
            .match-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .match-kpi-grid {
                grid-template-columns: 1fr;
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
        $totalMatches = (int) ($totalMatches ?? array_sum($tabCounts));
        $statusLabelMap = [
            'scheduled' => 'Programme',
            'locked' => 'Verrouille',
            'live' => 'Live',
            'finished' => 'Termine',
            'settled' => 'Regle',
            'cancelled' => 'Annule',
        ];
        $statusClassMap = [
            'scheduled' => 'is-upcoming',
            'locked' => 'is-upcoming',
            'live' => 'is-live',
            'finished' => 'is-finished',
            'settled' => 'is-finished',
            'cancelled' => 'is-finished',
        ];
        $coverPool = [
            '/template/assets/img/portfolio/1200/portfolio-1.jpg',
            '/template/assets/img/portfolio/1200/portfolio-2.jpg',
            '/template/assets/img/portfolio/1200/portfolio-3.jpg',
            '/template/assets/img/portfolio/1200/portfolio-4.jpg',
            '/template/assets/img/portfolio/1200/portfolio-5.jpg',
            '/template/assets/img/portfolio/1200/portfolio-6.jpg',
            '/template/assets/img/portfolio/1200/portfolio-7.jpg',
            '/template/assets/img/portfolio/1200/portfolio-8.jpg',
        ];
        $resultLabelMap = [
            'home' => 'Victoire equipe A',
            'away' => 'Victoire equipe B',
            'draw' => 'Match nul',
            'void' => 'Resultat annule',
        ];
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
                    <h1 class="ph-caption-title">Matchs</h1>
                    <div class="ph-caption-description max-width-800">
                        Suivi des rencontres esport: a venir, live et terminees.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Match Center</h2>
                        <h1 class="ph-caption-title">Matchs</h1>
                        <div class="ph-caption-description max-width-800">
                            Consultez les statistiques et ouvrez chaque match en detail.
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
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="match-toolbar">
                    <div class="match-tabs">
                        <a href="{{ route($indexRouteName, ['tab' => 'upcoming']) }}" class="match-tab {{ $tab === 'upcoming' ? 'active' : '' }}">
                            A venir
                            <span class="match-tab-count">{{ (int) ($tabCounts['upcoming'] ?? 0) }}</span>
                        </a>
                        <a href="{{ route($indexRouteName, ['tab' => 'live']) }}" class="match-tab {{ $tab === 'live' ? 'active' : '' }}">
                            Live
                            <span class="match-tab-count">{{ (int) ($tabCounts['live'] ?? 0) }}</span>
                        </a>
                        <a href="{{ route($indexRouteName, ['tab' => 'finished']) }}" class="match-tab {{ $tab === 'finished' ? 'active' : '' }}">
                            Termines
                            <span class="match-tab-count">{{ (int) ($tabCounts['finished'] ?? 0) }}</span>
                        </a>
                    </div>

                    @if($isPublicApp && auth()->guest())
                        <div class="match-toolbar-note">Mode public actif. Connexion requise pour placer un pari.</div>
                    @endif
                </div>

                <div class="match-kpi-grid">
                    <article class="match-kpi-card tt-anim-fadeinup">
                        <strong>{{ $totalMatches }}</strong>
                        <span>Total matchs</span>
                    </article>
                    <article class="match-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($tabCounts['upcoming'] ?? 0) }}</strong>
                        <span>A venir</span>
                    </article>
                    <article class="match-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($tabCounts['live'] ?? 0) }}</strong>
                        <span>Live</span>
                    </article>
                    <article class="match-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($tabCounts['finished'] ?? 0) }}</strong>
                        <span>Termines</span>
                    </article>
                </div>

                @if(($matches ?? null) && $matches->count())
                    <div class="tt-portfolio-compact-list pcl-caption-hover pcl-image-hover">
                        <div class="pcli-inner">
                            @foreach($matches as $match)
                                @php
                                    $teamA = (string) ($match->team_a_name ?: $match->home_team ?: 'Equipe A');
                                    $teamB = (string) ($match->team_b_name ?: $match->away_team ?: 'Equipe B');
                                    $statusKey = (string) $match->status;
                                    $statusLabel = $statusLabelMap[$statusKey] ?? strtoupper($statusKey);
                                    $statusClass = $statusClassMap[$statusKey] ?? 'is-finished';
                                    $cover = $coverPool[$loop->index % count($coverPool)];
                                    $startsAt = $match->starts_at ? $match->starts_at->format('d/m/Y H:i') : '-';
                                    $lockAt = $match->locked_at ? $match->locked_at->format('d/m/Y H:i') : '-';
                                    $resultLabel = $resultLabelMap[(string) $match->result] ?? ((string) $match->result !== '' ? strtoupper((string) $match->result) : null);
                                @endphp
                                <a href="{{ route($showRouteName, $match->id) }}" class="pcli-item match-pcli-item tt-anim-fadeinup" data-cursor="Voir<br>Match">
                                    <div class="pcli-item-inner">
                                        <div class="pcli-col pcli-col-image">
                                            <div class="pcli-image">
                                                <img src="{{ $cover }}" loading="lazy" alt="{{ $teamA }} vs {{ $teamB }}">
                                            </div>
                                        </div>

                                        <div class="pcli-col pcli-col-count">
                                            <div class="pcli-count"></div>
                                        </div>

                                        <div class="pcli-col pcli-col-caption">
                                            <div class="pcli-caption">
                                                <h2 class="pcli-title">{{ $teamA }} vs {{ $teamB }}</h2>
                                                <div class="pcli-categories">
                                                    <div class="pcli-category match-status-pill {{ $statusClass }}">{{ $statusLabel }}</div>
                                                    <div class="pcli-category">{{ $startsAt }}</div>
                                                </div>
                                                <p class="match-card-meta">
                                                    Paris enregistres: {{ (int) ($match->bets_count ?? 0) }}<br>
                                                    Verrouillage: {{ $lockAt }}
                                                    @if($resultLabel)
                                                        <br>Resultat: {{ $resultLabel }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if($matches->hasPages())
                        @php
                            $windowStart = max(1, $matches->currentPage() - 1);
                            $windowEnd = min($matches->lastPage(), $matches->currentPage() + 1);
                        @endphp
                        <div class="tt-pagination tt-pagin-center padding-top-80 padding-top-xlg-100 tt-anim-fadeinup">
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
                    <div class="match-empty">Aucun match dans cet onglet.</div>
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
