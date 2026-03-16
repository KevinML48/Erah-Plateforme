@extends('marketing.layouts.template')

@section('title', 'Notifications | ERAH Plateforme')
@section('meta_description', 'Centre de notifications dynamique: timeline, filtres et actions de lecture.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .notif-toolbar {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .notif-layout {
            display: grid;
            gap: 18px;
        }

        .notif-toolbar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .notif-state-tabs,
        .notif-category-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .notif-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .86);
        }

        .notif-tab.active {
            border-color: rgba(90, 206, 255, .62);
            background: rgba(90, 206, 255, .1);
            color: #d8f4ff;
        }

        .notif-tab-count {
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 999px;
            padding: 1px 7px;
            font-size: 11px;
            line-height: 1.3;
        }

        .notif-toolbar-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .notif-toolbar-actions form {
            margin: 0;
        }

        .notif-toolbar-meta {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed rgba(255, 255, 255, .14);
            color: rgba(255, 255, 255, .68);
            font-size: 13px;
        }

        .notif-category-tabs {
            margin-top: 12px;
        }

        .notif-tab.tone-duel.active {
            border-color: rgba(255, 224, 118, .7);
            background: rgba(255, 224, 118, .12);
            color: #fff2cb;
        }

        .notif-tab.tone-clips.active {
            border-color: rgba(131, 241, 206, .62);
            background: rgba(131, 241, 206, .1);
            color: #d9fff3;
        }

        .notif-tab.tone-system.active {
            border-color: rgba(168, 193, 255, .62);
            background: rgba(168, 193, 255, .1);
            color: #dfe9ff;
        }

        .notif-tab.tone-match.active {
            border-color: rgba(255, 153, 153, .65);
            background: rgba(255, 153, 153, .1);
            color: #ffe0e0;
        }

        .notif-tab.tone-bet.active {
            border-color: rgba(207, 177, 255, .64);
            background: rgba(207, 177, 255, .1);
            color: #efe1ff;
        }

        .notif-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .notif-page-card,
        .notif-stream {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 16px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .notif-page-card {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .notif-page-card strong {
            display: block;
            font-size: 24px;
            line-height: 1.1;
            margin-top: 6px;
        }

        .notif-page-card p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, .72);
            line-height: 1.55;
        }

        .notif-page-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .notif-page-pill,
        .notif-day-label {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 6px 11px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .18);
            background: rgba(255, 255, 255, .04);
            color: rgba(255, 255, 255, .84);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .notif-stream {
            display: grid;
            gap: 16px;
        }

        .notif-stream-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .notif-stream-head h2 {
            margin: 0;
            font-size: 28px;
            line-height: 1.1;
        }

        .notif-stream-head p {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, .72);
            line-height: 1.55;
        }

        .notif-stream-count {
            color: rgba(255, 255, 255, .62);
            font-size: 13px;
        }

        .notif-day-group {
            display: grid;
            gap: 12px;
        }

        .notif-kpi-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 14px 16px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .notif-kpi-card strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .notif-kpi-card span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .7);
        }

        .notif-list {
            display: grid;
            gap: 12px;
        }

        .notif-item {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 16px;
            background: rgba(255, 255, 255, .02);
        }

        .notif-item.is-unread {
            border-color: rgba(89, 200, 255, .45);
            background: linear-gradient(160deg, rgba(89, 200, 255, .12), rgba(255, 255, 255, .02));
        }

        .notif-item-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .notif-item-meta {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }

        .notif-category-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .notif-category-badge.tone-duel {
            border-color: rgba(255, 224, 118, .52);
            color: #fff0c4;
        }

        .notif-category-badge.tone-clips {
            border-color: rgba(131, 241, 206, .52);
            color: #dbfff3;
        }

        .notif-category-badge.tone-system {
            border-color: rgba(168, 193, 255, .52);
            color: #e4ecff;
        }

        .notif-category-badge.tone-match {
            border-color: rgba(255, 153, 153, .52);
            color: #ffe2e2;
        }

        .notif-category-badge.tone-bet {
            border-color: rgba(207, 177, 255, .52);
            color: #efe2ff;
        }

        .notif-state-badge {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #d6f5ff;
            border-color: rgba(89, 200, 255, .52);
        }

        .notif-time {
            color: rgba(255, 255, 255, .62);
            font-size: 13px;
            white-space: nowrap;
        }

        .notif-title {
            margin: 0 0 6px;
            font-size: 18px;
            line-height: 1.2;
        }

        .notif-message {
            margin: 0;
            color: rgba(255, 255, 255, .8);
            line-height: 1.5;
        }

        .notif-item-actions {
            margin-top: 12px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .notif-item-actions form {
            margin: 0;
        }

        .notif-item-actions .tt-btn {
            border-radius: 999px;
            padding-inline: 16px;
        }

        .notif-muted {
            color: rgba(255, 255, 255, .62);
            font-size: 13px;
        }

        .notif-empty {
            border: 1px dashed rgba(255, 255, 255, .2);
            border-radius: 14px;
            padding: 28px;
            text-align: center;
            color: rgba(255, 255, 255, .74);
        }

        .notif-empty p {
            margin: 0 0 14px;
        }

        .notif-pagin-item-disabled {
            opacity: .35;
            pointer-events: none;
        }

        .notif-btn-disabled {
            opacity: .45;
            pointer-events: none;
        }

        @media (max-width: 1199.98px) {
            .notif-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .notif-kpi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.notifications.index' : 'notifications.index';
        $readRouteName = $isPublicApp ? 'app.notifications.read' : 'notifications.read';
        $readAllRouteName = $isPublicApp ? 'app.notifications.read-all' : 'notifications.read-all';
        $preferencesRouteName = $isPublicApp ? 'app.notifications.preferences' : 'notifications.preferences';

        $filters = array_merge([
            'state' => 'all',
            'category' => 'all',
        ], $filters ?? []);

        $summary = array_merge([
            'total' => 0,
            'unread' => 0,
            'read' => 0,
            'filtered' => 0,
        ], $summary ?? []);

        $stateCounts = array_merge([
            'all' => 0,
            'unread' => 0,
            'read' => 0,
        ], $stateCounts ?? []);

        $categoryCounts = collect($categoryCounts ?? []);

        $stateLabels = [
            'all' => 'Toutes',
            'unread' => 'Non lues',
            'read' => 'Lues',
        ];

        $categoryLabels = [
            'duel' => 'Duels',
            'clips' => 'Clips',
            'comment' => 'Commentaires',
            'mission' => 'Missions',
            'quiz' => 'Quiz',
            'live_code' => 'Codes live',
            'achievement' => 'Succes',
            'event' => 'Evenements',
            'system' => 'Systeme',
            'match' => 'Matchs',
            'bet' => 'Paris',
        ];

        $categoryToneMap = [
            'duel' => 'tone-duel',
            'clips' => 'tone-clips',
            'comment' => 'tone-clips',
            'mission' => 'tone-system',
            'quiz' => 'tone-system',
            'live_code' => 'tone-system',
            'achievement' => 'tone-system',
            'event' => 'tone-match',
            'system' => 'tone-system',
            'match' => 'tone-match',
            'bet' => 'tone-bet',
        ];

        $categoryIconMap = [
            'duel' => 'fa-solid fa-crosshairs',
            'clips' => 'fa-solid fa-clapperboard',
            'comment' => 'fa-solid fa-comments',
            'mission' => 'fa-solid fa-list-check',
            'quiz' => 'fa-solid fa-circle-question',
            'live_code' => 'fa-solid fa-bolt',
            'achievement' => 'fa-solid fa-medal',
            'event' => 'fa-solid fa-calendar-days',
            'system' => 'fa-solid fa-shield-halved',
            'match' => 'fa-solid fa-trophy',
            'bet' => 'fa-solid fa-coins',
        ];

        $currentState = (string) ($filters['state'] ?? 'all');
        if (! array_key_exists($currentState, $stateLabels)) {
            $currentState = 'all';
        }

        $currentCategory = (string) ($filters['category'] ?? 'all');
        if ($currentCategory !== 'all' && ! array_key_exists($currentCategory, $categoryLabels)) {
            $currentCategory = 'all';
        }

        $buildParams = function (array $overrides = []) use ($currentState, $currentCategory): array {
            $params = [
                'state' => $currentState,
                'category' => $currentCategory,
            ];

            foreach ($overrides as $key => $value) {
                $params[$key] = $value;
            }

            if (($params['state'] ?? 'all') === 'all') {
                unset($params['state']);
            }

            if (($params['category'] ?? 'all') === 'all') {
                unset($params['category']);
            }

            return $params;
        };

        $notificationRows = collect(($notifications ?? null)?->items() ?? []);
        $pageUnreadNotifications = $notificationRows
            ->filter(fn ($notification) => $notification->read_at === null)
            ->values();
        $pageReadNotifications = $notificationRows
            ->reject(fn ($notification) => $notification->read_at === null)
            ->values();
        $hasSplitStreams = $currentState === 'all' && $pageUnreadNotifications->isNotEmpty() && $pageReadNotifications->isNotEmpty();
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
                    <h2 class="ph-caption-subtitle">ERAH Inbox</h2>
                    <h1 class="ph-caption-title">Notifications</h1>
                    <div class="ph-caption-description max-width-800">
                        {{ (int) ($summary['unread'] ?? 0) }} non lue(s) sur {{ (int) ($summary['total'] ?? 0) }} notification(s).
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Inbox</h2>
                        <h1 class="ph-caption-title">Notifications</h1>
                        <div class="ph-caption-description max-width-800">
                            Timeline, filtres categories et gestion des canaux.
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
                        <textPath xlink:href="#textcircle">Notification Center - Notification Center -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="notif-layout">
                    <section class="notif-toolbar tt-anim-fadeinup">
                        <div class="notif-toolbar-head">
                            <div class="notif-state-tabs">
                                @foreach($stateLabels as $stateKey => $stateLabel)
                                    <a href="{{ route($indexRouteName, $buildParams(['state' => $stateKey])) }}"
                                        class="notif-tab {{ $currentState === $stateKey ? 'active' : '' }}">
                                        {{ $stateLabel }}
                                        <span class="notif-tab-count">{{ (int) ($stateCounts[$stateKey] ?? 0) }}</span>
                                    </a>
                                @endforeach
                            </div>

                            <div class="notif-toolbar-actions">
                                <a href="{{ route($preferencesRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                    <span data-hover="Preferences">Preferences</span>
                                </a>

                                @if((int) ($summary['unread'] ?? 0) > 0)
                                    <form method="POST" action="{{ route($readAllRouteName) }}">
                                        @csrf
                                        <button type="submit" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                            <span data-hover="Tout marquer lu">Tout marquer lu</span>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="tt-btn tt-btn-outline tt-btn-sm notif-btn-disabled">Aucune non lue</button>
                                @endif
                            </div>
                        </div>

                        <div class="notif-category-tabs">
                            <a href="{{ route($indexRouteName, $buildParams(['category' => 'all'])) }}"
                                class="notif-tab {{ $currentCategory === 'all' ? 'active' : '' }}">
                                Toutes categories
                                <span class="notif-tab-count">{{ (int) ($summary['total'] ?? 0) }}</span>
                            </a>

                            @foreach($categoryLabels as $categoryKey => $categoryLabel)
                                @php
                                    $toneClass = $categoryToneMap[$categoryKey] ?? '';
                                @endphp
                                <a href="{{ route($indexRouteName, $buildParams(['category' => $categoryKey])) }}"
                                    class="notif-tab {{ $toneClass }} {{ $currentCategory === $categoryKey ? 'active' : '' }}">
                                    {{ $categoryLabel }}
                                    <span class="notif-tab-count">{{ (int) ($categoryCounts->get($categoryKey, 0)) }}</span>
                                </a>
                            @endforeach
                        </div>

                        <div class="notif-toolbar-meta">
                            Resultats filtres: {{ (int) ($summary['filtered'] ?? 0) }} notification(s) affichee(s).
                        </div>
                    </section>

                    <section class="notif-kpi-grid">
                        <article class="notif-kpi-card tt-anim-fadeinup">
                            <strong>{{ (int) ($summary['total'] ?? 0) }}</strong>
                            <span>Total</span>
                        </article>
                        <article class="notif-kpi-card tt-anim-fadeinup">
                            <strong>{{ (int) ($summary['unread'] ?? 0) }}</strong>
                            <span>Non lues</span>
                        </article>
                        <article class="notif-kpi-card tt-anim-fadeinup">
                            <strong>{{ (int) ($summary['read'] ?? 0) }}</strong>
                            <span>Lues</span>
                        </article>
                        <article class="notif-kpi-card tt-anim-fadeinup">
                            <strong>{{ (int) ($summary['filtered'] ?? 0) }}</strong>
                            <span>Dans le filtre</span>
                        </article>
                    </section>

                    <section class="notif-page-card tt-anim-fadeinup">
                        <div>
                            <span class="notif-page-pill">Sur cette page</span>
                            <strong>{{ $notificationRows->count() }} notification(s) visibles</strong>
                            <p>
                                @if(($notifications ?? null) && $notifications->hasPages())
                                    Page {{ $notifications->currentPage() }} sur {{ $notifications->lastPage() }}. La liste est maintenant separee par priorite et par jour pour faciliter la lecture.
                                @else
                                    La liste est separee par priorite et par jour pour faciliter la lecture quand plusieurs notifications s accumulent.
                                @endif
                            </p>
                        </div>
                        <div class="notif-page-meta">
                            <span class="notif-page-pill">{{ $pageUnreadNotifications->count() }} a traiter</span>
                            <span class="notif-page-pill">{{ $pageReadNotifications->count() }} deja lues</span>
                        </div>
                    </section>

                    @if(($notifications ?? null) && $notifications->count())
                        @if($hasSplitStreams)
                            @include('pages.notifications.partials.stream', [
                                'items' => $pageUnreadNotifications,
                                'title' => 'A traiter en priorite',
                                'subtitle' => 'Les notifications non lues restent en tete pour eviter qu elles se perdent dans le flux.',
                            ])

                            @include('pages.notifications.partials.stream', [
                                'items' => $pageReadNotifications,
                                'title' => 'Historique recent',
                                'subtitle' => 'Les notifications deja consultees sont rangees separement pour garder une timeline plus claire.',
                            ])
                        @else
                            @include('pages.notifications.partials.stream', [
                                'items' => $notificationRows,
                                'title' => $currentState === 'unread' ? 'Notifications a traiter' : 'Timeline des notifications',
                                'subtitle' => 'Affichage groupe par date pour mieux parcourir les notifications quand le volume augmente.',
                            ])
                        @endif

                        @if($notifications->hasPages())
                        @php
                            $windowStart = max(1, $notifications->currentPage() - 1);
                            $windowEnd = min($notifications->lastPage(), $notifications->currentPage() + 1);
                        @endphp
                        <div class="tt-pagination tt-pagin-center padding-top-80 padding-top-xlg-100 tt-anim-fadeinup">
                            <div class="tt-pagin-prev">
                                <a href="{{ $notifications->previousPageUrl() ?: '#' }}" class="tt-pagin-item tt-magnetic-item {{ $notifications->onFirstPage() ? 'notif-pagin-item-disabled' : '' }}">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                            <div class="tt-pagin-numbers">
                                @for($page = $windowStart; $page <= $windowEnd; $page++)
                                    <a href="{{ $notifications->url($page) }}" class="tt-pagin-item tt-magnetic-item {{ $notifications->currentPage() === $page ? 'active' : '' }}">{{ $page }}</a>
                                @endfor
                            </div>
                            <div class="tt-pagin-next">
                                <a href="{{ $notifications->nextPageUrl() ?: '#' }}" class="tt-pagin-item tt-pagin-next tt-magnetic-item {{ $notifications->hasMorePages() ? '' : 'notif-pagin-item-disabled' }}">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="notif-empty tt-anim-fadeinup">
                            <p>Aucune notification pour ce filtre.</p>
                            <a href="{{ route($preferencesRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                <span data-hover="Verifier preferences">Verifier preferences</span>
                            </a>
                        </div>
                    @endif
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
