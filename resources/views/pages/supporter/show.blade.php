@extends('marketing.layouts.template')

@section('title', 'Supporter ERAH | ERAH Esport')
@section('meta_description', 'Soutenez ERAH Esport avec les formules Supporter ERAH mensuelle, 6 mois ou annuelle et debloquez des avantages exclusifs.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <link rel="stylesheet" href="/template/assets/css/blog.css">
    <style>
        .supporter-shell { display: grid; gap: 26px; }
        .supporter-overview { display: grid; grid-template-columns: 1.35fr .95fr; gap: 18px; align-items: stretch; }
        .supporter-card {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 22px;
            padding: 24px;
            background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
        }
        .supporter-overview > .supporter-card { height: 100%; }
        .supporter-overview > aside.supporter-card { display: flex; flex-direction: column; }
        .supporter-pricing-card { display: flex; flex-direction: column; }
        .supporter-kpis { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .supporter-kpi { border: 1px solid rgba(255,255,255,.14); border-radius: 16px; padding: 16px; }
        .supporter-kpi strong { display: block; font-size: 34px; line-height: 1; }
        .supporter-kpi span { display: block; margin-top: 6px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,.68); }
        .supporter-plan-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-top: 24px; align-items: stretch; }
        .supporter-plan-card {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 18px;
            padding: 18px;
            background: rgba(255,255,255,.03);
            display: grid;
            gap: 14px;
            align-content: start;
            height: 100%;
            grid-template-rows: auto auto minmax(0, 1fr) auto;
        }
        .supporter-plan-card.is-recommended {
            border-color: rgba(223, 11, 11, .38);
            box-shadow: inset 0 0 0 1px rgba(223, 11, 11, .08);
            background: linear-gradient(180deg, rgba(223, 11, 11, .08), rgba(255,255,255,.03));
        }
        .supporter-plan-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .supporter-plan-head h4 { margin: 0; font-size: 18px; line-height: 1.2; min-height: 46px; }
        .supporter-plan-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .supporter-plan-tag.is-saving {
            border-color: rgba(255, 215, 107, .34);
            color: #ffd76b;
            background: rgba(255, 215, 107, .08);
        }
        .supporter-plan-price { display: flex; align-items: baseline; gap: 10px; flex-wrap: wrap; min-height: 62px; }
        .supporter-plan-price strong { font-size: 40px; line-height: 1; }
        .supporter-plan-price span { color: rgba(255,255,255,.72); }
        .supporter-plan-meta { display: grid; gap: 12px; color: rgba(255,255,255,.78); align-content: start; }
        .supporter-plan-meta p { margin: 0; }
        .supporter-plan-lead { min-height: 104px; }
        .supporter-plan-details {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 8px;
        }
        .supporter-plan-details li {
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 12px;
            padding: 10px 12px;
            background: rgba(255,255,255,.025);
            min-height: 62px;
            display: grid;
            align-content: center;
            gap: 4px;
        }
        .supporter-plan-details span {
            display: block;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.62);
        }
        .supporter-plan-details strong {
            display: block;
            font-size: 16px;
            line-height: 1.25;
            color: #fff;
        }
        .supporter-plan-footer { display: grid; gap: 10px; margin-top: auto; }
        .supporter-plan-footer .tt-btn { width: 100%; justify-content: center; }
        .supporter-billing-note {
            margin-top: 18px;
            padding: 14px 16px;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 14px;
            color: rgba(255,255,255,.74);
            background: rgba(255,255,255,.025);
        }
        .supporter-note-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 14px;
        }
        .supporter-note-tile {
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 14px;
            padding: 14px 16px;
            background: rgba(255,255,255,.025);
            min-height: 112px;
        }
        .supporter-note-tile strong {
            display: block;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.66);
            margin-bottom: 8px;
        }
        .supporter-note-tile span {
            display: block;
            line-height: 1.45;
            color: rgba(255,255,255,.82);
        }
        .supporter-pricing-fill {
            margin-top: 14px;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 18px;
            padding: 18px;
            background: rgba(255,255,255,.03);
            display: grid;
            gap: 14px;
            align-content: start;
            flex: 1;
        }
        .supporter-pricing-fill h4 {
            margin: 0;
            font-size: 22px;
            line-height: 1.08;
            text-transform: uppercase;
        }
        .supporter-pricing-fill-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }
        .supporter-pricing-fill-item {
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(255,255,255,.025);
            display: grid;
            gap: 4px;
            align-content: start;
        }
        .supporter-pricing-fill-item span {
            display: block;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.62);
        }
        .supporter-pricing-fill-item strong {
            display: block;
            font-size: 16px;
            line-height: 1.3;
            color: #fff;
        }
        .supporter-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
        .supporter-wall-grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 12px; }
        .supporter-wall-item {
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 18px;
            padding: 16px 14px;
            text-align: center;
            background: rgba(255,255,255,.03);
        }
        .supporter-wall-item img {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255,255,255,.2);
            margin-bottom: 10px;
            background: rgba(255,255,255,.06);
        }
        .supporter-badge { display: inline-flex; align-items: center; gap: 6px; border: 1px solid rgba(255,255,255,.16); border-radius: 999px; padding: 5px 10px; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; }
        .supporter-badge.is-progress {
            border-color: rgba(255, 208, 92, .42);
            color: #ffd86b;
            background: linear-gradient(180deg, rgba(255, 208, 92, .14), rgba(255, 208, 92, .05));
            box-shadow: inset 0 0 0 1px rgba(255, 224, 145, .08);
        }
        .supporter-badge.is-founder { border-color: rgba(255, 195, 87, .45); color: #ffe9b0; }
        .supporter-goals .tt-avlist-description { max-width: 520px; }
        .supporter-progress-line {
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.10);
            overflow: hidden;
            margin-top: 10px;
        }
        .supporter-progress-line span {
            display: block;
            height: 100%;
            min-width: 16px;
            max-width: 100%;
            background: linear-gradient(90deg, #df0b0b, #ff6a3d 60%, #ffa07f);
            box-shadow: 0 0 16px rgba(223, 11, 11, .18);
        }
        body:not(.is-mobile) .supporter-goals .tt-avlist-item:hover .supporter-progress-line,
        body:not(.is-mobile) .supporter-goals .tt-avlist-item:focus .supporter-progress-line {
            background: rgba(0,0,0,.12);
            border: 1px solid rgba(0,0,0,.18);
        }
        body:not(.is-mobile) .supporter-goals .tt-avlist-item:hover .supporter-progress-line span,
        body:not(.is-mobile) .supporter-goals .tt-avlist-item:focus .supporter-progress-line span {
            background: linear-gradient(90deg, #c50808, #ff4c1f 60%, #ff8c67);
            box-shadow: 0 0 18px rgba(197, 8, 8, .24);
        }
        .supporter-summary-list { display: grid; gap: 10px; }
        .supporter-summary-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .supporter-summary-row:last-child { border-bottom: 0; padding-bottom: 0; }
        .supporter-side-stack { display: flex; flex-direction: column; gap: 16px; margin-top: 28px; flex: 1; }
        .supporter-side-panel {
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 18px;
            padding: 18px;
            background: rgba(255,255,255,.03);
        }
        .supporter-side-panel h4 { margin: 0 0 10px; font-size: 22px; line-height: 1.1; text-transform: uppercase; }
        .supporter-side-panel p { margin: 0; color: rgba(255,255,255,.76); line-height: 1.5; }
        .supporter-side-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .supporter-side-tile {
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 16px;
            padding: 16px;
            background: rgba(255,255,255,.025);
            min-height: 144px;
            display: grid;
            align-content: start;
            gap: 10px;
        }
        .supporter-side-tile strong {
            display: block;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.72);
        }
        .supporter-side-tile span {
            display: block;
            font-size: 24px;
            line-height: 1.08;
            text-transform: uppercase;
        }
        .supporter-side-links { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
        .supporter-side-fill {
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 18px;
            padding: 18px;
            background: rgba(255,255,255,.03);
            display: grid;
            gap: 14px;
            align-content: start;
            flex: 1;
            min-height: 220px;
        }
        .supporter-side-fill h4 {
            margin: 0;
            font-size: 22px;
            line-height: 1.08;
            text-transform: uppercase;
        }
        .supporter-side-fill-list {
            display: grid;
            gap: 10px;
        }
        .supporter-side-fill-item {
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(255,255,255,.025);
            display: grid;
            gap: 4px;
        }
        .supporter-side-fill-item span {
            display: block;
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255,255,255,.62);
        }
        .supporter-side-fill-item strong {
            display: block;
            font-size: 16px;
            line-height: 1.3;
            color: #fff;
        }
        .supporter-benefits-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .supporter-benefit-card {
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 18px;
            padding: 18px;
            background: rgba(255,255,255,.03);
            display: grid;
            gap: 12px;
            min-height: 220px;
        }
        .supporter-benefit-card h4 {
            margin: 0;
            font-size: 30px;
            line-height: 1.05;
            text-transform: uppercase;
        }
        .supporter-benefit-card p {
            margin: 0;
            color: rgba(255,255,255,.78);
            line-height: 1.5;
        }
        .supporter-campaign-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .supporter-campaign-card { border: 1px solid rgba(255,255,255,.14); border-radius: 20px; padding: 18px; background: rgba(255,255,255,.03); }
        .supporter-campaign-clips { display: grid; gap: 10px; margin-top: 14px; }
        .supporter-campaign-clip { display: flex; align-items: center; gap: 10px; }
        .supporter-campaign-clip img { width: 72px; height: 50px; object-fit: cover; border-radius: 10px; }
        body.tt-lightmode-on .supporter-card,
        body.tt-lightmode-on .supporter-plan-card,
        body.tt-lightmode-on .supporter-pricing-fill,
        body.tt-lightmode-on .supporter-note-tile,
        body.tt-lightmode-on .supporter-side-panel,
        body.tt-lightmode-on .supporter-side-tile,
        body.tt-lightmode-on .supporter-side-fill,
        body.tt-lightmode-on .supporter-side-fill-item,
        body.tt-lightmode-on .supporter-pricing-fill-item,
        body.tt-lightmode-on .supporter-benefit-card,
        body.tt-lightmode-on .supporter-campaign-card,
        body.tt-lightmode-on .supporter-wall-item,
        body.tt-lightmode-on .supporter-kpi,
        body.tt-lightmode-on .supporter-plan-details li {
            border-color: rgba(148, 163, 184, .26);
            background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(244,247,252,.94));
            box-shadow: 0 18px 38px rgba(148, 163, 184, .16);
        }
        body.tt-lightmode-on .supporter-plan-card.is-recommended {
            border-color: rgba(216, 7, 7, .26);
            background: linear-gradient(180deg, rgba(255, 241, 241, .98), rgba(255,255,255,.94));
            box-shadow: inset 0 0 0 1px rgba(216, 7, 7, .08), 0 22px 42px rgba(216, 7, 7, .10);
        }
        body.tt-lightmode-on .supporter-plan-tag,
        body.tt-lightmode-on .supporter-badge,
        body.tt-lightmode-on .supporter-note-tile strong,
        body.tt-lightmode-on .supporter-side-fill-item span,
        body.tt-lightmode-on .supporter-pricing-fill-item span,
        body.tt-lightmode-on .supporter-kpi span {
            color: rgba(51, 65, 85, .78);
            border-color: rgba(148, 163, 184, .24);
        }
        body.tt-lightmode-on .supporter-plan-price span,
        body.tt-lightmode-on .supporter-plan-meta,
        body.tt-lightmode-on .supporter-note-tile span,
        body.tt-lightmode-on .supporter-side-panel p,
        body.tt-lightmode-on .supporter-benefit-card p {
            color: rgba(51, 65, 85, .80);
        }
        body.tt-lightmode-on .supporter-plan-details strong,
        body.tt-lightmode-on .supporter-pricing-fill-item strong,
        body.tt-lightmode-on .supporter-side-fill-item strong,
        body.tt-lightmode-on .supporter-side-tile strong,
        body.tt-lightmode-on .supporter-side-tile span,
        body.tt-lightmode-on .supporter-benefit-card h4,
        body.tt-lightmode-on .supporter-kpi strong {
            color: #0f172a;
        }
        body.tt-lightmode-on .supporter-plan-tag.is-saving,
        body.tt-lightmode-on .supporter-badge.is-progress {
            background: rgba(255, 237, 213, .9);
            color: #9a3412;
        }
        body.tt-lightmode-on .supporter-progress-line {
            background: rgba(148, 163, 184, .16);
            border-color: rgba(148, 163, 184, .2);
        }
        @media (max-width: 1199.98px) {
            .supporter-overview,
            .supporter-campaign-grid { grid-template-columns: 1fr; }
            .supporter-plan-grid,
            .supporter-benefits-grid,
            .supporter-note-grid,
            .supporter-pricing-fill-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .supporter-wall-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        @media (max-width: 767.98px) {
            .supporter-kpis,
            .supporter-wall-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .supporter-plan-grid,
            .supporter-benefits-grid,
            .supporter-side-grid,
            .supporter-note-grid,
            .supporter-pricing-fill-grid { grid-template-columns: 1fr; }
            .supporter-benefit-card { min-height: 0; }
            .supporter-plan-head h4,
            .supporter-plan-lead,
            .supporter-plan-details li,
            .supporter-note-tile,
            .supporter-side-tile,
            .supporter-pricing-fill,
            .supporter-side-fill { min-height: 0; }
            .supporter-actions .tt-btn { width: 100%; justify-content: center; }
            .supporter-side-links .tt-btn { width: 100%; justify-content: center; }
            .supporter-overview > .supporter-card { height: auto; }
        }
    </style>
@endsection

@section('content')
    @php
        $priceLabel = number_format(((int) ($plan->price_cents ?? 499)) / 100, 2, ',', ' ');
        $supporterSummary = $supporterSummary ?? null;
        $progress = $progress ?? null;
        $planCards = collect($planCards ?? []);
        $defaultPlanCard = $planCards->firstWhere('is_default', true) ?: $planCards->first();
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
                    <h2 class="ph-caption-subtitle">Supporter ERAH</h2>
                    <h1 class="ph-caption-title">Soutenez<br>ERAH</h1>
                    <div class="ph-caption-description max-width-900">
                        La plateforme reste gratuite. L abonnement permet de soutenir le club et debloque des avantages supplementaires.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Supporter ERAH</h2>
                        <h1 class="ph-caption-title">Soutenez<br>ERAH</h1>
                        <div class="ph-caption-description max-width-900">
                            Formules mensuelle, 6 mois et annuelle pour soutenir le club avec une reduction sur les engagements longs.
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
                        <textPath xlink:href="#textcircle">Support ERAH - Support ERAH -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120 border-bottom">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="supporter-shell">
                    <section class="supporter-overview">
                        <article class="supporter-card supporter-pricing-card tt-anim-fadeinup">
                            <div class="tt-heading tt-heading-lg no-margin">
                                <h3 class="tt-heading-subtitle">Communaute</h3>
                                <h2 class="tt-heading-title">{{ $totalSupporters }} supporters soutiennent deja ERAH Esport</h2>
                            </div>
                            <p class="margin-top-20 max-width-900">
                                Supporter ERAH active le badge supporter, les missions exclusives, les votes clips, les reactions premium et des avantages club/IRL.
                            </p>

                            <div class="supporter-kpis margin-top-30">
                                <div class="supporter-kpi">
                                    <strong>{{ $defaultPlanCard['price_label'] ?? $priceLabel }} EUR</strong>
                                    <span>Formule mensuelle</span>
                                </div>
                                <div class="supporter-kpi">
                                    <strong>8% / 16%</strong>
                                    <span>Reduction long terme</span>
                                </div>
                                <div class="supporter-kpi">
                                    <strong>{{ collect($goals ?? [])->where('is_unlocked', true)->count() }}</strong>
                                    <span>Paliers debloques</span>
                                </div>
                            </div>

                            <div class="supporter-plan-grid">
                                @foreach($planCards as $planCard)
                                    <article class="supporter-plan-card {{ $planCard['is_recommended'] ? 'is-recommended' : '' }}">
                                        <div class="supporter-plan-head">
                                            <h4>{{ $planCard['name'] }}</h4>
                                            @if($planCard['discount_percent'] > 0)
                                                <span class="supporter-plan-tag is-saving">-{{ rtrim(rtrim(number_format($planCard['discount_percent'], 2, '.', ''), '0'), '.') }}%</span>
                                            @else
                                                <span class="supporter-plan-tag">Flexible</span>
                                            @endif
                                        </div>
                                        <div class="supporter-plan-price">
                                            <strong>{{ $planCard['price_label'] }} EUR</strong>
                                            <span>
                                                @if($planCard['months'] === 1)
                                                    / mois
                                                @elseif($planCard['months'] === 12)
                                                    / an
                                                @else
                                                    / {{ $planCard['months'] }} mois
                                                @endif
                                            </span>
                                        </div>
                                        <div class="supporter-plan-meta">
                                            <p class="supporter-plan-lead">
                                                @if($planCard['months'] === 1)
                                                    Paiement mensuel flexible pour soutenir ERAH et debloquer tous les avantages supporter.
                                                @elseif($planCard['months'] === 12)
                                                    Paiement annuel en une fois avec reduction maximale et acces supporter continu.
                                                @else
                                                    Paiement sur 6 mois avec reduction immediate et tous les avantages supporter.
                                                @endif
                                            </p>
                                            <ul class="supporter-plan-details">
                                                <li>
                                                    <span>Acces</span>
                                                    <strong>Tous les avantages supporter</strong>
                                                </li>
                                                <li>
                                                    <span>Facturation</span>
                                                    <strong>
                                                        @if($planCard['months'] === 1)
                                                            Debite chaque mois
                                                        @elseif($planCard['months'] === 12)
                                                            Debite une fois par an
                                                        @else
                                                            Debite tous les 6 mois
                                                        @endif
                                                    </strong>
                                                </li>
                                                <li>
                                                    <span>{{ $planCard['months'] > 1 ? 'Economie' : 'Engagement' }}</span>
                                                    <strong>
                                                        @if($planCard['months'] > 1)
                                                            {{ $planCard['savings_label'] }} EUR economises, soit -{{ rtrim(rtrim(number_format($planCard['discount_percent'], 2, '.', ''), '0'), '.') }}%
                                                        @else
                                                            Sans avance sur 6 ou 12 mois
                                                        @endif
                                                    </strong>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="supporter-plan-footer">
                                            @auth
                                                @if(($supporterSummary['is_active'] ?? false) === true && ($supporterSummary['current_plan_key'] ?? null) === $planCard['key'])
                                                    <a href="{{ route('supporter.console') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                        <span data-hover="Formule active">Formule active</span>
                                                    </a>
                                                @elseif(($supporterSummary['is_active'] ?? false) === true)
                                                    <a href="{{ route('supporter.console') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                        <span data-hover="Gerer mon abonnement">Gerer mon abonnement</span>
                                                    </a>
                                                @else
                                                    <form method="POST" action="{{ route('supporter.checkout') }}">
                                                        @csrf
                                                        <input type="hidden" name="plan_key" value="{{ $planCard['key'] }}">
                                                        <button type="submit" class="tt-btn {{ $planCard['is_recommended'] ? 'tt-btn-primary' : 'tt-btn-outline' }} tt-magnetic-item">
                                                            <span data-hover="Choisir cette formule">Choisir cette formule</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="tt-btn {{ $planCard['is_recommended'] ? 'tt-btn-primary' : 'tt-btn-outline' }} tt-magnetic-item">
                                                    <span data-hover="Se connecter pour souscrire">Se connecter pour souscrire</span>
                                                </a>
                                            @endauth
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="supporter-billing-note">
                                Supporter ERAH reste un abonnement. Vous pouvez choisir un debit mensuel a 5,00 EUR, un paiement tous les 6 mois avec 8% de reduction, ou un paiement annuel avec 16% de reduction sur le total.
                            </div>

                            <div class="supporter-note-grid">
                                <article class="supporter-note-tile">
                                    <strong>Inclus partout</strong>
                                    <span>Badge supporter, votes clips, reactions premium et mise en avant sur la plateforme.</span>
                                </article>
                                <article class="supporter-note-tile">
                                    <strong>Missions exclusives</strong>
                                    <span>Bonus XP, mission hebdomadaire supporter et progression fidelite active chaque mois.</span>
                                </article>
                                <article class="supporter-note-tile">
                                    <strong>Avantages club</strong>
                                    <span>Merchandising, drops anticipes, rencontres et activations communaute selon les paliers.</span>
                                </article>
                            </div>

                            <div class="supporter-pricing-fill">
                                <h4>Pourquoi choisir plus long</h4>
                                <div class="supporter-pricing-fill-grid">
                                    <article class="supporter-pricing-fill-item">
                                        <span>Mensuel</span>
                                        <strong>Souple pour commencer sans avancer 6 ou 12 mois.</strong>
                                    </article>
                                    <article class="supporter-pricing-fill-item">
                                        <span>6 mois</span>
                                        <strong>Le bon compromis pour reduire le prix tout en gardant un rythme intermediaire.</strong>
                                    </article>
                                    <article class="supporter-pricing-fill-item">
                                        <span>Annuel</span>
                                        <strong>La formule la plus rentable pour soutenir durablement et payer moins sur le total.</strong>
                                    </article>
                                </div>
                            </div>
                        </article>

                        <aside class="supporter-card tt-anim-fadeinup">
                            <div class="tt-heading tt-heading-sm no-margin">
                                <h3 class="tt-heading-subtitle">Mon statut</h3>
                                <h2 class="tt-heading-title">{{ auth()->check() ? (($supporterSummary['is_active'] ?? false) ? 'Supporter actif' : 'Pret a soutenir') : 'Connectez-vous' }}</h2>
                            </div>

                            <div class="supporter-summary-list margin-top-30">
                                <div class="supporter-summary-row">
                                    <span>Statut</span>
                                    <span class="supporter-badge">{{ strtoupper((string) ($supporterSummary['status'] ?? 'inactive')) }}</span>
                                </div>
                                <div class="supporter-summary-row">
                                    <span>Formule</span>
                                    <span>{{ $supporterSummary['current_plan_name'] ?? 'Aucune formule active' }}</span>
                                </div>
                                <div class="supporter-summary-row">
                                    <span>Badge fidelite</span>
                                    <span>{{ $supporterSummary['loyalty_badge'] ?? 'Aucun pour le moment' }}</span>
                                </div>
                                <div class="supporter-summary-row">
                                    <span>Badge fondateur</span>
                                    <span>{{ ($supporterSummary['is_founder'] ?? false) ? 'Oui' : 'Non' }}</span>
                                </div>
                                <div class="supporter-summary-row">
                                    <span>XP cumule</span>
                                    <span>{{ (int) ($progress->total_xp ?? 0) }}</span>
                                </div>
                                <div class="supporter-summary-row">
                                    <span>Ligue</span>
                                    <span>{{ $progress->league->name ?? 'Aucune' }}</span>
                                </div>
                            </div>

                            <div class="supporter-side-stack">
                                <div class="supporter-side-panel">
                                    <h4>Avant de vous lancer</h4>
                                    <p>La FAQ reste accessible en permanence depuis le menu Plateforme pour comprendre les clips, missions, classement, wallet et l interet de la formule Supporter.</p>
                                    <div class="supporter-side-links">
                                        <a href="{{ route('marketing.faq') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                            <span data-hover="Ouvrir la FAQ">Ouvrir la FAQ</span>
                                        </a>
                                        @auth
                                            <a href="{{ route('app.profile') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Voir mon profil">Voir mon profil</span>
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Se connecter">Se connecter</span>
                                            </a>
                                        @endauth
                                    </div>
                                </div>

                                <div class="supporter-side-grid">
                                    <article class="supporter-side-tile">
                                        <strong>Gratuit</strong>
                                        <span>Compte standard</span>
                                        <p>Clips, commentaires, favoris, missions et progression restent accessibles sans abonnement.</p>
                                    </article>
                                    <article class="supporter-side-tile">
                                        <strong>Supporter</strong>
                                        <span>Visibilite premium</span>
                                        <p>Commentaires prioritaires, reactions premium, votes clips et badge visible dans la plateforme.</p>
                                    </article>
                                    <article class="supporter-side-tile">
                                        <strong>Facturation</strong>
                                        <span>3 rythmes</span>
                                        <p>Mensuel, 6 mois avec 8% de reduction, ou annuel avec 16% de reduction sur le total.</p>
                                    </article>
                                    <article class="supporter-side-tile">
                                        <strong>Club / IRL</strong>
                                        <span>Acces dedie</span>
                                        <p>Drops anticipes, avantages merchandising, rencontres et activations communaute selon les paliers.</p>
                                    </article>
                                </div>

                                <div class="supporter-side-fill">
                                    <h4>Active des le premier jour</h4>
                                    <div class="supporter-side-fill-list">
                                        <div class="supporter-side-fill-item">
                                            <span>Commentaires</span>
                                            <strong>Mise en avant sous les clips admin et visibilite supporter sur la plateforme.</strong>
                                        </div>
                                        <div class="supporter-side-fill-item">
                                            <span>Votes</span>
                                            <strong>Participation aux campagnes clip de la semaine et action du mois ouvertes aux supporters.</strong>
                                        </div>
                                        <div class="supporter-side-fill-item">
                                            <span>Progression</span>
                                            <strong>Bonus XP, mission hebdomadaire supporter et suivi fidelite actif chaque mois.</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </section>

                    <section class="supporter-card tt-anim-fadeinup">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h3 class="tt-heading-subtitle">Avantages</h3>
                            <h2 class="tt-heading-title">Ce que debloque Supporter ERAH</h2>
                        </div>

                        <div class="supporter-benefits-grid">
                            @foreach(($benefitCards ?? []) as $card)
                                <article class="supporter-benefit-card">
                                    <span class="supporter-badge {{ $card['label'] === 'En cours' ? 'is-progress' : '' }}">{{ $card['label'] }}</span>
                                    <h4>{{ $card['title'] }}</h4>
                                    <p>{{ $card['excerpt'] }}</p>
                                </article>
                            @endforeach
                        </div>

                        @auth
                            @if(($supporterSummary['is_active'] ?? false) === true)
                                <div class="supporter-actions margin-top-30">
                                    <a href="{{ route('supporter.console') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Ouvrir l espace supporter">Ouvrir l espace supporter</span>
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="supporter-actions margin-top-30">
                                <a href="{{ route('login') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Se connecter pour activer">Se connecter pour activer</span>
                                </a>
                            </div>
                        @endauth
                    </section>

                    <section class="supporter-card tt-anim-fadeinup">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h3 class="tt-heading-subtitle">Objectifs communaute</h3>
                            <h2 class="tt-heading-title">Paliers supporters</h2>
                        </div>

                        <div class="tt-avards-list supporter-goals">
                            @foreach(($goals ?? []) as $goal)
                                <div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
                                    <div class="tt-avlist-item-inner">
                                        <div class="tt-avlist-col tt-avlist-col-title">
                                            <h4 class="tt-avlist-title">{{ $goal['goal_count'] }} supporters</h4>
                                        </div>
                                        <div class="tt-avlist-col tt-avlist-col-description">
                                            <div class="tt-avlist-description">
                                                <strong>{{ $goal['title'] }}</strong><br>
                                                {{ $goal['description'] }}
                                                <div class="supporter-progress-line"><span style="width: {{ $goal['progress_percent'] }}%"></span></div>
                                            </div>
                                        </div>
                                        <div class="tt-avlist-col tt-avlist-col-info">
                                            <div class="tt-avlist-info">{{ $goal['is_unlocked'] ? 'Debloque' : 'En cours' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    @if(collect($campaigns ?? [])->count())
                        <section class="supporter-card tt-anim-fadeinup">
                            <div class="tt-heading tt-heading-lg margin-bottom-20">
                                <h3 class="tt-heading-subtitle">Clips supporters</h3>
                                <h2 class="tt-heading-title">Votes ouverts</h2>
                            </div>

                            <div class="supporter-campaign-grid">
                                @foreach($campaigns as $campaign)
                                    <article class="supporter-campaign-card">
                                        <span class="supporter-badge">{{ strtoupper($campaign['type']) }}</span>
                                        <h4 class="margin-top-16">{{ $campaign['title'] }}</h4>
                                        <p class="tt-form-text no-margin">Cloture: {{ optional($campaign['ends_at'])->format('d/m/Y H:i') }}</p>
                                        <p class="margin-top-10">{{ $campaign['votes_count'] }} vote(s) enregistres.</p>
                                        <div class="supporter-campaign-clips">
                                            @foreach($campaign['clips'] as $clip)
                                                <a href="{{ $clip['url'] }}" class="supporter-campaign-clip">
                                                    <img src="{{ $clip['thumbnail_url'] }}" alt="{{ $clip['title'] }}">
                                                    <span>{{ $clip['title'] }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <section class="supporter-card tt-anim-fadeinup">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h3 class="tt-heading-subtitle">Mur supporters</h3>
                            <h2 class="tt-heading-title">Reconnaissance publique</h2>
                        </div>

                        @if(collect($wallMembers ?? [])->count())
                            <div class="supporter-wall-grid">
                                @foreach($wallMembers as $member)
                                    <article class="supporter-wall-item">
                                        <img src="{{ $member['avatar_url'] }}" alt="{{ $member['name'] }}">
                                        <strong>{{ $member['name'] }}</strong>
                                        <div class="margin-top-8">
                                            <span class="supporter-badge {{ $member['is_founder'] ? 'is-founder' : '' }}">
                                                {{ $member['is_founder'] ? 'Supporter fondateur' : 'Supporter ERAH' }}
                                            </span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <p class="no-margin">Le mur public se remplira avec les premiers supporters qui acceptent d y apparaitre.</p>
                        @endif
                    </section>
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
@endsection
