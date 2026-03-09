@extends('marketing.layouts.template')

@section('title', 'Duels | ERAH Plateforme')
@section('meta_description', 'Suivi dynamique de vos duels, reponses rapides et historique par statut.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .duel-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .duel-filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .duel-filter-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .duel-filter-link.active {
            border-color: rgba(90, 206, 255, .6);
            color: #cbf0ff;
        }

        .duel-filter-count {
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 999px;
            padding: 1px 7px;
            font-size: 11px;
            line-height: 1.3;
        }

        .duel-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 30px;
        }

        .duel-quick-access {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 28px;
            background: linear-gradient(135deg, rgba(255, 255, 255, .04), rgba(255, 255, 255, .01));
        }

        .duel-quick-access-copy {
            max-width: 620px;
        }

        .duel-quick-access-title {
            display: block;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .duel-quick-access-text {
            color: rgba(255, 255, 255, .7);
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }

        .duel-quick-access-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .duel-summary-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 12px;
            padding: 16px 18px;
        }

        .duel-summary-value {
            display: block;
            font-size: 28px;
            line-height: 1;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .duel-item-card {
            position: relative;
        }

        .duel-item-card .pcli-item-inner {
            align-items: center;
        }

        .duel-item-card .pcli-col-image {
            flex: 0 0 180px;
            width: 180px;
        }

        .duel-item-card .pcli-image {
            width: 180px;
            height: 180px;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border-radius: 20px;
            margin-left: auto;
        }

        .duel-item-card .pcli-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .duel-item-card .pcli-caption {
            padding-right: 4px;
        }

        .duel-status-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-right: 6px;
            margin-bottom: 6px;
        }

        .duel-status-pill.is-active {
            border-color: rgba(80, 217, 127, .55);
            color: #dbffe7;
        }

        .duel-status-pill.is-pending {
            border-color: rgba(255, 224, 118, .55);
            color: #fff1c9;
        }

        .duel-status-pill.is-refused {
            border-color: rgba(255, 124, 124, .5);
            color: #ffd1d1;
        }

        .duel-status-pill.is-expired {
            border-color: rgba(174, 174, 174, .5);
            color: #dfdfdf;
        }

        .duel-card-message {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, .78);
            font-size: 14px;
            line-height: 1.45;
        }

        .duel-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
            margin-top: 12px;
            color: rgba(255, 255, 255, .62);
            font-size: 13px;
        }

        .duel-card-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 14px;
        }

        .duel-card-empty {
            border: 1px dashed rgba(255, 255, 255, .16);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            color: rgba(255, 255, 255, .72);
        }

        .duel-pagin-item-disabled {
            opacity: .35;
            pointer-events: none;
        }

        @media (max-width: 991.98px) {
            .duel-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .duel-item-card .pcli-col-image {
                flex: 0 0 132px;
                width: 132px;
            }

            .duel-item-card .pcli-image {
                width: 132px;
                height: 132px;
                border-radius: 16px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.duels.index' : 'duels.index';
        $duelLeaderboardRouteName = $isPublicApp ? 'app.duels.leaderboard' : 'duels.leaderboard';
        $statisticsRouteName = $isPublicApp ? 'app.statistics.index' : 'statistics.index';
        $statusCounts = $statusCounts ?? ['pending' => 0, 'active' => 0, 'finished' => 0];
        $summary = $summary ?? ['needs_response' => 0, 'sent_pending' => 0, 'all' => 0];
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
                    <h2 class="ph-caption-subtitle">ERAH Matchups</h2>
                    <h1 class="ph-caption-title">Duels</h1>
                    <div class="ph-caption-description max-width-800">
                        {{ (int) ($summary['all'] ?? 0) }} duel(s) au total - {{ (int) ($summary['needs_response'] ?? 0) }} en attente de votre reponse.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Matchups</h2>
                        <h1 class="ph-caption-title">Duels</h1>
                        <div class="ph-caption-description max-width-800">
                            Pending, actifs, termines - en liste dynamique.
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
                        <textPath xlink:href="#textcircle">Duel Queue - Duel Queue -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="duel-toolbar">
                    <div class="duel-filter-row">
                        <a href="{{ route($indexRouteName, ['status' => 'pending']) }}" class="duel-filter-link {{ $status === 'pending' ? 'active' : '' }}">
                            Pending
                            <span class="duel-filter-count">{{ (int) ($statusCounts['pending'] ?? 0) }}</span>
                        </a>
                        <a href="{{ route($indexRouteName, ['status' => 'active']) }}" class="duel-filter-link {{ $status === 'active' ? 'active' : '' }}">
                            Active
                            <span class="duel-filter-count">{{ (int) ($statusCounts['active'] ?? 0) }}</span>
                        </a>
                        <a href="{{ route($indexRouteName, ['status' => 'finished']) }}" class="duel-filter-link {{ $status === 'finished' ? 'active' : '' }}">
                            Finished
                            <span class="duel-filter-count">{{ (int) ($statusCounts['finished'] ?? 0) }}</span>
                        </a>
                    </div>

                    <a href="{{ route('duels.create') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                        <span data-hover="Creer un duel">Creer un duel</span>
                    </a>
                </div>

                <div class="duel-quick-access">
                    <div class="duel-quick-access-copy">
                        <span class="duel-quick-access-title">Voir le classement duel</span>
                        <p class="duel-quick-access-text">Le classement duel est maintenant sur une page dediee. Cette page reste concentree sur vos duels actuels, vos demandes en attente et votre historique personnel.</p>
                    </div>
                    <div class="duel-quick-access-actions">
                        <a href="{{ route($duelLeaderboardRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item no-transition">
                            <span data-hover="Classement duel">Classement duel</span>
                        </a>
                        <a href="{{ route($statisticsRouteName) }}" class="tt-btn tt-btn-primary tt-magnetic-item no-transition">
                            <span data-hover="Statistiques">Statistiques</span>
                        </a>
                    </div>
                </div>

                <div class="duel-summary-grid">
                    <article class="duel-summary-card">
                        <span class="duel-summary-value">{{ (int) ($summary['all'] ?? 0) }}</span>
                        <span class="text-muted">Total duels</span>
                    </article>
                    <article class="duel-summary-card">
                        <span class="duel-summary-value">{{ (int) ($summary['needs_response'] ?? 0) }}</span>
                        <span class="text-muted">A repondre</span>
                    </article>
                    <article class="duel-summary-card">
                        <span class="duel-summary-value">{{ (int) ($summary['sent_pending'] ?? 0) }}</span>
                        <span class="text-muted">Lances par vous</span>
                    </article>
                </div>

                @if(($duels ?? null) && $duels->count())
                    <div class="tt-portfolio-compact-list pcl-caption-hover pcl-image-hover">
                        <div class="pcli-inner">
                            @foreach($duels as $duel)
                                <article class="pcli-item duel-item-card tt-anim-fadeinup">
                                    <div class="pcli-item-inner">
                                        <div class="pcli-col pcli-col-image">
                                            <div class="pcli-image">
                                                <img src="{{ $duel['cover_image'] }}" loading="lazy" alt="Profil {{ $duel['opponent_name'] }}">
                                            </div>
                                        </div>

                                        <div class="pcli-col pcli-col-count">
                                            <div class="pcli-count"></div>
                                        </div>

                                        <div class="pcli-col pcli-col-caption">
                                            <div class="pcli-caption">
                                                <h2 class="pcli-title">{{ $duel['title'] }} vs {{ $duel['opponent_name'] }}</h2>

                                                <div class="pcli-categories">
                                                    <span class="duel-status-pill {{ $duel['status_class'] }}">{{ $duel['status_label'] }}</span>
                                                    <span class="duel-status-pill">{{ $duel['role_label'] }}</span>
                                                </div>

                                                @if($duel['message'] !== '')
                                                    <p class="duel-card-message">{{ $duel['message'] }}</p>
                                                @endif

                                                <div class="duel-card-meta">
                                                    <span>Cree: {{ optional($duel['created_at'])->format('d/m/Y H:i') ?? '-' }}</span>
                                                    <span>{{ $duel['expires_label'] }}</span>
                                                </div>

                                                @if($duel['can_respond'])
                                                    <div class="duel-card-actions">
                                                        <form method="POST" action="{{ route('duels.accept', $duel['id']) }}">
                                                            @csrf
                                                            <button type="submit" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                                                <span data-hover="Accepter">Accepter</span>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('duels.refuse', $duel['id']) }}">
                                                            @csrf
                                                            <button type="submit" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                                                <span data-hover="Refuser">Refuser</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    @if($duels->hasPages())
                        @php
                            $windowStart = max(1, $duels->currentPage() - 1);
                            $windowEnd = min($duels->lastPage(), $duels->currentPage() + 1);
                        @endphp
                        <div class="tt-pagination tt-pagin-center padding-top-80 padding-top-xlg-100 tt-anim-fadeinup">
                            <div class="tt-pagin-prev">
                                <a href="{{ $duels->previousPageUrl() ?: '#' }}" class="tt-pagin-item tt-magnetic-item {{ $duels->onFirstPage() ? 'duel-pagin-item-disabled' : '' }}">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                            <div class="tt-pagin-numbers">
                                @for($page = $windowStart; $page <= $windowEnd; $page++)
                                    <a href="{{ $duels->url($page) }}" class="tt-pagin-item tt-magnetic-item {{ $duels->currentPage() === $page ? 'active' : '' }}">{{ $page }}</a>
                                @endfor
                            </div>
                            <div class="tt-pagin-next">
                                <a href="{{ $duels->nextPageUrl() ?: '#' }}" class="tt-pagin-item tt-pagin-next tt-magnetic-item {{ $duels->hasMorePages() ? '' : 'duel-pagin-item-disabled' }}">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="duel-card-empty">
                        Aucun duel pour ce filtre.
                    </div>
                @endif
            </div>
        </div>

        <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="tt-row">
                    <div class="tt-col-lg-4">
                        <div class="tt-heading tt-heading-lg">
                            <h3 class="tt-heading-subtitle">Aide rapide</h3>
                            <h2 class="tt-heading-title">Comment<br>fonctionnent les duels ?</h2>
                        </div>
                        <p class="text-muted">
                            Les duels pending attendent une reponse. Les duels actifs sont acceptes. Les duels finished regroupent refuses et expires.
                        </p>
                    </div>

                    <div class="tt-col-lg-8 tt-align-self-center">
                        <div class="tt-accordion tt-ac-sm tt-ac-borders tt-ac-counter">
                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">Qui peut accepter un duel ?</h4>
                                        </div>
                                    </div>
                                    <div class="tt-accordion-caret">
                                        <div class="tt-accordion-caret-inner tt-magnetic-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p>Seul le joueur challenge peut accepter ou refuser un duel pending.</p>
                                </div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">Que se passe-t-il apres expiration ?</h4>
                                        </div>
                                    </div>
                                    <div class="tt-accordion-caret">
                                        <div class="tt-accordion-caret-inner tt-magnetic-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p>Le duel passe en statut expire et il apparait dans l onglet finished.</p>
                                </div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">Comment lancer un nouveau duel ?</h4>
                                        </div>
                                    </div>
                                    <div class="tt-accordion-caret">
                                        <div class="tt-accordion-caret-inner tt-magnetic-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p>Clique sur "Creer un duel", choisis un joueur, ajoute un message et envoie la demande.</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
@endsection
