@extends('marketing.layouts.template')

@section('title', 'Historique points | ERAH Plateforme')
@section('meta_description', 'Historique complet des transactions de points avec filtres par categories et types.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .tx-filters-card,
        .tx-history-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 12px;
            padding: 24px;
        }

        .tx-filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tx-inline-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tx-toolbar-top {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .tx-quick-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1 1 380px;
            margin: 0;
        }

        .tx-quick-form .tt-form-control {
            margin: 0;
        }

        .tx-toggle-advanced {
            white-space: nowrap;
        }

        .tx-active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .tx-active-filter-tag {
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 13px;
            color: rgba(255, 255, 255, .92);
        }

        .tx-category-row {
            margin-top: 16px;
        }

        .tx-advanced-panel {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px dashed rgba(255, 255, 255, .16);
            display: none;
        }

        .tx-advanced-panel.is-open {
            display: block;
        }

        .tx-filter-group .tt-btn {
            padding: 8px 14px;
            border-radius: 999px;
        }

        .tx-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .tx-list-item {
            border-bottom: 1px solid rgba(255, 255, 255, .09);
            padding: 16px 0;
        }

        .tx-list-item:last-child {
            border-bottom: 0;
        }

        .tx-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .tx-kind {
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .tx-kind.is-rank {
            border-color: rgba(77, 173, 255, .55);
            color: #c2e6ff;
        }

        .tx-kind.is-xp {
            border-color: rgba(91, 214, 143, .5);
            color: #d6ffe6;
        }

        .tx-story {
            margin: 0;
            line-height: 1.45;
        }

        .tx-meta {
            margin-top: 8px;
            font-size: 13px;
            color: rgba(255, 255, 255, .68);
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
        }

        @media (max-width: 991.98px) {
            .tx-filters-card,
            .tx-history-card {
                padding: 18px;
            }

            .tx-quick-form {
                flex-basis: 100%;
            }

            .tx-toolbar-top .tt-btn {
                width: 100%;
                text-align: center;
            }

            .tx-filter-group {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 2px;
            }

            .tx-filter-group .tt-btn {
                flex: 0 0 auto;
                white-space: nowrap;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $profileRouteName = $isPublicApp ? 'app.profile' : 'profile.show';
        $historyRouteName = $isPublicApp ? 'app.profile.transactions' : 'profile.transactions';

        $categoryLabels = [
            'all' => 'Toutes',
            'missions' => 'Missions',
            'duels' => 'Duels',
            'paris' => 'Paris',
            'clips' => 'Clips',
            'admin' => 'Admin',
            'autres' => 'Autres',
        ];

        $kindLabels = [
            'all' => 'Tous',
            'xp' => 'XP',
            'rank' => 'Classement',
        ];
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                    <h1 class="ph-caption-title">Historique points</h1>
                    <div class="ph-caption-description max-width-700">
                        Filtrez et parcourez vos transactions de points par categories.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                        <h1 class="ph-caption-title">Historique points</h1>
                        <div class="ph-caption-description max-width-700">
                            Filtrez et parcourez vos transactions de points par categories.
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
                        <textPath xlink:href="#textcircle">Points History - Points History -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-40">
            <div class="tt-section-inner tt-wrap max-width-1000">
                <div class="tx-inline-actions">
                    <a href="{{ route($profileRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                        <span data-hover="Retour profil">Retour profil</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="tt-section padding-bottom-40">
            <div class="tt-section-inner tt-wrap max-width-1000">
                <div class="tx-filters-card">
                    @php
                        $advancedOpen = $kind !== 'all';
                        $baseParams = [];
                        if ($category !== 'all') {
                            $baseParams['category'] = $category;
                        }
                        if ($search !== '') {
                            $baseParams['q'] = $search;
                        }
                        if ($kind !== 'all') {
                            $baseParams['kind'] = $kind;
                        }
                    @endphp

                    <div class="tx-toolbar-top">
                        <form method="GET" action="{{ route($historyRouteName) }}" class="tx-quick-form">
                            <input class="tt-form-control" id="q" name="q" type="text" value="{{ $search }}" placeholder="Recherche rapide (mission, duel, bet...)">
                            <input type="hidden" name="category" value="{{ $category }}">
                            <input type="hidden" name="kind" value="{{ $kind }}">
                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Rechercher">Rechercher</span>
                            </button>
                        </form>

                        <button type="button" class="tt-btn tt-btn-outline tt-magnetic-item tx-toggle-advanced" data-tx-toggle>
                            <span data-hover="Filtres avances">{{ $advancedOpen ? 'Masquer filtres' : 'Filtres avances' }}</span>
                        </button>

                        <a href="{{ route($historyRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                            <span data-hover="Reset">Reset</span>
                        </a>
                    </div>

                    @if($category !== 'all' || $kind !== 'all' || $search !== '')
                        <div class="tx-active-filters">
                            @if($category !== 'all')
                                <span class="tx-active-filter-tag">Categorie: {{ $categoryLabels[$category] ?? $category }}</span>
                            @endif
                            @if($kind !== 'all')
                                <span class="tx-active-filter-tag">Type: {{ $kindLabels[$kind] ?? $kind }}</span>
                            @endif
                            @if($search !== '')
                                <span class="tx-active-filter-tag">Recherche: "{{ \Illuminate\Support\Str::limit($search, 32) }}"</span>
                            @endif
                        </div>
                    @endif

                    <div class="tx-category-row">
                        <small class="tt-form-text">Categories</small>
                        <div class="tx-filter-group margin-top-10">
                            @foreach($categoryLabels as $categoryKey => $categoryLabel)
                                <a href="{{ route($historyRouteName, array_merge($baseParams, ['category' => $categoryKey])) }}"
                                    class="tt-btn {{ $category === $categoryKey ? 'tt-btn-primary' : 'tt-btn-outline' }} tt-magnetic-item">
                                    <span data-hover="{{ $categoryLabel }}">{{ $categoryLabel }} ({{ (int) ($categoryCounts[$categoryKey] ?? 0) }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="tx-advanced-panel {{ $advancedOpen ? 'is-open' : '' }}" data-tx-advanced>
                        <form method="GET" action="{{ route($historyRouteName) }}" class="tt-form tt-form-minimal">
                            <div class="tt-row">
                                <div class="tt-col-lg-6">
                                    <div class="tt-form-group">
                                        <label for="kind">Type de points</label>
                                        <select class="tt-form-control" id="kind" name="kind">
                                            @foreach($kindLabels as $kindKey => $kindLabel)
                                                <option value="{{ $kindKey }}" @selected($kind === $kindKey)>
                                                    {{ $kindLabel }} ({{ (int) ($kindCounts[$kindKey] ?? 0) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="tt-col-lg-6 tt-align-self-end">
                                    <input type="hidden" name="category" value="{{ $category }}">
                                    <input type="hidden" name="q" value="{{ $search }}">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Appliquer">Appliquer</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap max-width-1000">
                <div class="tx-history-card">
                    <p class="tt-form-text margin-bottom-20">
                        {{ (int) $transactions->total() }} resultat(s) pour vos filtres actuels.
                    </p>
                    @if(($transactions ?? null) && $transactions->count())
                        <ul class="tx-list">
                            @foreach($transactions as $tx)
                                @php
                                    $sourceMap = [
                                        'admin_grant' => 'attribution manuelle admin',
                                        'mission.daily' => 'mission quotidienne',
                                        'mission.weekly' => 'mission hebdomadaire',
                                        'duel.win' => 'duel remporte',
                                        'duel.loss' => 'duel termine',
                                        'bet.win' => 'pari gagne',
                                        'bet.refund' => 'remboursement de pari',
                                        'clip.like' => 'interaction sur clip',
                                        'clip.comment' => 'commentaire clip',
                                    ];

                                    $isRank = $tx->kind === \App\Models\PointsTransaction::KIND_RANK;
                                    $kindLabel = $isRank ? 'Classement' : 'XP';
                                    $kindClass = $isRank ? 'is-rank' : 'is-xp';
                                    $pointsLabel = (int) $tx->points.' '.($isRank ? 'points de classement' : 'XP');
                                    $sourceType = (string) $tx->source_type;
                                    $sourceLabel = $sourceMap[$sourceType]
                                        ?? trim(ucwords(str_replace(['.', '_', ':'], ' ', $sourceType)));
                                    $beforeValue = $isRank ? (int) $tx->before_rank_points : (int) $tx->before_xp;
                                    $afterValue = $isRank ? (int) $tx->after_rank_points : (int) $tx->after_xp;
                                    $story = 'Vous avez gagne '.$pointsLabel.' via '.$sourceLabel.'.';
                                    $meta = is_array($tx->meta) ? $tx->meta : [];
                                    $metaParts = [];

                                    foreach ($meta as $metaKey => $metaValue) {
                                        if (is_scalar($metaValue)) {
                                            $metaParts[] = str_replace('_', ' ', (string) $metaKey).': '.$metaValue;
                                        }
                                    }

                                    $metaPreview = implode(' - ', array_slice($metaParts, 0, 2));
                                @endphp
                                <li class="tx-list-item">
                                    <div class="tx-head">
                                        <span class="tx-kind {{ $kindClass }}">{{ $kindLabel }}</span>
                                        <span class="tt-form-text">{{ optional($tx->created_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="tx-story">{{ $story }}</p>
                                    <div class="tx-meta">
                                        <span>Avant: <strong>{{ $beforeValue }}</strong></span>
                                        <span>Apres: <strong>{{ $afterValue }}</strong></span>
                                        @if($metaPreview !== '')
                                            <span>{{ $metaPreview }}</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="margin-top-30">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <p class="tt-form-text">Aucune transaction pour ces filtres.</p>
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
    <script src="/template/assets/js/cookies.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.querySelector('[data-tx-toggle]');
            var panel = document.querySelector('[data-tx-advanced]');

            if (!toggleBtn || !panel) {
                return;
            }

            toggleBtn.addEventListener('click', function () {
                var isOpen = panel.classList.toggle('is-open');
                var span = toggleBtn.querySelector('span');
                if (span) {
                    span.setAttribute('data-hover', isOpen ? 'Masquer filtres' : 'Filtres avances');
                    span.textContent = isOpen ? 'Masquer filtres' : 'Filtres avances';
                }
            });
        });
    </script>
@endsection
