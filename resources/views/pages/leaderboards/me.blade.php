@extends('marketing.layouts.template')

@section('title', 'Ma ligue | ERAH Plateforme')
@section('meta_description', 'Suivez votre progression de ligue, vos points et votre position dans le leaderboard.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .lb-me-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .lb-me-title h1 {
            margin: 0;
            font-size: clamp(36px, 6vw, 64px);
            line-height: .92;
        }

        .lb-me-title p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, .72);
        }

        .lb-me-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .lb-me-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .lb-me-kpi {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 14px 16px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .lb-me-kpi strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .lb-me-kpi span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .7);
        }

        .lb-me-main {
            display: grid;
            grid-template-columns: 1fr 1.6fr;
            gap: 12px;
            margin-bottom: 18px;
        }

        .lb-me-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 16px;
            background: rgba(255, 255, 255, .02);
        }

        .lb-me-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .lb-me-profile-link {
            display: inline-flex;
            align-items: center;
            gap: inherit;
            color: inherit;
            text-decoration: none;
            transition: opacity .2s ease, transform .2s ease;
        }

        .lb-me-profile-link:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .lb-me-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, .2);
            background: rgba(255, 255, 255, .08);
        }

        .lb-me-profile strong {
            display: block;
            font-size: 20px;
            line-height: 1.1;
            font-weight: 700;
        }

        .lb-me-profile span {
            color: rgba(255, 255, 255, .72);
            font-size: 13px;
        }

        .lb-me-badge {
            display: inline-flex;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-right: 8px;
            margin-top: 8px;
        }

        .lb-me-progress-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .lb-me-progress-head strong {
            font-size: 26px;
            line-height: 1;
        }

        .lb-me-progress-track {
            width: 100%;
            height: 9px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            overflow: hidden;
        }

        .lb-me-progress-track span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #42d392 0%, #53b3ff 100%);
        }

        .lb-me-progress-note {
            margin-top: 10px;
            font-size: 13px;
            color: rgba(255, 255, 255, .72);
        }

        .lb-me-context {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            overflow: hidden;
        }

        .lb-me-row {
            display: grid;
            grid-template-columns: 90px 1.8fr 140px 120px;
            gap: 10px;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, .09);
        }

        .lb-me-row:last-child {
            border-bottom: 0;
        }

        .lb-me-row.header {
            background: rgba(255, 255, 255, .03);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255, 255, 255, .62);
        }

        .lb-me-row.is-me {
            background: rgba(70, 176, 255, .1);
        }

        .lb-me-rank {
            font-size: 28px;
            line-height: 1;
            font-weight: 700;
        }

        .lb-me-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .lb-me-user strong {
            display: block;
            font-size: 15px;
            font-weight: 600;
        }

        .lb-me-user small {
            color: rgba(255, 255, 255, .64);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .lb-me-value {
            text-align: right;
            font-size: 17px;
            font-weight: 700;
            line-height: 1;
        }

        .lb-me-empty {
            border: 1px dashed rgba(255, 255, 255, .18);
            border-radius: 14px;
            padding: 28px;
            text-align: center;
            color: rgba(255, 255, 255, .72);
        }

        @media (max-width: 1199.98px) {
            .lb-me-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .lb-me-main {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 991.98px) {
            .lb-me-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .lb-me-row.header {
                display: none;
            }

            .lb-me-value {
                text-align: left;
            }
        }

        @media (max-width: 767.98px) {
            .lb-me-kpi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.leaderboards.index' : 'leaderboards.index';
        $showRouteName = $isPublicApp ? 'app.leaderboards.show' : 'leaderboards.show';
        $entries = collect($leaderboard['entries'] ?? []);
        $myEntry = $entries->firstWhere('user_id', $user->id);
        $myPosition = (int) ($myEntry['position'] ?? 0);
        $currentLeagueName = (string) ($progress->league?->name ?? 'N/A');
        $currentPoints = (int) ($progress->total_rank_points ?? 0);
        $currentXp = (int) ($progress->total_xp ?? 0);
        $nextMin = $nextLeague ? (int) $nextLeague->min_rank_points : null;
        $gap = $nextMin !== null ? max(0, $nextMin - $currentXp) : 0;
        $currentMin = (int) ($progress->league?->min_rank_points ?? 0);
        $denominator = $nextMin !== null ? max(1, $nextMin - $currentMin) : 1;
        $numerator = max(0, $currentXp - $currentMin);
        $progressPercent = $nextMin !== null ? (int) min(100, round(($numerator / $denominator) * 100)) : 100;
        $avatarFallback = '/app-ui/assets/img/blog/avatar.png';
        $myAvatar = (string) (($user->avatar_url ?? '') !== '' ? $user->avatar_url : $avatarFallback);
        $publicProfileRouteName = 'users.public';
        $contextEntries = $myPosition > 0
            ? $entries->filter(fn ($entry) => abs(((int) ($entry['position'] ?? 0)) - $myPosition) <= 2)->values()
            : $entries->take(5)->values();
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
                    <h2 class="ph-caption-subtitle">Mon classement</h2>
                    <h1 class="ph-caption-title">{{ $currentLeagueName }}</h1>
                    <div class="ph-caption-description max-width-800">
                        {{ $currentXp }} XP - position {{ $myPosition > 0 ? '#'.$myPosition : 'non classee' }}.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Mon classement</h2>
                        <h1 class="ph-caption-title">{{ $currentLeagueName }}</h1>
                        <div class="ph-caption-description max-width-800">
                            Suivi perso de ta progression et de ta position.
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
                <header class="lb-me-head tt-anim-fadeinup">
                    <div class="lb-me-title">
                        <h1>Ma ligue: {{ $currentLeagueName }}</h1>
                        <p>{{ $currentXp }} XP et {{ $currentPoints }} points classement. Position actuelle: {{ $myPosition > 0 ? '#'.$myPosition : 'non classee' }}.</p>
                    </div>

                    <div class="lb-me-actions">
                        <a href="{{ route($indexRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                            <span data-hover="Toutes les ligues">Toutes les ligues</span>
                        </a>
                        @if(!empty($leaderboard['league']['key']))
                            <a href="{{ route($showRouteName, $leaderboard['league']['key']) }}" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                <span data-hover="Classement complet">Classement complet</span>
                            </a>
                        @endif
                    </div>
                </header>

                <section class="lb-me-kpi-grid">
                    <article class="lb-me-kpi tt-anim-fadeinup">
                        <strong>{{ $currentPoints }}</strong>
                        <span>Points classement</span>
                    </article>
                    <article class="lb-me-kpi tt-anim-fadeinup">
                        <strong>{{ $currentXp }}</strong>
                        <span>XP total</span>
                    </article>
                    <article class="lb-me-kpi tt-anim-fadeinup">
                        <strong>{{ $myPosition > 0 ? '#'.$myPosition : '-' }}</strong>
                        <span>Position actuelle</span>
                    </article>
                    <article class="lb-me-kpi tt-anim-fadeinup">
                        <strong>{{ $nextLeague ? $gap : 0 }}</strong>
                        <span>XP avant {{ $nextLeague?->name ?? 'max ligue' }}</span>
                    </article>
                </section>

                <section class="lb-me-main">
                    <article class="lb-me-card tt-anim-fadeinup">
                        <div class="lb-me-profile">
                            <a href="{{ route($publicProfileRouteName, $user) }}" class="lb-me-profile-link">
                                <img src="{{ $myAvatar }}" alt="{{ $user->name }}" class="lb-me-avatar">
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    <span>{{ $currentLeagueName }}</span>
                                </div>
                            </a>
                        </div>
                        <div>
                            <span class="lb-me-badge">Position {{ $myPosition > 0 ? '#'.$myPosition : 'N/A' }}</span>
                            <span class="lb-me-badge">{{ $entries->count() }} joueurs dans la ligue</span>
                        </div>
                    </article>

                    <article class="lb-me-card tt-anim-fadeinup">
                        <div class="lb-me-progress-head">
                            <span>Progression vers prochaine ligue</span>
                            <strong>{{ $progressPercent }}%</strong>
                        </div>
                        <div class="lb-me-progress-track">
                            <span style="width: {{ $progressPercent }}%"></span>
                        </div>
                        <div class="lb-me-progress-note">
                            @if($nextLeague)
                                Encore <strong>{{ $gap }}</strong> XP pour atteindre <strong>{{ $nextLeague->name }}</strong>.
                            @else
                                Vous etes deja dans la ligue la plus haute.
                            @endif
                        </div>
                    </article>
                </section>

                @if($contextEntries->count())
                    <section class="lb-me-context tt-anim-fadeinup">
                        <div class="lb-me-row header">
                            <div>Position</div>
                            <div>Joueur</div>
                            <div style="text-align:right;">Points classement</div>
                            <div style="text-align:right;">XP</div>
                        </div>

                        @foreach($contextEntries as $entry)
                            @php
                                $isMe = (int) ($entry['user_id'] ?? 0) === (int) $user->id;
                                $avatar = (string) ($entry['avatar_url'] ?? $avatarFallback);
                                $profileUrl = !empty($entry['user_id']) ? route($publicProfileRouteName, $entry['user_id']) : null;
                            @endphp
                            <article class="lb-me-row {{ $isMe ? 'is-me' : '' }}">
                                <div class="lb-me-rank">#{{ (int) ($entry['position'] ?? 0) }}</div>

                                <div class="lb-me-user">
                                    @if($profileUrl)
                                        <a href="{{ $profileUrl }}" class="lb-me-profile-link">
                                            <img src="{{ $avatar !== '' ? $avatar : $avatarFallback }}" alt="{{ $entry['name'] ?? 'Joueur' }}" class="lb-me-avatar">
                                            <div>
                                                <strong>{{ $entry['name'] ?? 'Joueur inconnu' }}</strong>
                                                <small>{{ $isMe ? 'vous' : 'joueur' }}</small>
                                            </div>
                                        </a>
                                    @else
                                        <img src="{{ $avatar !== '' ? $avatar : $avatarFallback }}" alt="{{ $entry['name'] ?? 'Joueur' }}" class="lb-me-avatar">
                                        <div>
                                            <strong>{{ $entry['name'] ?? 'Joueur inconnu' }}</strong>
                                            <small>{{ $isMe ? 'vous' : 'joueur' }}</small>
                                        </div>
                                    @endif
                                </div>

                                <div class="lb-me-value">{{ (int) ($entry['total_rank_points'] ?? 0) }}</div>
                                <div class="lb-me-value">{{ (int) ($entry['total_xp'] ?? 0) }}</div>
                            </article>
                        @endforeach
                    </section>
                @else
                    <div class="lb-me-empty">Aucun joueur n est encore classe dans votre ligue.</div>
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
