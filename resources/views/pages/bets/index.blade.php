@extends('marketing.layouts.template')

@section('title', 'Paris | ERAH Plateforme')
@section('meta_description', 'Suivi complet de vos paris ERAH: mises actives, gains regles et acces direct aux matchs.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .bet-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .bet-tabs,
        .bet-toolbar-actions,
        .bet-pill-row,
        .bet-card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .bet-tab,
        .bet-pill,
        .bet-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 999px;
            padding: 7px 13px;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .84);
        }

        .bet-tab.active {
            border-color: rgba(223, 11, 11, .5);
            background: rgba(223, 11, 11, .13);
            color: #fff;
        }

        .bet-tab-count {
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 999px;
            padding: 1px 8px;
            font-size: 11px;
            line-height: 1.3;
        }

        .bet-summary-grid,
        .bet-learning-grid {
            display: grid;
            gap: 14px;
        }

        .bet-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 22px;
        }

        .bet-summary-card,
        .bet-quick-band,
        .bet-card,
        .bet-learning-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 22px;
            background:
                linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .012)),
                rgba(255, 255, 255, .02);
        }

        .bet-summary-card {
            padding: 18px;
        }

        .bet-summary-card strong {
            display: block;
            font-size: clamp(30px, 3vw, 44px);
            line-height: .95;
            margin-bottom: 10px;
        }

        .bet-summary-card span {
            display: block;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .68);
        }

        .bet-summary-card p {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, .72);
        }

        .bet-summary-card.tone-danger strong {
            color: #ffb4b4;
        }

        .bet-summary-card.tone-success strong {
            color: #dcffe6;
        }

        .bet-quick-band {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            padding: 22px 24px;
            margin-bottom: 30px;
        }

        .bet-quick-band-copy {
            max-width: 760px;
        }

        .bet-quick-band-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: rgba(255, 255, 255, .62);
        }

        .bet-quick-band-kicker::before {
            content: '';
            width: 28px;
            height: 1px;
            background: #df0b0b;
        }

        .bet-quick-band-title {
            margin: 0 0 8px;
            font-size: clamp(24px, 3vw, 42px);
            line-height: .95;
        }

        .bet-quick-band-text {
            margin: 0;
            color: rgba(255, 255, 255, .74);
            max-width: 720px;
        }

        .bet-list {
            display: grid;
            gap: 14px;
        }

        .bet-card {
            padding: 22px;
        }

        .bet-card.is-won {
            border-color: rgba(104, 220, 150, .4);
            background:
                linear-gradient(160deg, rgba(104, 220, 150, .09), rgba(255, 255, 255, .02)),
                rgba(255, 255, 255, .02);
        }

        .bet-card.is-lost {
            border-color: rgba(255, 124, 124, .35);
        }

        .bet-card.is-void,
        .bet-card.is-cancelled {
            border-color: rgba(126, 196, 255, .35);
        }

        .bet-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .bet-card-title {
            margin: 10px 0 6px;
            font-size: clamp(24px, 2.7vw, 40px);
            line-height: .94;
        }

        .bet-card-subtitle {
            margin: 0;
            color: rgba(255, 255, 255, .7);
            max-width: 860px;
        }

        .bet-card-odds {
            min-width: 140px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 18px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, .03);
            text-align: right;
        }

        .bet-card-odds span {
            display: block;
            margin-bottom: 6px;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .58);
        }

        .bet-card-odds strong {
            font-size: 30px;
            line-height: 1;
        }

        .bet-status-pill.is-active {
            border-color: rgba(255, 214, 102, .48);
            color: #fff0c2;
        }

        .bet-status-pill.is-won {
            border-color: rgba(104, 220, 150, .5);
            color: #deffeb;
        }

        .bet-status-pill.is-lost {
            border-color: rgba(255, 124, 124, .48);
            color: #ffd7d7;
        }

        .bet-status-pill.is-void,
        .bet-status-pill.is-cancelled {
            border-color: rgba(126, 196, 255, .48);
            color: #d8efff;
        }

        .bet-meta-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .bet-meta-card {
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 18px;
            padding: 14px 15px;
            background: rgba(255, 255, 255, .025);
        }

        .bet-meta-card span {
            display: block;
            margin-bottom: 7px;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .58);
        }

        .bet-meta-card strong {
            display: block;
            font-size: 19px;
            line-height: 1.2;
            color: #fff;
        }

        .bet-meta-card small {
            display: block;
            margin-top: 7px;
            color: rgba(255, 255, 255, .55);
            font-size: 12px;
        }

        .bet-card-footer {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, .09);
        }

        .bet-card-note {
            max-width: 760px;
            margin: 0;
            color: rgba(255, 255, 255, .72);
        }

        .bet-card-actions form {
            margin: 0;
        }

        .bet-empty {
            border: 1px dashed rgba(255, 255, 255, .18);
            border-radius: 22px;
            padding: 34px 26px;
            text-align: center;
            color: rgba(255, 255, 255, .75);
        }

        .bet-empty p {
            margin: 0 0 18px;
        }

        .bet-learning-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 34px;
        }

        .bet-learning-card {
            padding: 22px;
        }

        .bet-learning-card h3 {
            margin: 0 0 12px;
            font-size: 28px;
            line-height: .96;
        }

        .bet-learning-card p {
            margin: 0;
            color: rgba(255, 255, 255, .72);
        }

        body.tt-lightmode-on .bet-tab,
        body.tt-lightmode-on .bet-pill,
        body.tt-lightmode-on .bet-status-pill {
            border-color: rgba(148, 163, 184, .28);
            background: rgba(255, 255, 255, .9);
            color: rgba(51, 65, 85, .92);
            box-shadow: 0 12px 26px rgba(148, 163, 184, .12);
        }

        body.tt-lightmode-on .bet-tab.active {
            border-color: rgba(216, 7, 7, .45);
            background: rgba(216, 7, 7, .12);
            color: #991b1b;
        }

        body.tt-lightmode-on .bet-tab-count {
            border-color: rgba(148, 163, 184, .24);
            background: rgba(255, 255, 255, .66);
            color: inherit;
        }

        body.tt-lightmode-on .bet-summary-card,
        body.tt-lightmode-on .bet-quick-band,
        body.tt-lightmode-on .bet-card,
        body.tt-lightmode-on .bet-learning-card,
        body.tt-lightmode-on .bet-card-odds,
        body.tt-lightmode-on .bet-meta-card,
        body.tt-lightmode-on .bet-empty {
            border-color: rgba(148, 163, 184, .22);
            background: linear-gradient(180deg, rgba(255, 255, 255, .94), rgba(248, 250, 252, .88));
            box-shadow: 0 20px 44px rgba(148, 163, 184, .16);
        }

        body.tt-lightmode-on .bet-summary-card span,
        body.tt-lightmode-on .bet-summary-card p,
        body.tt-lightmode-on .bet-quick-band-kicker,
        body.tt-lightmode-on .bet-quick-band-text,
        body.tt-lightmode-on .bet-card-subtitle,
        body.tt-lightmode-on .bet-card-odds span,
        body.tt-lightmode-on .bet-meta-card span,
        body.tt-lightmode-on .bet-meta-card small,
        body.tt-lightmode-on .bet-card-note,
        body.tt-lightmode-on .bet-empty,
        body.tt-lightmode-on .bet-learning-card p {
            color: rgba(51, 65, 85, .82);
        }

        body.tt-lightmode-on .bet-summary-card strong,
        body.tt-lightmode-on .bet-quick-band-title,
        body.tt-lightmode-on .bet-card-title,
        body.tt-lightmode-on .bet-card-odds strong,
        body.tt-lightmode-on .bet-meta-card strong,
        body.tt-lightmode-on .bet-learning-card h3 {
            color: #0f172a;
        }

        body.tt-lightmode-on .bet-summary-card.tone-danger strong {
            color: #c2410c;
        }

        body.tt-lightmode-on .bet-summary-card.tone-success strong {
            color: #166534;
        }

        body.tt-lightmode-on .bet-status-pill.is-active {
            color: #9a5800;
            border-color: rgba(217, 119, 6, .3);
        }

        body.tt-lightmode-on .bet-status-pill.is-won {
            color: #166534;
            border-color: rgba(22, 101, 52, .24);
        }

        body.tt-lightmode-on .bet-status-pill.is-lost {
            color: #b91c1c;
            border-color: rgba(185, 28, 28, .22);
        }

        body.tt-lightmode-on .bet-status-pill.is-void,
        body.tt-lightmode-on .bet-status-pill.is-cancelled {
            color: #1d4ed8;
            border-color: rgba(29, 78, 216, .22);
        }

        .bet-pagin-item-disabled {
            opacity: .35;
            pointer-events: none;
        }

        @media (max-width: 1399.98px) {
            .bet-summary-grid,
            .bet-meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .bet-learning-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .bet-summary-grid,
            .bet-meta-grid {
                grid-template-columns: 1fr;
            }

            .bet-tab,
            .bet-toolbar-actions .tt-btn,
            .bet-card-actions .tt-btn,
            .bet-card-actions button {
                width: 100%;
                justify-content: center;
            }

            .bet-card,
            .bet-learning-card,
            .bet-summary-card,
            .bet-quick-band {
                padding: 18px;
                border-radius: 20px;
            }

            .bet-card-odds {
                width: 100%;
                text-align: left;
            }

            .bet-card-footer,
            .bet-toolbar,
            .bet-quick-band {
                align-items: stretch;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.bets.index' : 'bets.index';
        $matchIndexRouteName = $isPublicApp ? 'app.matches.index' : 'matches.index';
        $matchShowRouteName = $isPublicApp ? 'app.matches.show' : 'matches.show';
        $cancelRouteName = $isPublicApp ? 'app.bets.cancel' : 'bets.cancel';
        $walletRouteName = 'wallet.index';

        $matchLabelResolver = $matchLabelResolver ?? null;
        $statusCounts = array_merge([
            'active' => 0,
            'settled' => 0,
            'won' => 0,
            'cancelled' => 0,
        ], $statusCounts ?? []);
        $summary = array_merge([
            'total' => 0,
            'stake_total' => 0,
            'settlement_total' => 0,
            'active_stake_total' => 0,
            'pending_gain_total' => 0,
            'won_total' => 0,
        ], $summary ?? []);

        $betCount = (int) (($bets ?? null)?->total() ?? 0);
        $profitTotal = (int) $summary['settlement_total'] - (int) $summary['stake_total'];
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
                    <h2 class="ph-caption-subtitle">ERAH Bet Center</h2>
                    <h1 class="ph-caption-title">Paris</h1>
                    <div class="ph-caption-description max-width-900">
                        {{ (int) $statusCounts['active'] }} pari(s) en cours, {{ (int) $statusCounts['settled'] }} regle(s) et {{ (int) $summary['stake_total'] }} points engages depuis votre compte.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Bet Center</h2>
                        <h1 class="ph-caption-title">Paris</h1>
                        <div class="ph-caption-description max-width-900">
                            Suivi complet des mises, du potentiel de gain, des remboursements et du résultat final de chaque pari.
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
                        <textPath xlink:href="#textcircle">Bet Center - Bet Center -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="bet-toolbar">
                    <div class="bet-tabs">
                        <a href="{{ route($indexRouteName, ['tab' => 'active']) }}" class="bet-tab {{ $tab === 'active' ? 'active' : '' }}">
                            En cours
                            <span class="bet-tab-count">{{ (int) $statusCounts['active'] }}</span>
                        </a>
                        <a href="{{ route($indexRouteName, ['tab' => 'settled']) }}" class="bet-tab {{ $tab === 'settled' ? 'active' : '' }}">
                            Regles
                            <span class="bet-tab-count">{{ (int) $statusCounts['settled'] }}</span>
                        </a>
                    </div>

                    <div class="bet-toolbar-actions">
                        <a href="{{ route($matchIndexRouteName) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                            <span data-hover="Voir les matchs">Voir les matchs</span>
                        </a>
                        <a href="{{ route($walletRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                            <span data-hover="Wallet points">Wallet points</span>
                        </a>
                    </div>
                </div>

                <section class="bet-summary-grid">
                    <article class="bet-summary-card tt-anim-fadeinup">
                        <strong>{{ (int) $summary['total'] }}</strong>
                        <span>Paris total</span>
                        <p>{{ $betCount }} visible(s) dans cet onglet.</p>
                    </article>
                    <article class="bet-summary-card tt-anim-fadeinup">
                        <strong>{{ (int) $summary['active_stake_total'] }}</strong>
                        <span>Points encore engages</span>
                        <p>{{ (int) $summary['pending_gain_total'] }} points de gain potentiel au maximum.</p>
                    </article>
                    <article class="bet-summary-card tone-success tt-anim-fadeinup">
                        <strong>{{ (int) $summary['won_total'] }}</strong>
                        <span>Points gagnes</span>
                        <p>{{ (int) $statusCounts['won'] }} pari(s) gagnes a ce jour.</p>
                    </article>
                    <article class="bet-summary-card {{ $profitTotal >= 0 ? 'tone-success' : 'tone-danger' }} tt-anim-fadeinup">
                        <strong>{{ $profitTotal >= 0 ? '+' : '' }}{{ $profitTotal }}</strong>
                        <span>Impact global</span>
                        <p>Difference entre les points regles et les mises engagees.</p>
                    </article>
                </section>

                <section class="bet-quick-band tt-anim-fadeinup">
                    <div class="bet-quick-band-copy">
                        <span class="bet-quick-band-kicker">Rythme de la session</span>
                        <h2 class="bet-quick-band-title">
                            {{ $tab === 'active' ? 'Suivez vos mises avant le lock des matchs.' : 'Relisez vos résultats sans perdre le fil.' }}
                        </h2>
                        <p class="bet-quick-band-text">
                            Chaque pari utilise votre solde points unique. Tant qu\'un match reste ouvert et que la fenêtre d\'annulation le permet, vous pouvez encore revenir sur une mise en cours.
                        </p>
                    </div>

                    <div class="bet-toolbar-actions">
                        <a href="{{ route($matchIndexRouteName) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                            <span data-hover="Placer un pari">Placer un pari</span>
                        </a>
                        <a href="{{ route($indexRouteName, ['tab' => $tab === 'active' ? 'settled' : 'active']) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                            <span data-hover="{{ $tab === 'active' ? 'Voir les regles' : 'Voir les en cours' }}">{{ $tab === 'active' ? 'Voir les regles' : 'Voir les en cours' }}</span>
                        </a>
                    </div>
                </section>

                @if(($bets ?? null) && $bets->count())
                    <section class="bet-list" data-tour="bets-overview">
                        @foreach($bets as $bet)
                            @php
                                $betMarket = $bet->match?->markets->firstWhere('key', $bet->market_key);
                                $betSelection = $betMarket?->selections->firstWhere('key', $bet->selection_key);
                                $selectionLabel = $betSelection?->label ?? $bet->selection_key ?? $bet->prediction;
                                $isActive = in_array($bet->status, [\App\Models\Bet::STATUS_PENDING, \App\Models\Bet::STATUS_PLACED], true);
                                $statusTone = match ($bet->status) {
                                    \App\Models\Bet::STATUS_WON => 'is-won',
                                    \App\Models\Bet::STATUS_LOST => 'is-lost',
                                    \App\Models\Bet::STATUS_VOID => 'is-void',
                                    \App\Models\Bet::STATUS_CANCELLED => 'is-cancelled',
                                    default => 'is-active',
                                };
                                $gainLabel = $isActive ? 'Gain potentiel' : 'Gain réglé';
                                $placedAt = $bet->placed_at ?? $bet->created_at;
                                $matchResult = $bet->match && $bet->match->settled_at
                                    ? $matchLabelResolver->labelForResult($bet->match, $bet->match->result)
                                    : 'En attente';
                                $cardNote = match ($bet->status) {
                                    \App\Models\Bet::STATUS_WON => 'Le pari est gagné. Les points ont déjà été crédités dans votre wallet.',
                                    \App\Models\Bet::STATUS_LOST => 'Le pari est terminé. Aucun gain n\'a été versé sur cette mise.',
                                    \App\Models\Bet::STATUS_VOID => 'Le pari a été remboursé après annulation ou résultat void du match.',
                                    \App\Models\Bet::STATUS_CANCELLED => 'La mise a été annulée et les points ont été renvoyés dans votre solde.',
                                    default => 'Le pari reste actif tant que le match n\'est pas réglé. Vous pouvez encore vérifier le marché ou l\'annuler si la fenêtre est toujours ouverte.',
                                };
                            @endphp

                            <article class="bet-card {{ $statusTone }} tt-anim-fadeinup">
                                <div class="bet-card-head">
                                    <div>
                                        <div class="bet-pill-row">
                                            <strong>{{ $matchLabelResolver->labelForBetStatus($bet->status) }}</strong>
                                            @if($bet->match)
                                                <span class="bet-pill">{{ $matchLabelResolver->labelForStatus($bet->match->status, true) }}</span>
                                            @endif
                                            <span class="bet-pill">{{ $matchLabelResolver->labelForMarketKey($bet->market_key) }}</span>
                                        </div>

                                        <h2 class="bet-card-title">{{ $bet->match?->displayTitle() ?? 'Match indisponible' }}</h2>
                                        <p class="bet-card-subtitle">
                                            Choix: {{ $selectionLabel }}
                                            @if($bet->match?->displaySubtitle())
                                                - {{ $bet->match->displaySubtitle() }}
                                            @elseif($bet->match?->compétition_name)
                                                - {{ $bet->match->compétition_name }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="bet-card-odds">
                                        <span>Cote</span>
                                        <strong>x{{ number_format((float) $bet->odds_snapshot, 2, '.', ' ') }}</strong>
                                    </div>
                                </div>

                                <div class="bet-meta-grid">
                                    <article class="bet-meta-card">
                                        <span>Mise</span>
                                        <strong>{{ (int) $bet->stake_points }} pts</strong>
                                        <small>Debite au moment de la prise de pari.</small>
                                    </article>
                                    <article class="bet-meta-card">
                                        <span>{{ $gainLabel }}</span>
                                        <strong>{{ $isActive ? (int) $bet->potential_payout : (int) $bet->settlement_points }} pts</strong>
                                        <small>{{ $isActive ? 'Estimation maximale si le pari passe.' : 'Montant final regle pour ce pari.' }}</small>
                                    </article>
                                    <article class="bet-meta-card">
                                        <span>Pari place</span>
                                        <strong>{{ optional($placedAt)->format('d/m/Y H:i') ?? '-' }}</strong>
                                        <small>Reference #{{ $bet->id }}</small>
                                    </article>
                                    <article class="bet-meta-card">
                                        <span>Debut du match</span>
                                        <strong>{{ $bet->match?->starts_at?->format('d/m/Y H:i') ?? '-' }}</strong>
                                        <small>Le lock intervient juste avant ce debut.</small>
                                    </article>
                                    <article class="bet-meta-card">
                                        <span>Resultat match</span>
                                        <strong>{{ $matchResult }}</strong>
                                        <small>{{ $bet->match?->settled_at?->format('d/m/Y H:i') ? 'Regle le '.$bet->match->settled_at->format('d/m/Y H:i') : 'Toujours en attente de règlement.' }}</small>
                                    </article>
                                </div>

                                <div class="bet-card-footer">
                                    <p class="bet-card-note">{{ $cardNote }}</p>

                                    <div class="bet-card-actions">
                                        @if($bet->match)
                                            <a href="{{ route($matchShowRouteName, $bet->match_id) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                                <span data-hover="Voir le match">Voir le match</span>
                                            </a>
                                        @endif

                                        @if($isActive)
                                            <form method="POST" action="{{ route($cancelRouteName, $bet->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="idempotency_key" value="web-cancel-{{ $bet->id }}-{{ now()->timestamp }}">
                                                <button type="submit" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                                    <span data-hover="Annuler le pari">Annuler le pari</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </section>

                    @if($bets->hasPages())
                        @php
                            $windowStart = max(1, $bets->currentPage() - 1);
                            $windowEnd = min($bets->lastPage(), $bets->currentPage() + 1);
                        @endphp
                        <div class="tt-pagination tt-pagin-center padding-top-80 padding-top-xlg-100 tt-anim-fadeinup">
                            <div class="tt-pagin-prev">
                                <a href="{{ $bets->previousPageUrl() ?: '#' }}" class="tt-pagin-item tt-magnetic-item {{ $bets->onFirstPage() ? 'bet-pagin-item-disabled' : '' }}">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>
                            <div class="tt-pagin-numbers">
                                @for($page = $windowStart; $page <= $windowEnd; $page++)
                                    <a href="{{ $bets->url($page) }}" class="tt-pagin-item tt-magnetic-item {{ $bets->currentPage() === $page ? 'active' : '' }}">{{ $page }}</a>
                                @endfor
                            </div>
                            <div class="tt-pagin-next">
                                <a href="{{ $bets->nextPageUrl() ?: '#' }}" class="tt-pagin-item tt-pagin-next tt-magnetic-item {{ $bets->hasMorePages() ? '' : 'bet-pagin-item-disabled' }}">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="bet-empty tt-anim-fadeinup">
                        <p>Aucun pari a afficher dans cet onglet pour le moment.</p>
                        <a href="{{ route($matchIndexRouteName) }}" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                            <span data-hover="Explorer les matchs">Explorer les matchs</span>
                        </a>
                    </div>
                @endif

                <section class="bet-learning-grid">
                    <article class="bet-learning-card tt-anim-fadeinup">
                        <h3>Placer un pari</h3>
                        <p>Tout commence sur la page match. Vous choisissez un marche, une selection et la mise en points a engager.</p>
                    </article>
                    <article class="bet-learning-card tt-anim-fadeinup">
                        <h3>Suivre la mise</h3>
                        <p>Les paris en cours restent ici jusqu au règlement. Vous gardez un acces direct au match et au potentiel de gain.</p>
                    </article>
                    <article class="bet-learning-card tt-anim-fadeinup">
                        <h3>Comprendre le résultat</h3>
                        <p>Un pari gagne credite vos points, un void rembourse la mise, un pari perdu ferme simplement la ligne sans gain.</p>
                    </article>
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
