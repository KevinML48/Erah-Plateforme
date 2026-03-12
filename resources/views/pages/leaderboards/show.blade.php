@extends('marketing.layouts.template')

@section('title', 'Classement ligue | ERAH Plateforme')
@section('meta_description', 'Classement dynamique par ligue avec top joueurs et progression points.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .lb-view-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .lb-view-title h1 {
            margin: 0;
            font-size: clamp(36px, 6vw, 66px);
            line-height: .92;
        }

        .lb-view-title p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, .72);
            max-width: 720px;
        }

        .lb-view-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .lb-switch {
            position: sticky;
            top: 88px;
            z-index: 9;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 14px;
            background: rgba(10, 10, 10, .72);
            backdrop-filter: blur(10px);
        }

        .lb-switch-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .85);
            transition: .2s ease;
        }

        .lb-switch-link:hover,
        .lb-switch-link.active {
            border-color: rgba(87, 196, 255, .65);
            color: #d4f2ff;
            background: rgba(87, 196, 255, .1);
        }

        .lb-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 22px;
        }

        .lb-kpi-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 14px 16px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .lb-kpi-card strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .lb-kpi-card span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .7);
        }

        .lb-podium {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 22px;
        }

        .lb-podium-item {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 14px;
            background: radial-gradient(600px 180px at -20% -100%, rgba(255, 255, 255, .08), transparent 55%), rgba(255, 255, 255, .02);
        }

        .lb-podium-rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .24);
            font-size: 12px;
            margin-bottom: 10px;
        }

        .lb-podium-user {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 10px;
        }

        .lb-profile-link {
            display: inline-flex;
            align-items: center;
            gap: inherit;
            color: inherit;
            text-decoration: none;
            transition: opacity .2s ease, transform .2s ease;
        }

        .lb-profile-link:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .lb-podium-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, .2);
            background: rgba(255, 255, 255, .08);
        }

        .lb-podium-name {
            font-size: 17px;
            font-weight: 600;
            line-height: 1.1;
        }

        .lb-podium-meta {
            color: rgba(255, 255, 255, .72);
            font-size: 13px;
            line-height: 1.4;
        }

        .lb-supporter-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 5px;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .84);
        }

        .lb-board {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            overflow: hidden;
        }

        .lb-row {
            display: grid;
            grid-template-columns: 90px 1.8fr 150px 130px;
            gap: 10px;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, .09);
        }

        .lb-row:last-child {
            border-bottom: 0;
        }

        .lb-row-header {
            background: rgba(255, 255, 255, .03);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255, 255, 255, .62);
        }

        .lb-row.is-me {
            background: rgba(70, 176, 255, .08);
        }

        .lb-rank-value {
            font-size: 28px;
            line-height: 1;
            font-weight: 700;
            opacity: .9;
        }

        .lb-user-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .lb-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, .2);
            background: rgba(255, 255, 255, .08);
        }

        .lb-user-wrap strong {
            display: block;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lb-you-badge {
            display: inline-flex;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 999px;
            padding: 2px 7px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: 3px;
        }

        .lb-value {
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
            text-align: right;
        }

        .lb-value small {
            display: block;
            margin-top: 3px;
            color: rgba(255, 255, 255, .62);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 500;
        }

        .lb-empty {
            border: 1px dashed rgba(255, 255, 255, .18);
            border-radius: 14px;
            padding: 30px;
            text-align: center;
            color: rgba(255, 255, 255, .72);
        }

        @media (max-width: 1199.98px) {
            .lb-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .lb-podium {
                grid-template-columns: 1fr;
            }

            .lb-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .lb-row-header {
                display: none;
            }

            .lb-rank-value {
                font-size: 24px;
            }

            .lb-value {
                text-align: left;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.leaderboards.index' : 'leaderboards.index';
        $showRouteName = $isPublicApp ? 'app.leaderboards.show' : 'leaderboards.show';
        $myRouteName = $isPublicApp ? 'app.leaderboards.me' : 'leaderboards.me';
        $entries = collect($leaderboard['entries'] ?? []);
        $leagueName = (string) ($leaderboard['league']['name'] ?? strtoupper((string) $leagueKey));
        $leagueMin = (int) ($leaderboard['league']['min_rank_points'] ?? 0);
        $topEntry = $entries->first();
        $topScore = (int) ($topEntry['total_rank_points'] ?? 0);
        $averageScore = $entries->count() > 0 ? (int) round((float) $entries->avg('total_rank_points')) : 0;
        $totalXp = (int) $entries->sum('total_xp');
        $topThree = $entries->take(3);
        $avatarFallback = '/app-ui/assets/img/blog/avatar.png';
        $publicProfileRouteName = 'users.public';
    @endphp

    <div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="ph-video ph-video-cover-6">
            <div class="ph-video-inner">
                <video loop muted autoplay playsinline preload="metadata" poster="/template/assets/vids/1920/video-2-1920.jpg">
                    <source src="/template/assets/vids/placeholder.mp4" data-src="/template/assets/vids/1920/video-2-1920.mp4" type="video/mp4">
                    <source src="/template/assets/vids/placeholder.webm" data-src="/template/assets/vids/1920/video-2-1920.webm" type="video/webm">
                </video>
            </div>
        </div>

        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">League Board</h2>
                    <h1 class="ph-caption-title">{{ $leagueName }}</h1>
                    <div class="ph-caption-description max-width-800">
                        {{ $entries->count() }} joueur(s) classes - minimum {{ $leagueMin }} points.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">League Board</h2>
                        <h1 class="ph-caption-title">{{ $leagueName }}</h1>
                        <div class="ph-caption-description max-width-800">
                            Positionnements et performances de la ligue.
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
                <header class="lb-view-head tt-anim-fadeinup">
                    <div class="lb-view-title">
                        <h1>{{ $leagueName }}</h1>
                        <p>Seuil minimum: {{ $leagueMin }} points. {{ $entries->count() }} joueur(s) classes dans cette ligue.</p>
                    </div>

                    <div class="lb-view-actions">
                        <a href="{{ route($indexRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                            <span data-hover="Toutes les ligues">Toutes les ligues</span>
                        </a>
                        @if($isPublicApp)
                            @auth
                                <a href="{{ route($myRouteName) }}" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                    <span data-hover="Ma ligue">Ma ligue</span>
                                </a>
                            @endauth
                        @else
                            <a href="{{ route($myRouteName) }}" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                <span data-hover="Ma ligue">Ma ligue</span>
                            </a>
                        @endif
                    </div>
                </header>

                <nav class="lb-switch tt-anim-fadeinup">
                    @foreach($leagues ?? [] as $league)
                        <a href="{{ route($showRouteName, $league->key) }}" class="lb-switch-link {{ $leagueKey === $league->key ? 'active' : '' }}">
                            {{ $league->name }}
                        </a>
                    @endforeach
                </nav>

                <section class="lb-kpi-grid">
                    <article class="lb-kpi-card tt-anim-fadeinup">
                        <strong>{{ $entries->count() }}</strong>
                        <span>Joueurs classes</span>
                    </article>
                    <article class="lb-kpi-card tt-anim-fadeinup">
                        <strong>{{ $topScore }}</strong>
                        <span>Top rank points</span>
                    </article>
                    <article class="lb-kpi-card tt-anim-fadeinup">
                        <strong>{{ $averageScore }}</strong>
                        <span>Moyenne rank points</span>
                    </article>
                    <article class="lb-kpi-card tt-anim-fadeinup">
                        <strong>{{ $totalXp }}</strong>
                        <span>XP cumule</span>
                    </article>
                </section>

                @if($topThree->count())
                    <section class="lb-podium">
                        @foreach($topThree as $entry)
                            @php($avatar = (string) ($entry['avatar_url'] ?? $avatarFallback))
                            @php($profileUrl = !empty($entry['user_id']) ? route($publicProfileRouteName, $entry['user_id']) : null)
                            <article class="lb-podium-item tt-anim-fadeinup">
                                <span class="lb-podium-rank">#{{ (int) ($entry['position'] ?? 0) }}</span>
                                <div class="lb-podium-user">
                                    @if($profileUrl)
                                        <a href="{{ $profileUrl }}" class="lb-profile-link">
                                            <img src="{{ $avatar !== '' ? $avatar : $avatarFallback }}" alt="{{ $entry['name'] ?? 'Joueur' }}" class="lb-podium-avatar">
                                            <div>
                                                <div class="lb-podium-name">{{ $entry['name'] ?? 'Joueur inconnu' }}</div>
                                                @if(($currentUserId ?? null) === ($entry['user_id'] ?? null))
                                                    <small class="text-muted">Vous</small>
                                                @endif
                                                @if($entry['is_supporter'] ?? false)
                                                    <span class="lb-supporter-badge">Supporter</span>
                                                @endif
                                            </div>
                                        </a>
                                    @else
                                        <img src="{{ $avatar !== '' ? $avatar : $avatarFallback }}" alt="{{ $entry['name'] ?? 'Joueur' }}" class="lb-podium-avatar">
                                        <div>
                                            <div class="lb-podium-name">{{ $entry['name'] ?? 'Joueur inconnu' }}</div>
                                            @if(($currentUserId ?? null) === ($entry['user_id'] ?? null))
                                                <small class="text-muted">Vous</small>
                                            @endif
                                            @if($entry['is_supporter'] ?? false)
                                                <span class="lb-supporter-badge">Supporter</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="lb-podium-meta">
                                    {{ (int) ($entry['total_rank_points'] ?? 0) }} points classement
                                    <br>
                                    {{ (int) ($entry['total_xp'] ?? 0) }} XP total
                                </div>
                            </article>
                        @endforeach
                    </section>
                @endif

                @if($entries->count())
                    <section class="lb-board tt-anim-fadeinup">
                        <div class="lb-row lb-row-header">
                            <div>Position</div>
                            <div>Joueur</div>
                            <div style="text-align:right;">Points classement</div>
                            <div style="text-align:right;">XP</div>
                        </div>

                        @foreach($entries as $entry)
                            @php($avatar = (string) ($entry['avatar_url'] ?? $avatarFallback))
                            @php($isMe = (($currentUserId ?? null) === ($entry['user_id'] ?? null)))
                            @php($profileUrl = !empty($entry['user_id']) ? route($publicProfileRouteName, $entry['user_id']) : null)
                            <article class="lb-row {{ $isMe ? 'is-me' : '' }}">
                                <div class="lb-rank-value">#{{ (int) ($entry['position'] ?? 0) }}</div>

                                <div class="lb-user-wrap">
                                    @if($profileUrl)
                                        <a href="{{ $profileUrl }}" class="lb-profile-link">
                                            <img src="{{ $avatar !== '' ? $avatar : $avatarFallback }}" alt="{{ $entry['name'] ?? 'Joueur' }}" class="lb-user-avatar">
                                            <div>
                                                <strong>{{ $entry['name'] ?? 'Joueur inconnu' }}</strong>
                                                @if($isMe)
                                                    <span class="lb-you-badge">Vous</span>
                                                @endif
                                                @if($entry['is_supporter'] ?? false)
                                                    <span class="lb-supporter-badge">Supporter</span>
                                                @endif
                                            </div>
                                        </a>
                                    @else
                                        <img src="{{ $avatar !== '' ? $avatar : $avatarFallback }}" alt="{{ $entry['name'] ?? 'Joueur' }}" class="lb-user-avatar">
                                        <div>
                                            <strong>{{ $entry['name'] ?? 'Joueur inconnu' }}</strong>
                                            @if($isMe)
                                                <span class="lb-you-badge">Vous</span>
                                            @endif
                                            @if($entry['is_supporter'] ?? false)
                                                <span class="lb-supporter-badge">Supporter</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="lb-value">
                                    {{ (int) ($entry['total_rank_points'] ?? 0) }}
                                    <small>Points classement</small>
                                </div>

                                <div class="lb-value">
                                    {{ (int) ($entry['total_xp'] ?? 0) }}
                                    <small>XP</small>
                                </div>
                            </article>
                        @endforeach
                    </section>
                @else
                    <div class="lb-empty">Le leaderboard de cette ligue est vide pour le moment.</div>
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
