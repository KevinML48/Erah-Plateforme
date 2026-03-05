@extends('marketing.layouts.template')

@section('title', 'Admin Dashboard | ERAH Plateforme')
@section('meta_description', 'Console admin centrale pour piloter la plateforme ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .admin-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }

        .admin-kpi-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 14px 16px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .admin-kpi-card strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .admin-kpi-card span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .72);
        }

        .admin-links-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .admin-link-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 16px;
            padding: 16px;
            background:
                radial-gradient(900px 220px at -20% -100%, rgba(255, 255, 255, .08), transparent 55%),
                rgba(255, 255, 255, .02);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .admin-link-card h3 {
            margin: 0;
            font-size: 30px;
            line-height: .96;
        }

        .admin-link-card p {
            margin: 0;
            color: rgba(255, 255, 255, .72);
            line-height: 1.45;
        }

        .admin-link-meta {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .admin-link-count {
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .8);
        }

        @media (max-width: 1199.98px) {
            .admin-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .admin-links-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .admin-kpi-grid,
            .admin-links-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $stats = $stats ?? [];
        $managementLinks = $managementLinks ?? [];
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
                    <h2 class="ph-caption-subtitle">ERAH Control Center</h2>
                    <h1 class="ph-caption-title">Admin Dashboard</h1>
                    <div class="ph-caption-description max-width-900">
                        Vue globale d administration pour piloter users, contenu, matchs, wallets, cadeaux et missions.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Control Center</h2>
                        <h1 class="ph-caption-title">Admin Dashboard</h1>
                        <div class="ph-caption-description max-width-900">
                            Actions de moderation et gestion centrale.
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
                        <textPath xlink:href="#textcircle">Admin Control - Admin Control -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <section class="admin-kpi-grid">
                    <article class="admin-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($stats['users_total'] ?? 0) }}</strong>
                        <span>Users total</span>
                    </article>
                    <article class="admin-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($stats['clips_published'] ?? 0) }}</strong>
                        <span>Clips publies</span>
                    </article>
                    <article class="admin-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($stats['matches_open'] ?? 0) }}</strong>
                        <span>Matchs ouverts</span>
                    </article>
                    <article class="admin-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($stats['bets_pending'] ?? 0) }}</strong>
                        <span>Paris en attente</span>
                    </article>
                    <article class="admin-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($stats['missions_active'] ?? 0) }}</strong>
                        <span>Missions actives</span>
                    </article>
                    <article class="admin-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($stats['redemptions_pending'] ?? 0) }}</strong>
                        <span>Redemptions pending</span>
                    </article>
                    <article class="admin-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($stats['notifications_unread'] ?? 0) }}</strong>
                        <span>Notifications non lues</span>
                    </article>
                    <article class="admin-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($stats['wallet_volume_today'] ?? 0) }}</strong>
                        <span>Volume wallet aujourd hui</span>
                    </article>
                </section>

                <section class="admin-links-grid">
                    @foreach($managementLinks as $item)
                        <article class="admin-link-card tt-anim-fadeinup">
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['description'] }}</p>
                            <div class="admin-link-meta">
                                <span class="admin-link-count">{{ (int) ($item['count'] ?? 0) }}</span>
                                <a href="{{ $item['route'] }}" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                    <span data-hover="{{ $item['action'] }}">{{ $item['action'] }}</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </section>
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
