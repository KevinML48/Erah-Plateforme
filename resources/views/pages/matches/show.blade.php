@extends('marketing.layouts.template')

@section('title', 'Evenement esport | ERAH Plateforme')
@section('meta_description', 'Detail d un match classique ou d un tournoi Rocket League avec ses markets de prediction.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .match-detail-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .match-detail-status {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .match-detail-grid {
            display: grid;
            grid-template-columns: minmax(320px, .85fr) minmax(0, 1.15fr);
            gap: 24px;
        }

        .match-panel {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 20px;
            padding: 18px;
            background: rgba(255,255,255,.025);
            display: grid;
            gap: 14px;
        }

        .match-market-stack,
        .match-child-stack {
            display: grid;
            gap: 16px;
        }

        .match-market-card {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 22px;
            padding: 18px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                rgba(255,255,255,.02);
            display: grid;
            gap: 14px;
        }

        .match-market-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .match-market-title {
            margin: 0;
            font-size: clamp(24px, 2.8vw, 38px);
            line-height: .96;
        }

        .match-market-note {
            margin: 0;
            color: rgba(255,255,255,.68);
        }

        .match-option-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .match-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .match-option-label {
            display: block;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 16px;
            padding: 12px 14px;
            cursor: pointer;
            background: rgba(255,255,255,.02);
            transition: border-color .2s ease, background .2s ease, transform .2s ease;
        }

        .match-option-label strong {
            display: block;
            font-size: 15px;
            line-height: 1.25;
        }

        .match-option-label span {
            display: block;
            margin-top: 6px;
            color: rgba(255,255,255,.62);
            font-size: 12px;
        }

        .match-option input:checked + .match-option-label {
            border-color: rgba(223,11,11,.55);
            background: rgba(223,11,11,.14);
            transform: translateY(-2px);
        }

        .match-market-form {
            display: grid;
            gap: 12px;
        }

        .match-market-form-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            align-items: end;
        }

        .match-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .match-summary-card {
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 16px;
            padding: 12px 14px;
            background: rgba(255,255,255,.02);
        }

        .match-summary-card span {
            display: block;
            margin-bottom: 6px;
            color: rgba(255,255,255,.62);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .match-summary-card strong {
            color: #fff;
            font-size: 15px;
        }

        .match-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .match-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 999px;
            padding: 5px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        @media (max-width: 1199.98px) {
            .match-detail-grid,
            .match-option-grid,
            .match-summary-grid {
                grid-template-columns: 1fr;
            }

            .match-market-form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.matches.index' : 'matches.index';
        $placeBetRouteName = $isPublicApp ? 'app.matches.bets.store' : 'matches.bets.store';
        $betsRouteName = $isPublicApp ? 'app.bets.index' : 'bets.index';
        $cancelRouteName = $isPublicApp ? 'app.bets.cancel' : 'bets.cancel';
        $teamA = (string) ($match->team_a_name ?: $match->home_team ?: 'Equipe A');
        $teamB = (string) ($match->team_b_name ?: $match->away_team ?: 'Equipe B');
        $isTournament = $match->event_type === \App\Models\EsportMatch::EVENT_TYPE_TOURNAMENT_RUN;
        $matchLabelResolver = $matchLabelResolver ?? null;
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
                    <h2 class="ph-caption-subtitle">{{ $gameLabel }} - {{ $eventTypeLabel }}</h2>
                    <h1 class="ph-caption-title">{{ $match->displayTitle() }}</h1>
                    <div class="ph-caption-description max-width-900">
                        {{ $match->displaySubtitle() ?: ($isTournament ? 'Avant le TOP 16, la prediction porte sur le parcours final de ERAH. Une fois la phase matchs debloquee, les matchs enfants apparaissent ici.' : 'Suivez les infos BO, le verrouillage et les markets disponibles sur ce match.') }}
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
                    <div class="match-pill-row">
                        <span class="match-detail-status">{{ $matchLabelResolver->labelForStatus($match->status) }}</span>
                        <span class="match-pill">{{ $gameLabel }}</span>
                        <span class="match-pill">{{ $eventTypeLabel }}</span>
                        @if($match->best_of)
                            <span class="match-pill">BO{{ $match->best_of }}</span>
                        @endif
                    </div>
                    <a href="{{ route($indexRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                        <span data-hover="Retour a la liste">Retour a la liste</span>
                    </a>
                </div>

                <div class="match-detail-grid">
                    <div class="tt-sticker">
                        <div class="tt-sticker-sticky tt-sticky-element">
                            <section class="match-panel tt-anim-fadeinup">
                                <h3 class="tt-heading-title">Infos evenement</h3>
                                <div class="tt-project-info-list">
                                    <ul>
                                        <li><div class="pi-list-heading">Jeu</div><div class="pi-list-cont">{{ $gameLabel }}</div></li>
                                        <li><div class="pi-list-heading">Type</div><div class="pi-list-cont">{{ $eventTypeLabel }}</div></li>
                                        <li><div class="pi-list-heading">Etat</div><div class="pi-list-cont">{{ $matchLabelResolver->labelForStatus($match->status) }}</div></li>
                                        <li><div class="pi-list-heading">Debut</div><div class="pi-list-cont">{{ $match->starts_at?->format('d/m/Y H:i') ?? '-' }}</div></li>
                                        <li><div class="pi-list-heading">Cloture des predictions</div><div class="pi-list-cont">{{ $match->locked_at?->format('d/m/Y H:i') ?? '-' }}</div></li>
                                        <li><div class="pi-list-heading">Competition</div><div class="pi-list-cont">{{ $match->competition_name ?: '-' }}</div></li>
                                        <li><div class="pi-list-heading">Phase</div><div class="pi-list-cont">{{ $match->competition_stage ?: '-' }}</div></li>
                                        <li><div class="pi-list-heading">Split</div><div class="pi-list-cont">{{ $match->competition_split ?: '-' }}</div></li>
                                        <li><div class="pi-list-heading">Pronostics</div><div class="pi-list-cont">{{ (int) $match->bets_count }}</div></li>
                                        @if($match->result)
                                            <li><div class="pi-list-heading">Resultat</div><div class="pi-list-cont">{{ $matchLabelResolver->labelForResult($match, $match->result) }}</div></li>
                                        @endif
                                        @if($match->team_a_score !== null && $match->team_b_score !== null)
                                            <li><div class="pi-list-heading">Score</div><div class="pi-list-cont">{{ $match->team_a_score }} - {{ $match->team_b_score }}</div></li>
                                        @endif
                                    </ul>
                                </div>

                                @if($match->parentMatch)
                                    <div class="match-pill-row">
                                        <span class="match-pill">Tournoi parent: {{ $match->parentMatch->event_name ?: $match->parentMatch->competition_name ?: 'Tournoi RL' }}</span>
                                    </div>
                                @endif
                            </section>
                        </div>
                    </div>

                    <div class="match-market-stack">
                        @foreach($markets as $market)
                            @php
                                $marketBet = $myBetsByMarket[(string) $market->key] ?? null;
                                $selectedKey = old('selection_key', $market->selections[0]->key ?? null);
                                $marketBetSelection = $marketBet ? $market->selections->firstWhere('key', $marketBet->selection_key) : null;
                            @endphp
                            <section class="match-market-card tt-anim-fadeinup">
                                <div class="match-market-head">
                                    <div>
                                        <h3 class="match-market-title">{{ $market->title }}</h3>
                                        <p class="match-market-note">
                                            @if($market->key === \App\Models\MatchMarket::KEY_TOURNAMENT_FINISH)
                                                Predisez jusqu ou ERAH ira dans le tournoi.
                                            @elseif($market->key === \App\Models\MatchMarket::KEY_EXACT_SCORE)
                                                Prediction du score final du BO.
                                            @else
                                                Parier sur le vainqueur du match avec le moteur classique.
                                            @endif
                                        </p>
                                    </div>
                                    <span class="match-pill">{{ $matchLabelResolver->labelForMarketKey($market->key) }}</span>
                                </div>

                                @if($marketBet)
                                    <div class="match-summary-grid">
                                        <article class="match-summary-card">
                                            <span>Statut</span>
                                            <strong>{{ $matchLabelResolver->labelForBetStatus($marketBet->status) }}</strong>
                                        </article>
                                        <article class="match-summary-card">
                                            <span>Selection</span>
                                            <strong>{{ $marketBetSelection?->label ?? $marketBet->selection_key }}</strong>
                                        </article>
                                        <article class="match-summary-card">
                                            <span>Mise</span>
                                            <strong>{{ (int) $marketBet->stake_points }}</strong>
                                        </article>
                                    </div>

                                    <div class="match-pill-row">
                                        <a href="{{ route($betsRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                            <span data-hover="Voir mes pronostics">Voir mes pronostics</span>
                                        </a>
                                        @if(in_array($marketBet->status, [\App\Models\Bet::STATUS_PENDING, \App\Models\Bet::STATUS_PLACED], true))
                                            <form method="POST" action="{{ route($cancelRouteName, $marketBet->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="idempotency_key" value="web-cancel-{{ $marketBet->id }}-{{ now()->timestamp }}">
                                                <button type="submit" class="tt-btn tt-btn-danger tt-btn-sm">
                                                    <span data-hover="Annuler ce pari">Annuler ce pari</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @elseif($isPublicApp && auth()->guest())
                                    <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Connexion requise">Connexion requise</span>
                                    </a>
                                @elseif($betIsOpen)
                                    <form method="POST" action="{{ route($placeBetRouteName, $match->id) }}" class="match-market-form">
                                        @csrf
                                        <input type="hidden" name="market_key" value="{{ $market->key }}">

                                        <div class="match-option-grid">
                                            @foreach($market->selections as $option)
                                                @php
                                                    $optionKey = (string) $option->key;
                                                    $inputId = 'market-'.$market->key.'-'.$optionKey;
                                                @endphp
                                                <label class="match-option" for="{{ $inputId }}">
                                                    <input id="{{ $inputId }}" type="radio" name="selection_key" value="{{ $optionKey }}" {{ $selectedKey === $optionKey ? 'checked' : '' }} required>
                                                    <span class="match-option-label">
                                                        <strong>{{ $option->label }}</strong>
                                                        <span>Cote {{ number_format((float) $option->odds, 3) }}</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>

                                        <div class="match-market-form-grid">
                                            <div>
                                                <label for="stake-{{ $market->key }}">Mise (points)</label>
                                                <input class="tt-form-control" id="stake-{{ $market->key }}" name="stake_points" type="number" min="1" step="1" value="{{ old('stake_points', 100) }}" required>
                                            </div>
                                            <div>
                                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                    <span data-hover="Placer prediction">Placer prediction</span>
                                                </button>
                                            </div>
                                        </div>

                                        <input type="hidden" name="idempotency_key" value="web-bet-{{ auth()->id() }}-{{ $match->id }}-{{ $market->key }}-{{ now()->timestamp }}">
                                    </form>
                                @else
                                    <p class="match-market-note">Les predictions sont actuellement fermees pour ce marche.</p>
                                @endif
                            </section>
                        @endforeach

                        @if($isTournament)
                            <section class="match-market-card tt-anim-fadeinup">
                                <div class="match-market-head">
                                    <div>
                                        <h3 class="match-market-title">Phase matchs TOP 16</h3>
                                        <p class="match-market-note">Les matchs reels n apparaissent qu apres debloquage de la phase matchs par l admin.</p>
                                    </div>
                                    <span class="match-pill">{{ $match->child_matches_count }} match(s)</span>
                                </div>

                                @if($match->hasUnlockedChildMatches() && $match->childMatches->count())
                                    <div class="match-child-stack">
                                        @foreach($match->childMatches as $childMatch)
                                            <article class="match-panel">
                                                <div class="match-market-head">
                                                    <div>
                                                        <h4 class="match-market-title" style="font-size:26px">{{ $childMatch->displayTitle() }}</h4>
                                                        <p class="match-market-note">{{ $childMatch->starts_at?->format('d/m/Y H:i') ?? '-' }}{{ $childMatch->best_of ? ' - BO'.$childMatch->best_of : '' }}</p>
                                                    </div>
                                                    <a href="{{ route($isPublicApp ? 'app.matches.show' : 'matches.show', $childMatch->id) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                                        <span data-hover="Voir le match">Voir le match</span>
                                                    </a>
                                                </div>
                                                <div class="match-pill-row">
                                                    <span class="match-pill">{{ $matchLabelResolver->labelForStatus($childMatch->status, true) }}</span>
                                                    <span class="match-pill">Pronostics {{ (int) $childMatch->bets_count }}</span>
                                                    <span class="match-pill">Resultat {{ $matchLabelResolver->labelForResult($childMatch, $childMatch->result) }}</span>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                @elseif($match->hasUnlockedChildMatches())
                                    <div class="match-panel">La phase matchs est debloquee, mais aucun match enfant n a encore ete publie.</div>
                                @else
                                    <div class="match-panel">Le tournoi n a pas encore atteint la phase matchs. Pour l instant, seule la prediction de parcours final est ouverte.</div>
                                @endif
                            </section>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(($relatedMatches ?? null) && $relatedMatches->count())
            <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top">
                <div class="tt-section-inner tt-wrap max-width-1800">
                    <h2 class="match-market-title tt-anim-fadeinup" style="font-size:58px">Autres evenements</h2>
                    <div class="match-child-stack">
                        @foreach($relatedMatches as $related)
                            <article class="match-panel tt-anim-fadeinup">
                                <div class="match-market-head">
                                    <div>
                                        <h3 class="match-market-title" style="font-size:30px">{{ $related->displayTitle() }}</h3>
                                        <p class="match-market-note">{{ $related->competition_name ?: ($related->displaySubtitle() ?: 'Acces detail a cet evenement esport.') }}</p>
                                    </div>
                                    <a href="{{ route($isPublicApp ? 'app.matches.show' : 'matches.show', $related->id) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                        <span data-hover="Voir le detail">Voir le detail</span>
                                    </a>
                                </div>
                                <div class="match-pill-row">
                                    <span class="match-pill">{{ $matchLabelResolver->labelForStatus($related->status, true) }}</span>
                                    <span class="match-pill">{{ $related->starts_at?->format('d/m/Y H:i') ?? '-' }}</span>
                                    @if($related->best_of)
                                        <span class="match-pill">BO{{ $related->best_of }}</span>
                                    @endif
                                </div>
                            </article>
                        @endforeach
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
