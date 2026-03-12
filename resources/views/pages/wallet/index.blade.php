@extends('marketing.layouts.template')

@section('title', 'Portefeuille points | ERAH Plateforme')
@section('meta_description', 'Portefeuille points ERAH: solde unifie, flux et historique dynamique.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .wallet-toolbar {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .wallet-toolbar-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .wallet-direction-tabs,
        .wallet-type-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .wallet-tab {
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

        .wallet-tab.active {
            border-color: rgba(90, 206, 255, .62);
            color: #d8f4ff;
            background: rgba(90, 206, 255, .1);
        }

        .wallet-tab.tone-in.active {
            border-color: rgba(104, 220, 150, .62);
            color: #d8ffe8;
            background: rgba(104, 220, 150, .11);
        }

        .wallet-tab.tone-out.active {
            border-color: rgba(255, 132, 132, .62);
            color: #ffe0e0;
            background: rgba(255, 132, 132, .1);
        }

        .wallet-tab-count {
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 999px;
            padding: 1px 7px;
            font-size: 11px;
            line-height: 1.3;
        }

        .wallet-search-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin: 0;
        }

        .wallet-search-form .tt-form-control {
            margin: 0;
            min-width: 270px;
        }

        .wallet-toolbar-meta {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed rgba(255, 255, 255, .15);
            font-size: 13px;
            color: rgba(255, 255, 255, .68);
        }

        .wallet-type-tabs {
            margin-top: 12px;
        }

        .wallet-kpi-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .wallet-kpi-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 14px 16px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .wallet-kpi-card strong {
            display: block;
            font-size: 33px;
            line-height: 1;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .wallet-kpi-card span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .72);
        }

        .wallet-kpi-card.kpi-balance strong {
            color: #d8f4ff;
        }

        .wallet-kpi-card.kpi-in strong {
            color: #d8ffe8;
        }

        .wallet-kpi-card.kpi-out strong {
            color: #ffe0e0;
        }

        .wallet-list {
            display: grid;
            gap: 12px;
        }

        .wallet-item {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 16px;
            background: rgba(255, 255, 255, .02);
        }

        .wallet-item.is-in {
            border-color: rgba(104, 220, 150, .45);
            background: linear-gradient(160deg, rgba(104, 220, 150, .08), rgba(255, 255, 255, .02));
        }

        .wallet-item.is-out {
            border-color: rgba(255, 132, 132, .45);
            background: linear-gradient(160deg, rgba(255, 132, 132, .09), rgba(255, 255, 255, .02));
        }

        .wallet-item-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .wallet-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .wallet-type-badge.is-in {
            border-color: rgba(104, 220, 150, .52);
            color: #deffec;
        }

        .wallet-type-badge.is-out {
            border-color: rgba(255, 132, 132, .52);
            color: #ffe3e3;
        }

        .wallet-time {
            color: rgba(255, 255, 255, .62);
            font-size: 13px;
            white-space: nowrap;
        }

        .wallet-amount {
            margin: 0;
            font-size: 30px;
            line-height: 1;
            font-weight: 700;
        }

        .wallet-amount.is-in {
            color: #d8ffe8;
        }

        .wallet-amount.is-out {
            color: #ffe0e0;
        }

        .wallet-message {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, .79);
            line-height: 1.45;
        }

        .wallet-meta {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px 14px;
            color: rgba(255, 255, 255, .68);
            font-size: 13px;
        }

        .wallet-meta-pill {
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            padding: 2px 9px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .wallet-empty {
            border: 1px dashed rgba(255, 255, 255, .2);
            border-radius: 14px;
            padding: 28px;
            text-align: center;
            color: rgba(255, 255, 255, .74);
        }

        .wallet-empty p {
            margin: 0 0 14px;
        }

        .wallet-pagin-item-disabled {
            opacity: .35;
            pointer-events: none;
        }

        @media (max-width: 1399.98px) {
            .wallet-kpi-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .wallet-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .wallet-search-form {
                width: 100%;
            }

            .wallet-search-form .tt-form-control {
                width: 100%;
                min-width: 0;
            }
        }

        @media (max-width: 767.98px) {
            .wallet-kpi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $indexRouteName = 'wallet.index';
        $betsRouteName = request()->routeIs('app.*') ? 'app.bets.index' : 'bets.index';
        $matchesRouteName = request()->routeIs('app.*') ? 'app.matches.index' : 'matches.index';

        $filters = array_merge([
            'type' => 'all',
            'direction' => 'all',
            'q' => '',
        ], $filters ?? []);
        $summary = array_merge([
            'total' => 0,
            'in_count' => 0,
            'out_count' => 0,
            'in_total' => 0,
            'out_total' => 0,
            'filtered' => 0,
            'month_count' => 0,
        ], $summary ?? []);
        $typeCounts = collect($typeCounts ?? []);

        $currentType = (string) ($filters['type'] ?? 'all');
        $currentDirection = (string) ($filters['direction'] ?? 'all');
        $search = (string) ($filters['q'] ?? '');

        $directionLabels = [
            'all' => 'Tous flux',
            'in' => 'Entrees',
            'out' => 'Sorties',
        ];

        if (! array_key_exists($currentDirection, $directionLabels)) {
            $currentDirection = 'all';
        }

        $typeLabels = [
            'all' => 'Tous types',
            \App\Models\RewardWalletTransaction::TYPE_GRANT => 'Credit',
            \App\Models\RewardWalletTransaction::TYPE_MISSION_REWARD => 'Mission',
            \App\Models\RewardWalletTransaction::TYPE_REDEEM_COST => 'Demande cadeau',
            \App\Models\RewardWalletTransaction::TYPE_REDEEM_REFUND => 'Remboursement cadeau',
            \App\Models\RewardWalletTransaction::TYPE_ADJUST => 'Ajustement',
            \App\Models\RewardWalletTransaction::TYPE_GIFT_PURCHASE => 'Achat cadeau',
            \App\Models\RewardWalletTransaction::TYPE_BET_STAKE => 'Mise',
            \App\Models\RewardWalletTransaction::TYPE_BET_PAYOUT => 'Gain de pari',
            \App\Models\RewardWalletTransaction::TYPE_BET_REFUND => 'Remboursement pari',
            \App\Models\RewardWalletTransaction::TYPE_DUEL_STAKE => 'Duel engage',
            \App\Models\RewardWalletTransaction::TYPE_DUEL_WIN => 'Duel gagne',
            \App\Models\RewardWalletTransaction::TYPE_DUEL_REFUND => 'Remboursement duel',
            \App\Models\RewardWalletTransaction::TYPE_ADMIN_ADJUSTMENT => 'Admin',
            \App\Models\RewardWalletTransaction::TYPE_STREAK_REWARD => 'Streak',
            \App\Models\RewardWalletTransaction::TYPE_SHOP_PURCHASE => 'Boutique',
            \App\Models\RewardWalletTransaction::TYPE_ACTIVITY_REWARD => 'Activite',
        ];

        if (! array_key_exists($currentType, $typeLabels)) {
            $currentType = 'all';
        }

        $directionCounts = [
            'all' => (int) ($summary['total'] ?? 0),
            'in' => (int) ($summary['in_count'] ?? 0),
            'out' => (int) ($summary['out_count'] ?? 0),
        ];

        $buildParams = function (array $overrides = []) use ($currentType, $currentDirection, $search): array {
            $params = [
                'type' => $currentType,
                'direction' => $currentDirection,
                'q' => $search,
            ];

            foreach ($overrides as $key => $value) {
                $params[$key] = $value;
            }

            if (($params['type'] ?? 'all') === 'all') {
                unset($params['type']);
            }
            if (($params['direction'] ?? 'all') === 'all') {
                unset($params['direction']);
            }
            if (($params['q'] ?? '') === '') {
                unset($params['q']);
            }

            return $params;
        };
    @endphp

    <div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="ph-video ph-video-cover-6">
            <div class="ph-video-inner">
                <video loop muted autoplay playsinline preload="metadata" poster="/template/assets/vids/1920/video-3-1920.jpg">
                    <source src="/template/assets/vids/placeholder.mp4" data-src="/template/assets/vids/1920/video-3-1920.mp4" type="video/mp4">
                    <source src="/template/assets/vids/placeholder.webm" data-src="/template/assets/vids/1920/video-3-1920.webm" type="video/webm">
                </video>
            </div>
        </div>

        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Points ERAH</h2>
                    <h1 class="ph-caption-title">Portefeuille</h1>
                    <div class="ph-caption-description max-width-900">
                        Solde actuel: {{ (int) ($wallet->balance ?? 0) }} points - {{ (int) ($summary['filtered'] ?? 0) }} transaction(s) dans l affichage.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Points ERAH</h2>
                        <h1 class="ph-caption-title">Portefeuille</h1>
                        <div class="ph-caption-description max-width-900">
                            Historique dynamique des points utilises pour les cadeaux, paris, duels et recompenses.
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
                        <textPath xlink:href="#textcircle">Portefeuille ERAH - Portefeuille ERAH -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <section class="wallet-toolbar tt-anim-fadeinup">
                    <div class="wallet-toolbar-row">
                        <div class="wallet-direction-tabs">
                            @foreach($directionLabels as $directionKey => $directionLabel)
                                @php
                                    $directionTone = $directionKey === 'in' ? 'tone-in' : ($directionKey === 'out' ? 'tone-out' : '');
                                @endphp
                                <a href="{{ route($indexRouteName, $buildParams(['direction' => $directionKey])) }}"
                                   class="wallet-tab {{ $directionTone }} {{ $currentDirection === $directionKey ? 'active' : '' }}">
                                    {{ $directionLabel }}
                                    <span class="wallet-tab-count">{{ (int) ($directionCounts[$directionKey] ?? 0) }}</span>
                                </a>
                            @endforeach
                        </div>

                        <form method="GET" action="{{ route($indexRouteName) }}" class="wallet-search-form">
                            <input type="hidden" name="type" value="{{ $currentType }}">
                            <input type="hidden" name="direction" value="{{ $currentDirection }}">
                            <input class="tt-form-control" type="text" name="q" value="{{ $search }}" placeholder="Rechercher (type, source, reference interne)">
                            <button type="submit" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                <span data-hover="Filtrer">Filtrer</span>
                            </button>
                            <a href="{{ route($indexRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                <span data-hover="Tout voir">Tout voir</span>
                            </a>
                        </form>
                    </div>

                    <div class="wallet-type-tabs">
                        @foreach($typeLabels as $typeKey => $typeLabel)
                            @php
                                $countValue = $typeKey === 'all' ? (int) ($summary['total'] ?? 0) : (int) ($typeCounts->get($typeKey, 0));
                            @endphp
                            <a href="{{ route($indexRouteName, $buildParams(['type' => $typeKey])) }}"
                               class="wallet-tab {{ $currentType === $typeKey ? 'active' : '' }}">
                                {{ $typeLabel }}
                                <span class="wallet-tab-count">{{ $countValue }}</span>
                            </a>
                        @endforeach
                    </div>

                    <div class="wallet-toolbar-meta">
                        Filtre actif: {{ (int) ($summary['filtered'] ?? 0) }} transaction(s) - ce mois: {{ (int) ($summary['month_count'] ?? 0) }} transaction(s).
                    </div>
                </section>

                <section class="wallet-kpi-grid">
                    <article class="wallet-kpi-card kpi-balance tt-anim-fadeinup">
                        <strong>{{ (int) ($wallet->balance ?? 0) }}</strong>
                        <span>Solde points</span>
                    </article>
                    <article class="wallet-kpi-card kpi-in tt-anim-fadeinup">
                        <strong>+{{ (int) ($summary['in_total'] ?? 0) }}</strong>
                        <span>Total entrees</span>
                    </article>
                    <article class="wallet-kpi-card kpi-out tt-anim-fadeinup">
                        <strong>-{{ (int) ($summary['out_total'] ?? 0) }}</strong>
                        <span>Total sorties</span>
                    </article>
                    <article class="wallet-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($summary['total'] ?? 0) }}</strong>
                        <span>Transactions total</span>
                    </article>
                    <article class="wallet-kpi-card tt-anim-fadeinup">
                        <strong>{{ (int) ($summary['month_count'] ?? 0) }}</strong>
                        <span>Transactions du mois</span>
                    </article>
                </section>

                @if(($transactions ?? null) && $transactions->count())
                    <section class="wallet-list">
                        @foreach($transactions as $tx)
                            @php
                                $isIn = (int) $tx->amount >= 0;
                                $impactClass = $isIn ? 'is-in' : 'is-out';
                                $amountValue = abs((int) $tx->amount);
                                $amountLabel = ($isIn ? '+' : '-').$amountValue.' points';

                                $typeKey = (string) $tx->type;
                                $typeLabel = $typeLabels[$typeKey] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $typeKey));

                                $sourceLabelMap = [
                                    'mission' => 'Mission',
                                    'gift' => 'Cadeau',
                                    'bet' => 'Pari',
                                    'duel' => 'Duel',
                                    'shop' => 'Boutique',
                                    'admin' => 'Admin',
                                    'system' => 'Systeme',
                                ];
                                $sourceType = (string) ($tx->ref_type ?? 'system');
                                $sourceLabel = $sourceLabelMap[$sourceType] ?? \Illuminate\Support\Str::headline($sourceType);

                                $typeMessageMap = [
                                    \App\Models\RewardWalletTransaction::TYPE_MISSION_REWARD => 'Recompense de mission creditee.',
                                    \App\Models\RewardWalletTransaction::TYPE_GIFT_PURCHASE => 'Points utilises pour demander un cadeau.',
                                    \App\Models\RewardWalletTransaction::TYPE_REDEEM_COST => 'Debit lors d une redemption cadeau.',
                                    \App\Models\RewardWalletTransaction::TYPE_REDEEM_REFUND => 'Remboursement apres annulation ou rejet cadeau.',
                                    \App\Models\RewardWalletTransaction::TYPE_BET_STAKE => 'Mise placee sur un match.',
                                    \App\Models\RewardWalletTransaction::TYPE_BET_PAYOUT => 'Gain de pari verse.',
                                    \App\Models\RewardWalletTransaction::TYPE_BET_REFUND => 'Pari rembourse.',
                                    \App\Models\RewardWalletTransaction::TYPE_DUEL_STAKE => 'Points engages sur un duel.',
                                    \App\Models\RewardWalletTransaction::TYPE_DUEL_WIN => 'Gain ou recompense duel creditee.',
                                    \App\Models\RewardWalletTransaction::TYPE_DUEL_REFUND => 'Duel rembourse.',
                                    \App\Models\RewardWalletTransaction::TYPE_GRANT => 'Credit manuel de points.',
                                    \App\Models\RewardWalletTransaction::TYPE_ADMIN_ADJUSTMENT => 'Ajustement admin du solde.',
                                    \App\Models\RewardWalletTransaction::TYPE_STREAK_REWARD => 'Bonus de connexion credite.',
                                    \App\Models\RewardWalletTransaction::TYPE_SHOP_PURCHASE => 'Achat boutique en points.',
                                    \App\Models\RewardWalletTransaction::TYPE_ACTIVITY_REWARD => 'Recompense d activite creditee.',
                                    \App\Models\RewardWalletTransaction::TYPE_ADJUST => 'Ajustement manuel du solde.',
                                ];
                                $message = $typeMessageMap[$typeKey] ?? 'Mouvement de points enregistre.';

                                $meta = is_array($tx->metadata) ? $tx->metadata : [];
                                $metaLabelMap = [
                                    'match_id' => 'Match',
                                    'bet_id' => 'Bet',
                                    'prediction' => 'Prediction',
                                    'reason' => 'Motif',
                                ];
                                $metaParts = [];
                                foreach ($metaLabelMap as $metaKey => $metaLabel) {
                                    if (! array_key_exists($metaKey, $meta)) {
                                        continue;
                                    }

                                    $metaValue = $meta[$metaKey];
                                    if (! is_scalar($metaValue)) {
                                        continue;
                                    }

                                    $metaParts[] = $metaLabel.': '.\Illuminate\Support\Str::limit((string) $metaValue, 28);
                                    if (count($metaParts) >= 2) {
                                        break;
                                    }
                                }
                            @endphp
                            <article class="wallet-item {{ $impactClass }} tt-anim-fadeinup">
                                <header class="wallet-item-head">
                                    <span class="wallet-type-badge {{ $impactClass }}">
                                        <i class="fa-solid {{ $isIn ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                        {{ $typeLabel }}
                                    </span>
                                    <time class="wallet-time">{{ optional($tx->created_at)->format('d/m/Y H:i') ?? '-' }}</time>
                                </header>

                                <h2 class="wallet-amount {{ $impactClass }}">{{ $amountLabel }}</h2>
                                <p class="wallet-message">{{ $message }}</p>

                                <div class="wallet-meta">
                                    <span>Solde apres: <strong>{{ (int) $tx->balance_after }}</strong> points</span>
                                    <span class="wallet-meta-pill">{{ $sourceLabel }}</span>
                                    @foreach($metaParts as $metaText)
                                        <span>{{ $metaText }}</span>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </section>

                    @if($transactions->hasPages())
                        @php
                            $windowStart = max(1, $transactions->currentPage() - 1);
                            $windowEnd = min($transactions->lastPage(), $transactions->currentPage() + 1);
                        @endphp
                        <div class="tt-pagination tt-pagin-center padding-top-80 padding-top-xlg-100 tt-anim-fadeinup">
                            <div class="tt-pagin-prev">
                                <a href="{{ $transactions->previousPageUrl() ?: '#' }}" class="tt-pagin-item tt-magnetic-item {{ $transactions->onFirstPage() ? 'wallet-pagin-item-disabled' : '' }}">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                            <div class="tt-pagin-numbers">
                                @for($page = $windowStart; $page <= $windowEnd; $page++)
                                    <a href="{{ $transactions->url($page) }}" class="tt-pagin-item tt-magnetic-item {{ $transactions->currentPage() === $page ? 'active' : '' }}">{{ $page }}</a>
                                @endfor
                            </div>
                            <div class="tt-pagin-next">
                                <a href="{{ $transactions->nextPageUrl() ?: '#' }}" class="tt-pagin-item tt-pagin-next tt-magnetic-item {{ $transactions->hasMorePages() ? '' : 'wallet-pagin-item-disabled' }}">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="wallet-empty tt-anim-fadeinup">
                        <p>Aucune transaction pour ce filtre.</p>
                        <a href="{{ route($matchesRouteName) }}" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                            <span data-hover="Voir matchs">Voir matchs</span>
                        </a>
                        <a href="{{ route($betsRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                            <span data-hover="Mes paris">Mes paris</span>
                        </a>
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
@endsection
