@extends('marketing.layouts.template')

@section('title', 'Creer un duel | ERAH Plateforme')
@section('meta_description', 'Selectionnez un joueur, personnalisez votre message et lancez un duel.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .duel-create-toolbar {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .duel-create-search {
            flex: 1 1 520px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 12px;
            padding: 16px;
        }

        .duel-create-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .duel-create-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 13px;
            letter-spacing: .04em;
        }

        .duel-create-player {
            position: relative;
        }

        .duel-create-player .pcli-item-inner {
            align-items: stretch;
        }

        .duel-create-player .pcli-image {
            height: 100%;
            min-height: 220px;
        }

        .duel-create-player .pcli-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .duel-create-player .pcli-caption {
            width: 100%;
        }

        .duel-latest-row {
            margin: 10px 0 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .duel-latest-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .duel-latest-pill.is-pending {
            border-color: rgba(255, 224, 118, .55);
            color: #fff1c9;
        }

        .duel-latest-pill.is-active {
            border-color: rgba(80, 217, 127, .55);
            color: #dbffe7;
        }

        .duel-latest-pill.is-refused {
            border-color: rgba(255, 124, 124, .5);
            color: #ffd1d1;
        }

        .duel-latest-pill.is-expired {
            border-color: rgba(174, 174, 174, .5);
            color: #dfdfdf;
        }

        .duel-create-form .tt-form-control {
            margin-bottom: 12px;
        }

        .duel-create-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .duel-create-empty {
            border: 1px dashed rgba(255, 255, 255, .16);
            border-radius: 12px;
            padding: 26px;
            text-align: center;
            color: rgba(255, 255, 255, .74);
        }
    </style>
@endsection

@section('content')
    @php
        $latestByUserId = $latestByUserId ?? [];
        $availableUsersCount = (int) ($availableUsersCount ?? 0);
        $currentSearch = trim((string) ($search ?? ''));
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
                    <h2 class="ph-caption-subtitle">ERAH Matchups</h2>
                    <h1 class="ph-caption-title">Creer un duel</h1>
                    <div class="ph-caption-description max-width-800">
                        Recherche rapide, message personnalise et envoi instantane vers le joueur cible.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Matchups</h2>
                        <h1 class="ph-caption-title">Creer un duel</h1>
                        <div class="ph-caption-description max-width-800">
                            Selectionnez un adversaire et envoyez votre invitation.
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
                        <textPath xlink:href="#textcircle">Create Duel - Create Duel -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="duel-create-toolbar">
                    <form method="GET" action="{{ route('duels.create') }}" class="tt-form tt-form-creative duel-create-search">
                        <div class="tt-row tt-lg-row-reverse">
                            <div class="tt-col-lg-8">
                                <label for="q">Recherche joueur</label>
                                <input id="q" class="tt-form-control" name="q" value="{{ $currentSearch }}" placeholder="Nom ou email">
                            </div>
                            <div class="tt-col-lg-4 tt-align-self-end">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Rechercher">Rechercher</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="duel-create-summary">
                        <span class="duel-create-chip">Disponibles: <strong>{{ $availableUsersCount }}</strong></span>
                        <span class="duel-create-chip">Resultats: <strong>{{ ($users ?? collect())->count() }}</strong></span>
                        @if($currentSearch !== '')
                            <span class="duel-create-chip">Filtre: <strong>{{ $currentSearch }}</strong></span>
                        @endif
                    </div>
                </div>

                @if(($users ?? null) && $users->count())
                    <div class="tt-portfolio-compact-list pcl-image-hover">
                        <div class="pcli-inner">
                            @foreach($users as $target)
                                @php
                                    $avatar = $target->display_avatar_url;
                                    $latest = $latestByUserId[$target->id] ?? null;
                                    $latestStatusClass = match((string) ($latest['status'] ?? '')) {
                                        \App\Models\Duel::STATUS_ACCEPTED => 'is-active',
                                        \App\Models\Duel::STATUS_REFUSED => 'is-refused',
                                        \App\Models\Duel::STATUS_EXPIRED => 'is-expired',
                                        \App\Models\Duel::STATUS_PENDING => 'is-pending',
                                        default => '',
                                    };
                                @endphp
                                <article class="pcli-item duel-create-player tt-anim-fadeinup">
                                    <div class="pcli-item-inner">
                                        <div class="pcli-col pcli-col-image">
                                            <div class="pcli-image">
                                                <img src="{{ $avatar }}" loading="lazy" alt="{{ $target->name }}">
                                            </div>
                                        </div>

                                        <div class="pcli-col pcli-col-count">
                                            <div class="pcli-count"></div>
                                        </div>

                                        <div class="pcli-col pcli-col-caption">
                                            <div class="pcli-caption">
                                                <h2 class="pcli-title">{{ $target->name }}</h2>
                                                <div class="pcli-categories">
                                                    <div class="pcli-category">{{ $target->email }}</div>
                                                </div>

                                                @if($latest)
                                                    <div class="duel-latest-row">
                                                        <span class="duel-latest-pill {{ $latestStatusClass }}">Dernier: {{ $latest['status_label'] }}</span>
                                                        <span class="duel-latest-pill">{{ $latest['role_label'] }}</span>
                                                        <span class="duel-latest-pill">{{ optional($latest['created_at'])->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                @endif

                                                <form method="POST" action="{{ route('duels.store') }}" class="tt-form duel-create-form" data-duel-create-form data-target-id="{{ $target->id }}" data-auth-user-id="{{ (int) auth()->id() }}">
                                                    @csrf
                                                    <input type="hidden" name="challenged_user_id" value="{{ $target->id }}">
                                                    <input type="hidden" name="idempotency_key" data-duel-idempotency>

                                                    <label for="message-{{ $target->id }}">Message (optionnel)</label>
                                                    <input id="message-{{ $target->id }}" class="tt-form-control" name="message" placeholder="Message rapide">

                                                    <label for="expires-{{ $target->id }}">Duree expiration (minutes)</label>
                                                    <input
                                                        id="expires-{{ $target->id }}"
                                                        class="tt-form-control"
                                                        type="number"
                                                        name="expires_in_minutes"
                                                        min="1"
                                                        max="10080"
                                                        value="60"
                                                    >

                                                    <div class="duel-create-actions">
                                                        <button type="submit" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                                            <span data-hover="Envoyer duel">Envoyer duel</span>
                                                        </button>
                                                        <a href="{{ route('duels.index') }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                                            <span data-hover="Voir mes duels">Voir mes duels</span>
                                                        </a>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="duel-create-empty">
                        Aucun utilisateur trouve pour cette recherche.
                    </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var forms = Array.from(document.querySelectorAll('[data-duel-create-form]'));
            if (!forms.length) {
                return;
            }

            forms.forEach(function (form) {
                form.addEventListener('submit', function () {
                    var keyInput = form.querySelector('[data-duel-idempotency]');
                    if (!keyInput) {
                        return;
                    }

                    var targetId = form.getAttribute('data-target-id') || '0';
                    var authUserId = form.getAttribute('data-auth-user-id') || '0';
                    keyInput.value = 'duel-' + authUserId + '-' + targetId + '-' + Date.now() + '-' + Math.floor(Math.random() * 100000);
                });
            });
        });
    </script>
@endsection
