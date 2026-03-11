@extends('marketing.layouts.template')

@section('title', 'Classements | ERAH Plateforme')
@section('meta_description', 'Explorez les ligues et accedez au classement dynamique des joueurs ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .lb-console-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 26px;
        }

        .lb-console-title h1 {
            margin: 0 0 6px;
            font-size: clamp(38px, 5.8vw, 68px);
            line-height: .95;
        }

        .lb-console-title p {
            margin: 0;
            color: rgba(255, 255, 255, .72);
            max-width: 720px;
        }

        .lb-console-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .lb-console-kpis {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 22px;
        }

        .lb-console-kpi {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 14px 16px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .lb-console-kpi strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .lb-console-kpi span {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .72);
        }

        .lb-league-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .lb-league-card {
            position: relative;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 16px;
            padding: 16px;
            background:
                radial-gradient(1200px 260px at -10% -80%, rgba(255, 255, 255, .09), transparent 50%),
                rgba(255, 255, 255, .02);
            overflow: hidden;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .lb-league-card::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 3px;
            background: var(--lb-tone, #6fa8ff);
        }

        .lb-tone-bronze { --lb-tone: #cf8e4d; }
        .lb-tone-argent { --lb-tone: #a7b7c7; }
        .lb-tone-or { --lb-tone: #f4c35f; }
        .lb-tone-platine { --lb-tone: #6ee1d8; }
        .lb-tone-diamant { --lb-tone: #80a9ff; }
        .lb-tone-master { --lb-tone: #ff8db0; }

        .lb-league-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .lb-league-name {
            margin: 0;
            font-size: 30px;
            line-height: .95;
            font-weight: 700;
        }

        .lb-league-key {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 999px;
            padding: 4px 10px;
            color: rgba(255, 255, 255, .82);
        }

        .lb-league-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .lb-league-metric {
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 10px;
            padding: 10px;
            background: rgba(0, 0, 0, .2);
        }

        .lb-league-metric strong {
            display: block;
            font-size: 22px;
            line-height: 1;
            margin-bottom: 4px;
        }

        .lb-league-metric span {
            font-size: 12px;
            color: rgba(255, 255, 255, .68);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .lb-league-meter {
            display: grid;
            gap: 8px;
        }

        .lb-league-meter-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 13px;
            color: rgba(255, 255, 255, .74);
        }

        .lb-league-meter-track {
            width: 100%;
            height: 7px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .1);
            overflow: hidden;
        }

        .lb-league-meter-track span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--lb-tone, #6fa8ff), rgba(255, 255, 255, .95));
        }

        .lb-league-footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .lb-league-champion {
            font-size: 13px;
            color: rgba(255, 255, 255, .72);
        }

        .lb-empty {
            border: 1px dashed rgba(255, 255, 255, .2);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            color: rgba(255, 255, 255, .74);
        }

        @media (max-width: 1199.98px) {
            .lb-console-kpis {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .lb-league-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .lb-console-kpis,
            .lb-league-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $showRouteName = $isPublicApp ? 'app.leaderboards.show' : 'leaderboards.show';
        $myRouteName = $isPublicApp ? 'app.leaderboards.me' : 'leaderboards.me';
        $cards = collect($leagueCards ?? [])->values();
        $maxLeagueMinPoints = max(1, (int) $cards->max('min_rank_points'));
        $bestLeagueName = (string) (($bestLeague['name'] ?? null) ?: '-');
        $bestLeagueScore = (int) ($bestLeague['top_rank_points'] ?? 0);
        $leagueToneMap = [
            'bronze' => 'lb-tone-bronze',
            'argent' => 'lb-tone-argent',
            'or' => 'lb-tone-or',
            'platine' => 'lb-tone-platine',
            'diamant' => 'lb-tone-diamant',
            'master' => 'lb-tone-master',
        ];
    @endphp

    <div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="ph-video ph-video-cover-6">
            <div class="ph-video-inner">
                <video loop muted autoplay playsinline preload="metadata" poster="/template/assets/vids/1920/video-1-1920.jpg">
                    <source src="/template/assets/vids/placeholder.mp4" data-src="/template/assets/vids/1920/video-1-1920.mp4" type="video/mp4">
                    <source src="/template/assets/vids/placeholder.webm" data-src="/template/assets/vids/1920/video-1-1920.webm" type="video/webm">
                </video>
            </div>
        </div>

        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Ranking</h2>
                    <h1 class="ph-caption-title">Classements</h1>
                    <div class="ph-caption-description max-width-800">
                        Console des ligues, top joueurs et progression en direct.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Ranking</h2>
                        <h1 class="ph-caption-title">Classements</h1>
                        <div class="ph-caption-description max-width-800">
                            Explorez les ligues de Bronze a Master.
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
        <div class="tt-section padding-top-60 padding-bottom-40 border-bottom">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <header class="lb-console-head tt-anim-fadeinup">
                    <div class="lb-console-title">
                        <h1>Classements</h1>
                        <p>Vue claire des ligues, des leaders et du volume de joueurs actifs. Les donnees sont actualisees en temps reel.</p>
                    </div>

                    <div class="lb-console-actions">
                        @if($isPublicApp)
                            @auth
                                <a href="{{ route($myRouteName) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Ma ligue">Ma ligue</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Connexion">Connexion</span>
                                </a>
                            @endauth
                        @else
                            <a href="{{ route($myRouteName) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Ma ligue">Ma ligue</span>
                            </a>
                        @endif
                    </div>
                </header>

                <section class="lb-console-kpis">
                    <article class="lb-console-kpi tt-anim-fadeinup">
                        <strong>{{ (int) ($cards->count()) }}</strong>
                        <span>Ligues actives</span>
                    </article>
                    <article class="lb-console-kpi tt-anim-fadeinup">
                        <strong>{{ (int) ($totalPlayers ?? 0) }}</strong>
                        <span>Joueurs classes</span>
                    </article>
                    <article class="lb-console-kpi tt-anim-fadeinup">
                        <strong>{{ (int) ($averagePlayersPerLeague ?? 0) }}</strong>
                        <span>Joueurs / ligue</span>
                    </article>
                    <article class="lb-console-kpi tt-anim-fadeinup">
                        <strong>{{ $bestLeagueName }}</strong>
                        <span>Top score: {{ $bestLeagueScore }} pts</span>
                    </article>
                </section>

                @if($cards->count())
                    <section class="lb-league-grid">
                        @foreach($cards as $card)
                            @php
                                $leagueKey = (string) ($card['key'] ?? '');
                                $toneClass = $leagueToneMap[$leagueKey] ?? '';
                                $meterPercent = (int) round(((int) ($card['min_rank_points'] ?? 0) / $maxLeagueMinPoints) * 100);
                            @endphp
                            <article class="lb-league-card {{ $toneClass }} tt-anim-fadeinup">
                                <div class="lb-league-head">
                                    <h2 class="lb-league-name">{{ (string) ($card['name'] ?? '-') }}</h2>
                                    <span class="lb-league-key">{{ strtoupper($leagueKey) }}</span>
                                </div>

                                <div class="lb-league-metrics">
                                    <div class="lb-league-metric">
                                        <strong>{{ (int) ($card['players_count'] ?? 0) }}</strong>
                                        <span>Joueurs</span>
                                    </div>
                                    <div class="lb-league-metric">
                                        <strong>{{ (int) ($card['average_rank_points'] ?? 0) }}</strong>
                                        <span>Moyenne</span>
                                    </div>
                                </div>

                                <div class="lb-league-meter">
                                    <div class="lb-league-meter-head">
                                        <span>Seuil entree</span>
                                        <strong>{{ (int) ($card['min_rank_points'] ?? 0) }} pts</strong>
                                    </div>
                                    <div class="lb-league-meter-track">
                                        <span style="width: {{ min(100, max(0, $meterPercent)) }}%"></span>
                                    </div>
                                </div>

                                <footer class="lb-league-footer">
                                    <div class="lb-league-champion">
                                        Leader: <strong>{{ (string) (($card['top_name'] ?? null) ?: 'Aucun joueur') }}</strong>
                                    </div>
                                    <a href="{{ route($showRouteName, $leagueKey) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                        <span data-hover="Voir classement">Voir classement</span>
                                    </a>
                                </footer>
                            </article>
                        @endforeach
                    </section>
                @else
                    <div class="lb-empty">Aucune ligue active pour le moment.</div>
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
