@extends('marketing.layouts.template')

@section('title', 'Missions | ERAH Plateforme')
@section('meta_description', 'Suivi dynamique des missions daily, weekly et event avec progression et historique.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .mission-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-top: 26px;
        }

        .mission-kpi-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 12px;
            padding: 18px;
            background: rgba(255, 255, 255, .01);
        }

        .mission-kpi-value {
            display: block;
            font-size: 30px;
            line-height: 1;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .mission-quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
            align-items: center;
        }

        .mission-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .mission-nav a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .mission-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
            margin-top: 12px;
            color: rgba(255, 255, 255, .66);
            font-size: 13px;
        }

        .mission-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            border: 1px solid rgba(255, 255, 255, .3);
        }

        .mission-status.is-completed {
            border-color: rgba(86, 204, 144, .5);
            color: #d3ffe8;
        }

        .mission-status.is-pending {
            border-color: rgba(255, 214, 102, .45);
            color: #ffefc5;
        }

        .mission-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .mission-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .mission-progress {
            margin-top: 18px;
            margin-bottom: 10px;
        }

        .mission-progress-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 13px;
            color: rgba(255, 255, 255, .74);
        }

        .mission-progress-track {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            overflow: hidden;
        }

        .mission-progress-track > span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #42d392 0%, #53b3ff 100%);
        }

        .mission-reward-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .mission-reward-chip {
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 12px;
            letter-spacing: .04em;
        }

        .mission-list-empty {
            border: 1px dashed rgba(255, 255, 255, .16);
            border-radius: 12px;
            padding: 20px;
            color: rgba(255, 255, 255, .7);
            text-align: center;
        }

        .mission-history-wrap {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 12px;
            padding: 26px;
        }

        .mission-history-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .22);
            padding: 2px 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .mission-page-note {
            color: rgba(255, 255, 255, .72);
            margin-top: 8px;
        }

        .mission-pagin-item-disabled {
            opacity: .35;
            pointer-events: none;
        }

        @media (max-width: 1199.98px) {
            .mission-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .mission-quick-actions {
                justify-content: flex-start;
                margin-top: 20px;
            }
        }

        @media (max-width: 767.98px) {
            .mission-kpi-grid {
                grid-template-columns: 1fr;
            }

            .mission-history-wrap {
                padding: 18px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $dashboardRouteName = $isPublicApp ? 'app.leaderboards.me' : 'dashboard';
        $dailyCards = $dailyCards ?? collect();
        $weeklyCards = $weeklyCards ?? collect();
        $specialCards = $specialCards ?? collect();
        $missionStats = $missionStats ?? [
            'total' => 0,
            'completed' => 0,
            'pending' => 0,
            'completion_rate' => 0,
            'xp_potential' => 0,
            'rank_potential' => 0,
            'reward_potential' => 0,
            'bet_potential' => 0,
        ];
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
                    <h2 class="ph-caption-subtitle">ERAH Rewards Board</h2>
                    <h1 class="ph-caption-title">Missions</h1>
                    <div class="ph-caption-description max-width-900">
                        {{ (int) ($missionStats['completed'] ?? 0) }} / {{ (int) ($missionStats['total'] ?? 0) }} completees
                        - {{ (int) ($missionStats['completion_rate'] ?? 0) }}% de progression globale.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Rewards Board</h2>
                        <h1 class="ph-caption-title">Missions</h1>
                        <div class="ph-caption-description max-width-900">
                            Daily, Weekly et Event en suivi dynamique.
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
                        <textPath xlink:href="#textcircle">Mission Board - Mission Board -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row">
                    <div class="tt-col-xl-8">
                        <div class="tt-heading tt-heading-lg no-margin">
                            <h3 class="tt-heading-subtitle">Progression</h3>
                            <h2 class="tt-heading-title">Vue globale missions</h2>
                        </div>
                        <p class="mission-page-note">
                            Les blocs sont separes pour simplifier la lecture: statut global, daily, weekly/event puis historique.
                        </p>
                        <div class="mission-nav">
                            <a href="#mission-daily">Daily</a>
                            <a href="#mission-weekly">Weekly</a>
                            <a href="#mission-special">Event</a>
                            <a href="#mission-history">Historique</a>
                        </div>
                    </div>

                    <div class="tt-col-xl-4 tt-align-self-center">
                        <div class="mission-quick-actions">
                            <a href="{{ route('gifts.index') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Voir cadeaux">Voir cadeaux</span>
                            </a>
                            <a href="{{ route($dashboardRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour dashboard">Retour dashboard</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mission-kpi-grid">
                    <article class="mission-kpi-card">
                        <span class="mission-kpi-value">{{ (int) ($missionStats['total'] ?? 0) }}</span>
                        <span class="text-muted">Missions actives</span>
                    </article>
                    <article class="mission-kpi-card">
                        <span class="mission-kpi-value">{{ (int) ($missionStats['completed'] ?? 0) }}</span>
                        <span class="text-muted">Completees</span>
                    </article>
                    <article class="mission-kpi-card">
                        <span class="mission-kpi-value">{{ (int) ($missionStats['completion_rate'] ?? 0) }}%</span>
                        <span class="text-muted">Taux de completion</span>
                    </article>
                    <article class="mission-kpi-card">
                        <span class="mission-kpi-value">{{ (int) ($missionStats['reward_potential'] ?? 0) }}</span>
                        <span class="text-muted">Reward points potentiels</span>
                    </article>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-xlg-120 border-top" id="mission-daily">
            <div class="tt-section-inner">
                <div class="tt-heading tt-heading-lg tt-heading-center margin-bottom-120">
                    <h2 class="tt-heading-title tt-text-reveal">Missions quotidiennes</h2>
                    <p class="max-width-900 tt-anim-fadeinup text-muted">
                        Objectifs rapides a boucler sur la journee.
                    </p>
                </div>

                @if($dailyCards->count())
                    <div class="tt-accordion tt-ac-xxlg tt-ac-hover tt-ac-counter tt-ac-borders">
                        @foreach($dailyCards as $mission)
                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">{{ $mission['title'] }}</h4>
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

                                <div class="tt-accordion-content max-width-1400 {{ $loop->first ? 'is-open' : '' }}">
                                    <div class="mission-pill-row">
                                        <span class="mission-status {{ $mission['status_class'] }}">{{ $mission['status_label'] }}</span>
                                        <span class="mission-pill">{{ $mission['scope_label'] }}</span>
                                        <span class="mission-pill">{{ $mission['event_label'] }}</span>
                                    </div>

                                    <p>{{ $mission['description'] }}</p>

                                    <div class="mission-progress">
                                        <div class="mission-progress-head">
                                            <span>Progression</span>
                                            <strong>{{ (int) $mission['progress_count'] }} / {{ (int) $mission['target_count'] }}</strong>
                                        </div>
                                        <div class="mission-progress-track">
                                            <span style="width: {{ (int) $mission['progress_percent'] }}%"></span>
                                        </div>
                                    </div>

                                    <div class="mission-reward-row">
                                        @if((int) ($mission['rewards']['xp'] ?? 0) > 0)
                                            <span class="mission-reward-chip">+{{ (int) $mission['rewards']['xp'] }} XP</span>
                                        @endif
                                        @if((int) ($mission['rewards']['rank_points'] ?? 0) > 0)
                                            <span class="mission-reward-chip">+{{ (int) $mission['rewards']['rank_points'] }} Rank</span>
                                        @endif
                                        @if((int) ($mission['rewards']['reward_points'] ?? 0) > 0)
                                            <span class="mission-reward-chip">+{{ (int) $mission['rewards']['reward_points'] }} Reward</span>
                                        @endif
                                        @if((int) ($mission['rewards']['bet_points'] ?? 0) > 0)
                                            <span class="mission-reward-chip">+{{ (int) $mission['rewards']['bet_points'] }} Bet</span>
                                        @endif
                                    </div>

                                    <div class="mission-meta-row">
                                        <span>Periode: {{ optional($mission['period_start'])->format('d/m/Y H:i') ?? '-' }} -> {{ optional($mission['period_end'])->format('d/m/Y H:i') ?? '-' }}</span>
                                        <span>Maj: {{ optional($mission['updated_at'])->format('d/m/Y H:i') ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="tt-wrap">
                        <div class="mission-list-empty">Aucune mission daily active pour le moment.</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="tt-section padding-top-xlg-120 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row">
                    <div class="tt-col-lg-6 margin-bottom-50" id="mission-weekly">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h3 class="tt-heading-subtitle">Hebdomadaire</h3>
                            <h2 class="tt-heading-title">Missions weekly</h2>
                        </div>

                        @if($weeklyCards->count())
                            <div class="tt-accordion tt-ac-sm tt-ac-borders tt-ac-counter">
                                @foreach($weeklyCards as $mission)
                                    <div class="tt-accordion-item tt-anim-fadeinup">
                                        <div class="tt-accordion-heading">
                                            <div class="tt-ac-head cursor-alter">
                                                <div class="tt-ac-head-inner">
                                                    <h4 class="tt-ac-head-title">{{ $mission['title'] }}</h4>
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

                                        <div class="tt-accordion-content max-width-1000 {{ $loop->first ? 'is-open' : '' }}">
                                            <div class="mission-pill-row">
                                                <span class="mission-status {{ $mission['status_class'] }}">{{ $mission['status_label'] }}</span>
                                                <span class="mission-pill">{{ $mission['event_label'] }}</span>
                                            </div>
                                            <p>{{ $mission['description'] }}</p>
                                            <div class="mission-progress-head">
                                                <span>Progression</span>
                                                <strong>{{ (int) $mission['progress_count'] }} / {{ (int) $mission['target_count'] }}</strong>
                                            </div>
                                            <div class="mission-progress-track">
                                                <span style="width: {{ (int) $mission['progress_percent'] }}%"></span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mission-list-empty">Aucune mission weekly active.</div>
                        @endif
                    </div>

                    <div class="tt-col-lg-6" id="mission-special">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h3 class="tt-heading-subtitle">Speciales</h3>
                            <h2 class="tt-heading-title">Missions event / one-shot</h2>
                        </div>

                        @if($specialCards->count())
                            <div class="tt-accordion tt-ac-sm tt-ac-borders tt-ac-counter">
                                @foreach($specialCards as $mission)
                                    <div class="tt-accordion-item tt-anim-fadeinup">
                                        <div class="tt-accordion-heading">
                                            <div class="tt-ac-head cursor-alter">
                                                <div class="tt-ac-head-inner">
                                                    <h4 class="tt-ac-head-title">{{ $mission['title'] }}</h4>
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

                                        <div class="tt-accordion-content max-width-1000 {{ $loop->first ? 'is-open' : '' }}">
                                            <div class="mission-pill-row">
                                                <span class="mission-status {{ $mission['status_class'] }}">{{ $mission['status_label'] }}</span>
                                                <span class="mission-pill">{{ $mission['scope_label'] }}</span>
                                                <span class="mission-pill">{{ $mission['event_label'] }}</span>
                                            </div>
                                            <p>{{ $mission['description'] }}</p>
                                            <div class="mission-progress-head">
                                                <span>Progression</span>
                                                <strong>{{ (int) $mission['progress_count'] }} / {{ (int) $mission['target_count'] }}</strong>
                                            </div>
                                            <div class="mission-progress-track">
                                                <span style="width: {{ (int) $mission['progress_percent'] }}%"></span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mission-list-empty">Aucune mission speciale active.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top" id="mission-history">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-heading tt-heading-lg margin-bottom-30">
                    <h3 class="tt-heading-subtitle">Historique</h3>
                    <h2 class="tt-heading-title">Dernieres mises a jour missions</h2>
                </div>

                <div class="mission-history-wrap">
                    @if(($history ?? null) && $history->count())
                        <div class="tt-avards-list">
                            @foreach($history as $mission)
                                <div class="tt-avlist-item tt-anim-fadeinup">
                                    <div class="tt-avlist-item-inner">
                                        <div class="tt-avlist-col tt-avlist-col-count">
                                            <div class="tt-avlist-count"></div>
                                        </div>
                                        <div class="tt-avlist-col tt-avlist-col-title">
                                            <h4 class="tt-avlist-title">{{ $mission['title'] ?? 'Mission' }}</h4>
                                        </div>
                                        <div class="tt-avlist-col tt-avlist-col-description">
                                            <div class="tt-avlist-description">
                                                {{ ($mission['scope_label'] ?? 'Mission') }} -
                                                {{ (int) ($mission['progress_count'] ?? 0) }} / {{ (int) ($mission['target_count'] ?? 0) }} -
                                                {{ $mission['event_label'] ?? 'Evenement libre' }}
                                            </div>
                                        </div>
                                        <div class="tt-avlist-col tt-avlist-col-info">
                                            <div class="tt-avlist-info">
                                                <span class="mission-history-status {{ $mission['status_class'] ?? '' }}">{{ $mission['status_label'] ?? 'En cours' }}</span>
                                                <br>
                                                {{ optional($mission['updated_at'] ?? null)->format('d/m/Y H:i') ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($history->hasPages())
                            @php
                                $windowStart = max(1, $history->currentPage() - 1);
                                $windowEnd = min($history->lastPage(), $history->currentPage() + 1);
                            @endphp
                            <div class="tt-pagination tt-pagin-center padding-top-60 tt-anim-fadeinup">
                                <div class="tt-pagin-prev">
                                    <a href="{{ $history->previousPageUrl() ?: '#' }}"
                                       class="tt-pagin-item tt-magnetic-item {{ $history->onFirstPage() ? 'mission-pagin-item-disabled' : '' }}">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                </div>
                                <div class="tt-pagin-numbers">
                                    @for($page = $windowStart; $page <= $windowEnd; $page++)
                                        <a href="{{ $history->url($page) }}"
                                           class="tt-pagin-item tt-magnetic-item {{ $history->currentPage() === $page ? 'active' : '' }}">
                                            {{ $page }}
                                        </a>
                                    @endfor
                                </div>
                                <div class="tt-pagin-next">
                                    <a href="{{ $history->nextPageUrl() ?: '#' }}"
                                       class="tt-pagin-item tt-pagin-next tt-magnetic-item {{ $history->hasMorePages() ? '' : 'mission-pagin-item-disabled' }}">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="mission-list-empty">Historique vide pour le moment.</div>
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
