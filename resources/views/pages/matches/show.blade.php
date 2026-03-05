@extends('marketing.layouts.template')

@section('title', 'Match detail | ERAH Plateforme')
@section('meta_description', 'Detail match esport, options de pari et suivi de votre position.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .match-detail-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .match-detail-status {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .9);
        }

        .match-detail-status.is-live {
            border-color: rgba(255, 115, 115, .65);
            color: #ffd6d6;
        }

        .match-detail-status.is-upcoming {
            border-color: rgba(96, 214, 255, .62);
            color: #d8f5ff;
        }

        .match-detail-status.is-finished {
            border-color: rgba(149, 149, 149, .55);
            color: #e2e2e2;
        }

        .match-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 16px;
            background: rgba(255, 255, 255, .02);
        }

        .match-card h3 {
            margin: 0 0 10px;
            font-size: 24px;
            line-height: 1.05;
            font-weight: 700;
        }

        .match-card-note {
            color: rgba(255, 255, 255, .72);
            font-size: 14px;
            line-height: 1.45;
            margin: 0 0 10px;
        }

        .match-options-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .match-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .match-option-label {
            display: block;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 12px;
            padding: 11px 10px;
            cursor: pointer;
            transition: .2s ease;
            background: rgba(255, 255, 255, .01);
        }

        .match-option-label strong {
            display: block;
            font-size: 14px;
            line-height: 1.2;
        }

        .match-option-label span {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: rgba(255, 255, 255, .68);
        }

        .match-option input:checked + .match-option-label {
            border-color: rgba(86, 196, 255, .7);
            background: rgba(86, 196, 255, .12);
            color: #d6f4ff;
        }

        .match-form-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .match-form-grid label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .7);
        }

        .match-form-grid input[type="number"] {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 10px;
            height: 46px;
            padding: 0 12px;
            color: #fff;
            background: rgba(0, 0, 0, .2);
        }

        .match-mybet-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .match-mybet-item {
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 10px;
            padding: 10px;
            background: rgba(0, 0, 0, .18);
        }

        .match-mybet-item strong {
            display: block;
            font-size: 18px;
            line-height: 1;
            margin-bottom: 4px;
        }

        .match-mybet-item span {
            font-size: 12px;
            color: rgba(255, 255, 255, .68);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .match-actions-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .match-related-title {
            margin: 0 0 16px;
            font-size: 38px;
            line-height: .95;
        }

        .match-related-item .pcli-item-inner {
            align-items: stretch;
        }

        .match-related-item .pcli-image {
            height: 100%;
        }

        .match-related-item .pcli-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 1199.98px) {
            .match-mybet-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .match-options-grid {
                grid-template-columns: 1fr;
            }

            .match-form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .match-mybet-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.matches.index' : 'matches.index';
        $showRouteName = $isPublicApp ? 'app.matches.show' : 'matches.show';
        $placeBetRouteName = $isPublicApp ? 'app.matches.bets.store' : 'matches.bets.store';
        $betsRouteName = $isPublicApp ? 'app.bets.index' : 'bets.index';
        $cancelRouteName = $isPublicApp ? 'app.bets.cancel' : 'bets.cancel';
        $teamA = (string) ($match->team_a_name ?: $match->home_team ?: 'Equipe A');
        $teamB = (string) ($match->team_b_name ?: $match->away_team ?: 'Equipe B');
        $statusLabelMap = [
            'scheduled' => 'Programme',
            'locked' => 'Verrouille',
            'live' => 'Live',
            'finished' => 'Termine',
            'settled' => 'Regle',
            'cancelled' => 'Annule',
        ];
        $statusClassMap = [
            'scheduled' => 'is-upcoming',
            'locked' => 'is-upcoming',
            'live' => 'is-live',
            'finished' => 'is-finished',
            'settled' => 'is-finished',
            'cancelled' => 'is-finished',
        ];
        $statusKey = (string) $match->status;
        $statusLabel = $statusLabelMap[$statusKey] ?? strtoupper($statusKey);
        $statusClass = $statusClassMap[$statusKey] ?? 'is-finished';
        $resultLabelMap = [
            'home' => 'Victoire equipe A',
            'away' => 'Victoire equipe B',
            'draw' => 'Match nul',
            'void' => 'Resultat annule',
        ];
        $resultLabel = $resultLabelMap[(string) $match->result] ?? ((string) $match->result !== '' ? strtoupper((string) $match->result) : '-');
        $predictionLabelMap = [
            'home' => $teamA,
            'away' => $teamB,
            'draw' => 'Draw',
        ];
        $selectedKey = old('selection_key', ($options[0]['key'] ?? null));
        $relatedPool = [
            '/template/assets/img/portfolio/1200/portfolio-1.jpg',
            '/template/assets/img/portfolio/1200/portfolio-2.jpg',
            '/template/assets/img/portfolio/1200/portfolio-3.jpg',
            '/template/assets/img/portfolio/1200/portfolio-4.jpg',
            '/template/assets/img/portfolio/1200/portfolio-5.jpg',
            '/template/assets/img/portfolio/1200/portfolio-6.jpg',
            '/template/assets/img/portfolio/1200/portfolio-7.jpg',
            '/template/assets/img/portfolio/1200/portfolio-8.jpg',
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
                    <h2 class="ph-caption-subtitle">ERAH Match Detail</h2>
                    <h1 class="ph-caption-title">{{ $teamA }} vs {{ $teamB }}</h1>
                    <div class="ph-caption-description max-width-900">
                        Match #{{ $match->id }} - {{ $statusLabel }} - debut {{ $match->starts_at?->format('d/m/Y H:i') ?? '-' }}.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Match Detail</h2>
                        <h1 class="ph-caption-title">{{ $teamA }} vs {{ $teamB }}</h1>
                        <div class="ph-caption-description max-width-900">
                            Analyse rapide, options de pari et suivi de votre ticket.
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
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="match-detail-toolbar tt-anim-fadeinup">
                    <div>
                        <span class="match-detail-status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                    <a href="{{ route($indexRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                        <span data-hover="Retour matchs">Retour matchs</span>
                    </a>
                </div>

                <div class="tt-sticker">
                    <div class="tt-row">
                        <div class="tt-col-lg-4 margin-bottom-50">
                            <div class="tt-sticker-sticky tt-sticky-element">
                                <div class="tt-heading tt-heading-lg margin-bottom-30">
                                    <h2 class="tt-heading-title tt-text-reveal">Infos Match</h2>
                                </div>

                                <div class="tt-project-info-list tt-anim-fadeinup">
                                    <ul>
                                        <li>
                                            <div class="pi-list-heading">Affiche</div>
                                            <div class="pi-list-cont">{{ $teamA }} vs {{ $teamB }}</div>
                                        </li>
                                        <li>
                                            <div class="pi-list-heading">Debut</div>
                                            <div class="pi-list-cont">{{ $match->starts_at?->format('d/m/Y H:i') ?? '-' }}</div>
                                        </li>
                                        <li>
                                            <div class="pi-list-heading">Lock</div>
                                            <div class="pi-list-cont">{{ $match->locked_at?->format('d/m/Y H:i') ?? '-' }}</div>
                                        </li>
                                        <li>
                                            <div class="pi-list-heading">Paris</div>
                                            <div class="pi-list-cont">{{ (int) ($match->bets_count ?? 0) }}</div>
                                        </li>
                                        <li>
                                            <div class="pi-list-heading">Resultat</div>
                                            <div class="pi-list-cont">{{ $resultLabel }}</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="tt-col-lg-8">
                            <article class="match-card tt-anim-fadeinup margin-bottom-20">
                                <h3>Parier sur le vainqueur</h3>
                                <p class="match-card-note">Wallet: {{ (int) ($walletBalance ?? 0) }} bet_points</p>

                                @if($isPublicApp && auth()->guest())
                                    <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Connexion pour parier">Connexion pour parier</span>
                                    </a>
                                @elseif($betIsOpen)
                                    <form method="POST" action="{{ route($placeBetRouteName, $match->id) }}">
                                        @csrf

                                        <div class="match-options-grid">
                                            @foreach($options ?? [] as $option)
                                                @php
                                                    $optionKey = (string) ($option['key'] ?? '');
                                                @endphp
                                                <label class="match-option">
                                                    <input
                                                        type="radio"
                                                        name="selection_key"
                                                        value="{{ $optionKey }}"
                                                        {{ $selectedKey === $optionKey ? 'checked' : '' }}
                                                        required
                                                    >
                                                    <span class="match-option-label">
                                                        <strong>{{ $option['label'] ?? 'Selection' }}</strong>
                                                        <span>Cote {{ $option['odds'] ?? '-' }}</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>

                                        <div class="match-form-grid">
                                            <div>
                                                <label for="stake_points">Mise (bet_points)</label>
                                                <input id="stake_points" name="stake_points" type="number" min="1" step="1" value="{{ old('stake_points', 100) }}" required>
                                            </div>
                                            <div>
                                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                    <span data-hover="Placer mon pari">Placer mon pari</span>
                                                </button>
                                            </div>
                                        </div>

                                        <input type="hidden" name="idempotency_key" value="web-bet-{{ auth()->id() }}-{{ $match->id }}-{{ now()->timestamp }}">
                                    </form>
                                @else
                                    <p class="match-card-note">Les paris sont fermes pour ce match.</p>
                                @endif
                            </article>

                            <article class="match-card tt-anim-fadeinup">
                                <h3>Mon pari</h3>
                                @if($myBet)
                                    <div class="match-mybet-grid">
                                        <div class="match-mybet-item">
                                            <strong>{{ strtoupper((string) $myBet->status) }}</strong>
                                            <span>Statut</span>
                                        </div>
                                        <div class="match-mybet-item">
                                            <strong>{{ $predictionLabelMap[(string) $myBet->prediction] ?? (string) $myBet->prediction }}</strong>
                                            <span>Selection</span>
                                        </div>
                                        <div class="match-mybet-item">
                                            <strong>{{ (int) $myBet->stake_points }}</strong>
                                            <span>Mise</span>
                                        </div>
                                        <div class="match-mybet-item">
                                            <strong>{{ number_format((float) $myBet->odds_snapshot, 3) }}</strong>
                                            <span>Cote</span>
                                        </div>
                                    </div>

                                    <div class="match-actions-row">
                                        <a href="{{ route($betsRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                            <span data-hover="Voir mes paris">Voir mes paris</span>
                                        </a>
                                        @if(in_array($myBet->status, [\App\Models\Bet::STATUS_PENDING, \App\Models\Bet::STATUS_PLACED], true))
                                            <form method="POST" action="{{ route($cancelRouteName, $myBet->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="idempotency_key" value="web-cancel-{{ $myBet->id }}-{{ now()->timestamp }}">
                                                <button type="submit" class="tt-btn tt-btn-danger tt-btn-sm">
                                                    <span data-hover="Annuler le pari">Annuler le pari</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <p class="match-card-note">
                                        @if($isPublicApp)
                                            @guest
                                                Le detail public n affiche pas les paris personnels. Connectez-vous pour placer un pari.
                                            @else
                                                Aucun pari actif sur ce match.
                                            @endguest
                                        @else
                                            Aucun pari actif sur ce match.
                                        @endif
                                    </p>
                                @endif
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(($relatedMatches ?? null) && $relatedMatches->count())
            <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top">
                <div class="tt-section-inner tt-wrap max-width-1800">
                    <h2 class="match-related-title tt-anim-fadeinup">Autres matchs</h2>

                    <div class="tt-portfolio-compact-list pcl-caption-hover pcl-image-hover">
                        <div class="pcli-inner">
                            @foreach($relatedMatches as $related)
                                @php
                                    $relatedA = (string) ($related->team_a_name ?: $related->home_team ?: 'Equipe A');
                                    $relatedB = (string) ($related->team_b_name ?: $related->away_team ?: 'Equipe B');
                                    $relatedStatus = $statusLabelMap[(string) $related->status] ?? strtoupper((string) $related->status);
                                    $relatedCover = $relatedPool[$loop->index % count($relatedPool)];
                                @endphp
                                <a href="{{ route($showRouteName, $related->id) }}" class="pcli-item match-related-item tt-anim-fadeinup" data-cursor="Voir<br>Match">
                                    <div class="pcli-item-inner">
                                        <div class="pcli-col pcli-col-image">
                                            <div class="pcli-image">
                                                <img src="{{ $relatedCover }}" loading="lazy" alt="{{ $relatedA }} vs {{ $relatedB }}">
                                            </div>
                                        </div>

                                        <div class="pcli-col pcli-col-count">
                                            <div class="pcli-count"></div>
                                        </div>

                                        <div class="pcli-col pcli-col-caption">
                                            <div class="pcli-caption">
                                                <h2 class="pcli-title">{{ $relatedA }} vs {{ $relatedB }}</h2>
                                                <div class="pcli-categories">
                                                    <div class="pcli-category">{{ $relatedStatus }}</div>
                                                    <div class="pcli-category">{{ $related->starts_at?->format('d/m/Y H:i') ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
