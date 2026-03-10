@extends('marketing.layouts.template')

@section('title', ($mode === 'console' ? 'Help Center Console' : 'Help Center').' | ERAH Esport')
@section('meta_description', $page['hero']['subtitle'] ?? "Centre d'aide ERAH")
@section('body_class', 'tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
    <style>
        #tt-page-transition { display: none !important; }
        .erah-help-shell { position: relative; }
        .erah-help-overlap { position: relative; z-index: 4; margin-top: -92px; }
        .erah-help-surface,
        .erah-help-card,
        .erah-help-band,
        .erah-help-faq-card,
        .erah-help-footer-panel,
        .erah-help-assistant-panel {
            position: relative;
            overflow: hidden;
            padding: 34px;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 28px;
            background: linear-gradient(145deg, rgba(255,255,255,.045), rgba(255,255,255,.018));
            box-shadow: 0 20px 60px rgba(0,0,0,.18);
        }
        .erah-help-surface::before,
        .erah-help-card::before,
        .erah-help-band::before,
        .erah-help-faq-card::before,
        .erah-help-footer-panel::before,
        .erah-help-assistant-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(216,7,7,.16), transparent 46%);
            pointer-events: none;
        }
        .erah-help-overline {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            color: rgba(255,255,255,.58);
            font-size: 12px;
            letter-spacing: .22em;
            text-transform: uppercase;
        }
        .erah-help-overline::before {
            content: "";
            width: 28px;
            height: 1px;
            background: rgba(216,7,7,.85);
        }
        .erah-help-search-grid,
        .erah-help-footer-grid,
        .erah-help-faq-toolbar,
        .erah-help-assistant-grid,
        .erah-help-video-grid {
            display: grid;
            gap: 26px;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, .9fr);
        }
        .erah-help-anchor-row,
        .erah-help-search-actions,
        .erah-help-pill-row,
        .erah-help-faq-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .erah-help-anchor-row a,
        .erah-help-chip,
        .erah-help-faq-filters a {
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            padding: 0 18px;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 999px;
            background: rgba(255,255,255,.03);
            color: rgba(255,255,255,.82);
            font-size: 12px;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .erah-help-anchor-row a:hover,
        .erah-help-faq-filters a:hover,
        .erah-help-faq-filters a.is-active {
            color: #fff;
            border-color: rgba(216,7,7,.55);
            background: rgba(216,7,7,.12);
        }
        .erah-help-lead {
            max-width: 820px;
            font-size: 19px;
            line-height: 1.7;
            color: rgba(255,255,255,.82);
        }
        .erah-help-stat-grid,
        .erah-help-discovery-grid,
        .erah-help-quick-grid,
        .erah-help-category-grid,
        .erah-help-faq-grid,
        .erah-help-timeline {
            display: grid;
            gap: 20px;
        }
        .erah-help-stat-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); margin-top: 26px; }
        .erah-help-stat {
            padding: 18px 18px 16px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.03);
        }
        .erah-help-stat-label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255,255,255,.48);
            font-size: 11px;
            letter-spacing: .18em;
            text-transform: uppercase;
        }
        .erah-help-stat-value {
            font-family: "Big Shoulders Display", sans-serif;
            font-size: 50px;
            line-height: .92;
            color: #fff;
        }
        .erah-help-discovery-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .erah-help-quick-grid,
        .erah-help-category-grid,
        .erah-help-faq-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .erah-help-timeline { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .erah-help-list {
            margin: 20px 0 0;
            padding: 0;
            list-style: none;
        }
        .erah-help-list li {
            position: relative;
            padding-left: 18px;
            margin-bottom: 10px;
            color: rgba(255,255,255,.76);
        }
        .erah-help-list li::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 0;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: rgba(216,7,7,.85);
        }
        .erah-help-badge {
            display: inline-flex;
            margin-bottom: 14px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.05);
            color: rgba(255,255,255,.64);
            font-size: 11px;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .erah-help-category-preview {
            display: grid;
            gap: 12px;
            margin-top: 22px;
        }
        .erah-help-category-preview div {
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.03);
            color: rgba(255,255,255,.76);
        }
        .erah-help-faq-card.is-active {
            border-color: rgba(216,7,7,.55);
            box-shadow: 0 20px 40px rgba(216,7,7,.08);
        }
        .erah-help-faq-steps {
            margin: 18px 0 0;
            padding: 0;
            list-style: none;
            counter-reset: faq-step;
        }
        .erah-help-faq-steps li {
            position: relative;
            padding-left: 42px;
            margin-bottom: 14px;
            color: rgba(255,255,255,.8);
        }
        .erah-help-faq-steps li::before {
            counter-increment: faq-step;
            content: counter(faq-step);
            position: absolute;
            top: -1px;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(216,7,7,.14);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
        }
        .erah-help-note {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.03);
            color: rgba(255,255,255,.78);
        }
        .erah-help-video-frame {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,.12);
            background: radial-gradient(circle at top left, rgba(216,7,7,.2), transparent 40%), linear-gradient(135deg, rgba(255,255,255,.04), rgba(255,255,255,.015));
            aspect-ratio: 16 / 9;
        }
        .erah-help-video-frame iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
        .erah-help-video-empty {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            height: 100%;
            padding: 28px;
        }
        .erah-help-step-card {
            padding: 26px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.03);
        }
        .erah-help-step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            margin-bottom: 18px;
            border-radius: 18px;
            background: rgba(216,7,7,.14);
            color: #fff;
            font-family: "Big Shoulders Display", sans-serif;
            font-size: 32px;
        }
        .erah-help-assistant-panel { background: linear-gradient(145deg, rgba(216,7,7,.12), rgba(255,255,255,.025)); }
        .erah-help-assistant-prompt { display: grid; gap: 12px; margin-top: 22px; }
        .erah-help-assistant-prompt div {
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(0,0,0,.28);
            color: rgba(255,255,255,.8);
        }
        @media (max-width: 1399.98px) {
            .erah-help-search-grid,
            .erah-help-footer-grid,
            .erah-help-faq-toolbar,
            .erah-help-assistant-grid,
            .erah-help-video-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 1199.98px) {
            .erah-help-discovery-grid,
            .erah-help-timeline { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 1024px) {
            .erah-help-overlap { margin-top: -38px; }
            .erah-help-quick-grid,
            .erah-help-category-grid,
            .erah-help-faq-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 767.98px) {
            .erah-help-surface,
            .erah-help-card,
            .erah-help-band,
            .erah-help-faq-card,
            .erah-help-footer-panel,
            .erah-help-assistant-panel { padding: 24px; border-radius: 24px; }
            .erah-help-stat-grid,
            .erah-help-discovery-grid,
            .erah-help-timeline { grid-template-columns: 1fr; }
            .erah-help-stat-value { font-size: 42px; }
            .erah-help-lead { font-size: 17px; }
        }
    </style>
@endsection

@section('content')
    @php($search = $page['filters']['search'] ?? '')
    @php($baseRoute = $mode === 'console' ? route('console.help') : route('help.index'))
    <div id="page-header" class="ph-full ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">{{ $mode === 'console' ? 'Support in-app' : 'Help Center / FAQ' }}</h2>
                    <h1 class="ph-caption-title">{{ $page['hero']['title'] }}</h1>
                    <div class="ph-caption-description max-width-700">{{ $page['hero']['subtitle'] }}</div>
                </div>
            </div>
        </div>
        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">{{ $mode === 'console' ? 'Navigation guidee' : 'Source de verite ERAH' }}</h2>
                        <h1 class="ph-caption-title">{{ $page['hero']['title'] }}</h1>
                        <div class="ph-caption-description max-width-700">{{ $page['hero']['subtitle'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tt-scroll-down">
            <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
                <div class="tt-scrd-icon"></div>
                <svg viewBox="0 0 500 500">
                    <defs><path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="textcircle"></path></defs>
                    <text dy="30"><textPath xlink:href="#textcircle">ERAH Help Center - ERAH Help Center -</textPath></text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content" class="erah-help-shell">
        <div class="tt-section no-padding-top padding-bottom-lg-40 padding-bottom-20">
            <div class="tt-section-inner tt-wrap">
                <div class="erah-help-overlap">
                    <div class="erah-help-search-grid">
                        <div class="erah-help-surface">
                            <div class="erah-help-overline">{{ $mode === 'console' ? 'Trouver la bonne reponse vite' : 'Une seule page canonique pour tout comprendre' }}</div>
                            <div class="tt-heading tt-heading-xxlg margin-bottom-20">
                                <h2 class="tt-heading-title">{{ $mode === 'console' ? 'Retrouver un module, une regle ou un parcours sans quitter votre espace.' : 'Comprendre ERAH, son fonctionnement, ses modules et ses bonnes pratiques sans se perdre.' }}</h2>
                            </div>
                            <p class="erah-help-lead">{{ $page['hero']['microcopy'] }}</p>
                            <form method="GET" action="{{ $baseRoute }}" class="margin-top-40">
                                <div class="tt-form-group">
                                    <label>Recherche globale</label>
                                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ $page['hero']['search_placeholder'] }}">
                                </div>
                                <div class="erah-help-search-actions margin-top-25">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Chercher">Chercher</span></button>
                                    <a href="#starter-journey" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Parcours">Bien commencer</span></a>
                                    <a href="#faq-center" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="FAQ">Voir la FAQ</span></a>
                                </div>
                            </form>
                            <div class="erah-help-anchor-row margin-top-30">
                                <a href="#discover">Decouvrir ERAH</a>
                                <a href="#starter-journey">Bien commencer</a>
                                <a href="#understand-platform">Comprendre la plateforme</a>
                                <a href="#faq-center">FAQ detaillee</a>
                                <a href="{{ $page['assistant']['page_url'] }}">Assistant</a>
                            </div>
                        </div>
                        <div class="erah-help-surface">
                            <div class="erah-help-overline">{{ $mode === 'console' ? 'Lecture immediate' : 'Repere rapide' }}</div>
                            <div class="tt-heading tt-heading-lg margin-bottom-10">
                                <h2 class="tt-heading-title">{{ $mode === 'console' ? 'Les bons points d entree depuis votre espace.' : 'Une vue claire des briques qui composent vraiment la plateforme.' }}</h2>
                            </div>
                            <p class="text-muted margin-bottom-0">{{ $mode === 'console' ? 'Ce hub vous aide a revenir vite vers le bon module: matchs, missions, clips, cadeaux, duels ou notifications.' : 'Le help center sert a la fois de page de decouverte, de FAQ unique et de source de verite pour le futur assistant ERAH.' }}</p>
                            <div class="erah-help-stat-grid">
                                <div class="erah-help-stat"><span class="erah-help-stat-label">Categories</span><span class="erah-help-stat-value">{{ $page['overview']['categories'] ?? 0 }}</span></div>
                                <div class="erah-help-stat"><span class="erah-help-stat-label">Questions clefs</span><span class="erah-help-stat-value">{{ $page['overview']['faqs'] ?? 0 }}</span></div>
                                <div class="erah-help-stat"><span class="erah-help-stat-label">Etapes utiles</span><span class="erah-help-stat-value">{{ $page['overview']['steps'] ?? 0 }}</span></div>
                            </div>
                            @if ($mode === 'console' && ! empty($page['consoleLinks']))
                                <div class="erah-help-pill-row margin-top-28">
                                    @foreach ($page['consoleLinks'] as $link)
                                        <span class="erah-help-chip">{{ $link['title'] }}</span>
                                    @endforeach
                                </div>
                            @else
                                <div class="erah-help-pill-row margin-top-28">
                                    <span class="erah-help-chip">Lecture publique sans compte</span>
                                    <span class="erah-help-chip">Participation reservee aux membres</span>
                                    <span class="erah-help-chip">Points, XP, missions et recompenses</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-40 padding-bottom-lg-60 padding-bottom-40 border-top" id="discover">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-heading tt-heading-xxlg margin-bottom-40">
                    <h3 class="tt-heading-subtitle tt-text-uppercase">Decouvrir ERAH</h3>
                    <h2 class="tt-heading-title">Comprendre rapidement ce que la plateforme permet, pour qui elle existe et comment elle se lit au quotidien.</h2>
                </div>
                <div class="erah-help-discovery-grid">
                    @foreach ($page['discovery'] as $panel)
                        <article class="erah-help-band">
                            <div class="erah-help-overline">{{ $panel['eyebrow'] }}</div>
                            <div class="tt-heading tt-heading-lg margin-bottom-15"><h3 class="tt-heading-title">{{ $panel['title'] }}</h3></div>
                            <p class="text-muted">{{ $panel['description'] }}</p>
                            <ul class="erah-help-list">
                                @foreach ($panel['items'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                            <div class="tt-btn-wrap margin-top-25">
                                <a href="{{ $panel['cta_url'] }}" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Ouvrir">{{ $panel['cta_label'] }}</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-30 padding-bottom-lg-60 padding-bottom-40 border-top" id="starter-journey">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-heading tt-heading-xxlg margin-bottom-40">
                    <h3 class="tt-heading-subtitle tt-text-uppercase">Comment bien commencer</h3>
                    <h2 class="tt-heading-title">Un mini parcours premium pour comprendre les bases, activer son espace et savoir quoi faire ensuite.</h2>
                </div>
                <div class="erah-help-timeline">
                    @foreach (collect($page['starterJourney']['steps'] ?? [])->take(6) as $step)
                        <article class="erah-help-step-card">
                            <div class="erah-help-step-number">{{ str_pad((string) ($step['step_number'] ?? $loop->iteration), 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="tt-heading tt-heading-sm margin-bottom-15"><h3 class="tt-heading-title">{{ $step['title'] }}</h3></div>
                            <p class="text-muted">{{ $step['summary'] }}</p>
                            @if (! empty($step['visual_title']) || ! empty($step['visual_body']))
                                <div class="erah-help-note">
                                    @if (! empty($step['visual_title']))
                                        <strong>{{ $step['visual_title'] }}</strong>
                                    @endif
                                    @if (! empty($step['visual_body']))
                                        <div class="margin-top-10">{{ $step['visual_body'] }}</div>
                                    @endif
                                </div>
                            @endif
                            @if (! empty($step['cta_url']) && ! empty($step['cta_label']))
                                <div class="tt-btn-wrap margin-top-20">
                                    <a href="{{ $step['cta_url'] }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Suivre">{{ $step['cta_label'] }}</span></a>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-30 padding-bottom-lg-60 padding-bottom-40 border-top" id="understand-platform">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-heading tt-heading-xxlg margin-bottom-40">
                    <h3 class="tt-heading-subtitle tt-text-uppercase">Comprendre la plateforme</h3>
                    <h2 class="tt-heading-title">Les grandes logiques metier d ERAH, expliquees avec les vrais modules du produit.</h2>
                </div>
                <div class="erah-help-quick-grid">
                    @foreach ($page['featureHighlights'] as $item)
                        <article class="erah-help-card">
                            <div class="erah-help-badge">{{ $item['eyebrow'] ?? 'Module' }}</div>
                            <div class="tt-heading tt-heading-lg margin-bottom-12"><h3 class="tt-heading-title">{{ $item['title'] }}</h3></div>
                            <p class="text-muted">{{ $item['description'] }}</p>
                            @if (! empty($item['points']))
                                <ul class="erah-help-list">
                                    @foreach ($item['points'] as $point)
                                        <li>{{ $point }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @if (! empty($item['url']))
                                <div class="tt-btn-wrap margin-top-20">
                                    <a href="{{ $item['url'] }}" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Explorer">Voir le module</span></a>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        @if (! empty($page['quickQuestions']))
            <div class="tt-section padding-top-lg-30 padding-bottom-lg-60 padding-bottom-40 border-top">
                <div class="tt-section-inner tt-wrap">
                    <div class="tt-row">
                        <div class="tt-col-lg-4">
                            <div class="tt-heading tt-heading-xlg">
                                <h3 class="tt-heading-subtitle tt-text-uppercase">Questions rapides</h3>
                                <h2 class="tt-heading-title">Les questions qu un membre ou un visiteur pose le plus souvent en premier.</h2>
                            </div>
                            <p class="text-muted">Ce bloc sert d entree directe. La FAQ detaillee juste en dessous garde le contenu complet, les etapes et les reponses enrichies.</p>
                            <div class="tt-btn-wrap margin-top-25">
                                <a href="#faq-center" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="FAQ">Aller a la FAQ detaillee</span></a>
                            </div>
                        </div>
                        <div class="tt-col-lg-8">
                            <div class="erah-help-quick-grid">
                                @foreach ($page['quickQuestions'] as $question)
                                    <article class="erah-help-card">
                                        <div class="erah-help-badge">Question rapide</div>
                                        <div class="tt-heading tt-heading-sm margin-bottom-15"><h3 class="tt-heading-title">{{ $question['title'] }}</h3></div>
                                        <p class="text-muted">{{ $question['answer'] }}</p>
                                        <div class="tt-btn-wrap margin-top-20">
                                            <a href="{{ $question['href'] }}" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Lire">Voir la reponse</span></a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="tt-section padding-top-lg-30 padding-bottom-lg-60 padding-bottom-40 border-top" id="categories">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-heading tt-heading-xxlg margin-bottom-40">
                    <h3 class="tt-heading-subtitle tt-text-uppercase">Grandes categories</h3>
                    <h2 class="tt-heading-title">Choisir directement le bon sujet plutot que chercher au hasard dans toute la plateforme.</h2>
                </div>
                <div class="erah-help-category-grid">
                    @foreach ($page['categories'] as $category)
                        <article class="erah-help-card">
                            <div class="erah-help-overline">{{ $category['icon'] ?: 'Guide' }}</div>
                            <div class="tt-heading tt-heading-lg margin-bottom-12"><h3 class="tt-heading-title">{{ $category['title'] }}</h3></div>
                            <p class="text-muted">{{ $category['description'] }}</p>
                            <div class="erah-help-pill-row margin-top-18">
                                <span class="erah-help-chip">{{ $category['articles_count'] }} article(s)</span>
                                @if (! empty($category['landing_bucket']))
                                    <span class="erah-help-chip">{{ str_replace('_', ' ', $category['landing_bucket']) }}</span>
                                @endif
                            </div>
                            @if (! empty($category['articles_preview']))
                                <div class="erah-help-category-preview">
                                    @foreach ($category['articles_preview'] as $article)
                                        <div>{{ $article['title'] }}</div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="tt-btn-wrap margin-top-25">
                                <a href="{{ $category['url'] }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Explorer">Voir la categorie</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-30 padding-bottom-lg-60 padding-bottom-40 border-top" id="faq-center">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-heading tt-heading-xxlg margin-bottom-35">
                    <h3 class="tt-heading-subtitle tt-text-uppercase">FAQ detaillee</h3>
                    <h2 class="tt-heading-title">Une FAQ centrale riche qui explique les regles, les parcours, les modules et les incomprehensions frequentes.</h2>
                </div>
                <div class="erah-help-faq-toolbar">
                    <div class="erah-help-card">
                        <div class="erah-help-overline">Rechercher dans la FAQ</div>
                        <p class="text-muted margin-bottom-20">Cherchez un sujet, un module ou une action. Vous pouvez aussi filtrer par categorie juste a droite.</p>
                        <form method="GET" action="{{ $baseRoute }}">
                            @if (! empty($page['faq']['active_category']))
                                <input type="hidden" name="category" value="{{ $page['faq']['active_category'] }}">
                            @endif
                            <div class="tt-form-group">
                                <label>Question ou mot-cle</label>
                                <input type="text" name="search" value="{{ $search }}" placeholder="Exemple : missions, points, profil, matchs, cadeaux...">
                            </div>
                            <div class="erah-help-search-actions margin-top-25">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Chercher">Chercher</span></button>
                                <a href="{{ $baseRoute }}#faq-center" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Reset">Tout voir</span></a>
                            </div>
                        </form>
                    </div>
                    <div class="erah-help-card">
                        <div class="erah-help-overline">Filtrer par categorie</div>
                        <div class="tt-heading tt-heading-sm margin-bottom-15"><h3 class="tt-heading-title">Garder la FAQ utile meme quand elle grandit.</h3></div>
                        <div class="erah-help-faq-filters">
                            <a href="{{ $baseRoute }}#faq-center" class="{{ empty($page['faq']['active_category']) ? 'is-active' : '' }}">Toutes</a>
                            @foreach ($page['faq']['categories'] as $category)
                                <a href="{{ $baseRoute }}?category={{ $category['slug'] }}#faq-center" class="{{ ($page['faq']['active_category'] ?? null) === $category['slug'] ? 'is-active' : '' }}">{{ $category['title'] }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if (! empty($page['faq']['filtered_items']))
                    <div class="erah-help-faq-grid margin-top-30">
                        @foreach ($page['faq']['filtered_items'] as $article)
                            <article class="erah-help-faq-card {{ ($page['faq']['active_article'] ?? null) === $article['slug'] ? 'is-active' : '' }}" id="faq-{{ $article['slug'] }}">
                                <div class="erah-help-badge">{{ $article['category']['title'] ?? 'FAQ' }}</div>
                                <div class="tt-heading tt-heading-lg margin-bottom-12"><h3 class="tt-heading-title">{{ $article['title'] }}</h3></div>
                                @if (! empty($article['summary']))
                                    <p class="text-muted margin-bottom-18">{{ $article['summary'] }}</p>
                                @endif
                                <p>{{ $article['short_answer'] ?: $article['summary'] }}</p>

                                @if (! empty($article['support']['steps']))
                                    <ol class="erah-help-faq-steps">
                                        @foreach ($article['support']['steps'] as $step)
                                            <li>{{ $step }}</li>
                                        @endforeach
                                    </ol>
                                @endif

                                @if (! empty($article['support']['tips']))
                                    <div class="erah-help-note">
                                        <strong>Conseil utile</strong>
                                        <ul class="erah-help-list margin-top-15">
                                            @foreach ($article['support']['tips'] as $tip)
                                                <li>{{ $tip }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (! empty($article['support']['note']))
                                    <div class="erah-help-note">
                                        <strong>A retenir</strong>
                                        <div class="margin-top-10">{{ $article['support']['note'] }}</div>
                                    </div>
                                @endif

                                <div class="erah-help-search-actions margin-top-25">
                                    @if (! empty($article['cta_url']) && ! empty($article['cta_label']))
                                        <a href="{{ $article['cta_url'] }}" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Ouvrir">{{ $article['cta_label'] }}</span></a>
                                    @endif
                                    <a href="{{ $page['assistant']['page_url'] }}?article={{ $article['slug'] }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Assistant">Demander a l assistant</span></a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="erah-help-card margin-top-30">
                        <div class="tt-heading tt-heading-sm margin-bottom-15"><h3 class="tt-heading-title">Aucune reponse sur ce filtre</h3></div>
                        <p class="text-muted margin-bottom-20">Essayez une recherche plus large, revenez a toutes les categories ou basculez vers l assistant dedie pour formuler la question librement.</p>
                        <div class="erah-help-search-actions">
                            <a href="{{ $baseRoute }}#faq-center" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="FAQ">Voir toute la FAQ</span></a>
                            <a href="{{ $page['assistant']['page_url'] }}" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Assistant">Ouvrir l assistant</span></a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="tt-section padding-top-lg-30 padding-bottom-lg-60 padding-bottom-40 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="erah-help-video-grid">
                    <div class="erah-help-card">
                        <div class="erah-help-overline">Bloc video</div>
                        <div class="tt-heading tt-heading-xlg margin-bottom-15"><h2 class="tt-heading-title">{{ $page['video']['title'] }}</h2></div>
                        <p class="text-muted">{{ $page['video']['description'] }}</p>
                        <ul class="erah-help-list">
                            @foreach ($page['video']['highlights'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="erah-help-card">
                        <div class="erah-help-video-frame">
                            @if (! empty($page['video']['embed_url']))
                                <iframe src="{{ $page['video']['embed_url'] }}" allowfullscreen loading="lazy" title="Tutoriel ERAH"></iframe>
                            @else
                                <div class="erah-help-video-empty">
                                    <div class="erah-help-overline">Video a brancher</div>
                                    <div class="tt-heading tt-heading-sm margin-bottom-10"><h3 class="tt-heading-title">Ajoutez un tutoriel video quand il sera pret.</h3></div>
                                    <p class="text-muted margin-bottom-0">{{ $page['video']['fallback'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-40 padding-bottom-lg-100 padding-bottom-80 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="erah-help-assistant-grid margin-bottom-40">
                    <div class="erah-help-card">
                        <div class="erah-help-overline">Assistant IA dedie</div>
                        <div class="tt-heading tt-heading-xxlg margin-bottom-15"><h2 class="tt-heading-title">{{ $page['assistant']['title'] }}</h2></div>
                        <p class="text-muted margin-bottom-0">{{ $page['assistant']['description'] }}</p>
                    </div>
                    <div class="erah-help-assistant-panel">
                        <div class="erah-help-overline">Exemples de questions</div>
                        <div class="erah-help-assistant-prompt">
                            @foreach (collect($page['assistant']['suggested_prompts'] ?? [])->take(3) as $prompt)
                                <div>{{ $prompt }}</div>
                            @endforeach
                        </div>
                        <div class="tt-btn-wrap margin-top-25">
                            <a href="{{ $page['assistant']['page_url'] }}" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Assistant">Ouvrir le chatbot</span></a>
                        </div>
                    </div>
                </div>

                <div class="erah-help-footer-grid">
                    <div class="erah-help-card">
                        <div class="erah-help-overline">Tu es nouveau ?</div>
                        <div class="tt-heading tt-heading-xxlg margin-bottom-15"><h2 class="tt-heading-title">{{ $page['footerCta']['title'] ?? 'Pret a participer vraiment a la plateforme ?' }}</h2></div>
                        <p class="text-muted margin-bottom-30">{{ $page['footerCta']['description'] ?? '' }}</p>
                        <div class="erah-help-search-actions">
                            <a href="{{ $page['footerCta']['login_url'] ?? route('login') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Connexion">Connexion</span></a>
                            <a href="{{ $page['footerCta']['register_url'] ?? route('register') }}" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Inscription">Creer un compte</span></a>
                            <a href="{{ $page['assistant']['page_url'] }}" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Assistant">Poser une question</span></a>
                        </div>
                    </div>
                    <div class="erah-help-footer-panel">
                        <div class="erah-help-overline">Base de connaissance</div>
                        <div class="tt-heading tt-heading-lg margin-bottom-15"><h2 class="tt-heading-title">Une seule source de verite pour la FAQ et l assistant</h2></div>
                        <p class="text-muted">Articles, questions, reponses courtes, glossaire et suggestions d actions sont deja structures pour servir de base au futur assistant ERAH.</p>
                        <div class="erah-help-pill-row margin-top-25">
                            <span class="erah-help-chip">Categories administrables</span>
                            <span class="erah-help-chip">Questions mises en avant</span>
                            <span class="erah-help-chip">Reponses courtes IA</span>
                            <span class="erah-help-chip">{{ $page['assistant']['status'] ?? 'Pret' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('marketing.partials.theme-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var transition = document.getElementById('tt-page-transition');
            if (transition) {
                transition.remove();
            }
            document.body.classList.remove('tt-transition');
        });
    </script>
@endsection
