@extends('marketing.layouts.template')

@section('title', ($mode === 'console' ? 'Assistant Console' : 'Assistant ERAH').' | ERAH Esport')
@section('meta_description', "Assistant d'aide ERAH")
@section('meta_keywords', 'ERAH assistant, aide ERAH, FAQ ERAH, support plateforme')
@section('body_class', 'tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page_styles')
    <style>
        #tt-page-transition { display: none !important; }
        .erah-assistant-wrap { position: relative; }
        .erah-assistant-overlap { position: relative; z-index: 4; margin-top: -92px; }
        .erah-assistant-shell,
        .erah-assistant-panel,
        .erah-assistant-thread,
        .erah-assistant-context,
        .erah-assistant-composer {
            position: relative;
            overflow: hidden;
            padding: 32px;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 28px;
            background: linear-gradient(145deg, rgba(255,255,255,.045), rgba(255,255,255,.018));
            box-shadow: 0 20px 60px rgba(0,0,0,.18);
        }
        .erah-assistant-shell::before,
        .erah-assistant-panel::before,
        .erah-assistant-thread::before,
        .erah-assistant-context::before,
        .erah-assistant-composer::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(216,7,7,.16), transparent 46%);
            pointer-events: none;
        }
        .erah-assistant-grid {
            display: grid;
            gap: 26px;
            grid-template-columns: minmax(320px, .85fr) minmax(0, 1.15fr);
            align-items: start;
        }
        .erah-assistant-overline {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            color: rgba(255,255,255,.58);
            font-size: 12px;
            letter-spacing: .22em;
            text-transform: uppercase;
        }
        .erah-assistant-overline::before {
            content: "";
            width: 28px;
            height: 1px;
            background: rgba(216,7,7,.85);
        }
        .erah-assistant-pills,
        .erah-assistant-actions,
        .erah-assistant-next {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .erah-assistant-suggestion {
            display: grid;
            gap: 14px;
            margin-top: 4px;
        }
        .erah-assistant-chip,
        .erah-assistant-suggestion button {
            display: inline-flex;
            align-items: center;
            justify-self: start;
            min-height: 48px;
            max-width: 100%;
            padding: 14px 22px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.12);
            background:
                radial-gradient(circle at top left, rgba(216,7,7,.08), transparent 48%),
                rgba(255,255,255,.03);
            color: rgba(255,255,255,.82);
            font-size: 12px;
            letter-spacing: .12em;
            line-height: 1.45;
            text-align: left;
            text-transform: uppercase;
            transition: border-color .24s ease, background-color .24s ease, color .24s ease, transform .24s ease;
        }
        .erah-assistant-suggestion button:hover {
            border-color: rgba(216,7,7,.55);
            background:
                radial-gradient(circle at top left, rgba(216,7,7,.12), transparent 48%),
                rgba(255,255,255,.05);
            color: #fff;
            transform: translateX(3px);
        }
        .erah-assistant-suggestion button:disabled {
            cursor: wait;
            opacity: .45;
        }
        .erah-assistant-thread { min-height: 640px; display: flex; flex-direction: column; }
        .erah-assistant-messages { display: grid; gap: 16px; flex: 1; align-content: start; }
        .erah-assistant-message {
            padding: 20px 22px;
            border-radius: 22px;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.03);
        }
        .erah-assistant-message--user { background: linear-gradient(135deg, rgba(216,7,7,.18), rgba(255,255,255,.03)); }
        .erah-assistant-message-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 12px;
            color: rgba(255,255,255,.6);
            font-size: 11px;
            letter-spacing: .18em;
            text-transform: uppercase;
        }
        .erah-assistant-message p:last-child { margin-bottom: 0; }
        .erah-assistant-composer {
            padding: 30px;
            border-color: rgba(255,255,255,.12);
            background:
                radial-gradient(circle at top right, rgba(216,7,7,.16), transparent 38%),
                linear-gradient(180deg, rgba(9,10,15,.98), rgba(255,255,255,.03));
            box-shadow:
                0 26px 70px rgba(0,0,0,.24),
                inset 0 1px 0 rgba(255,255,255,.03);
        }
        .erah-assistant-form {
            display: grid;
            gap: 22px;
        }
        .erah-assistant-form[aria-busy="true"] {
            pointer-events: none;
        }
        .erah-assistant-form-header {
            display: grid;
            gap: 10px;
            max-width: 720px;
        }
        .erah-assistant-form-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,.54);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .22em;
            text-transform: uppercase;
        }
        .erah-assistant-form-kicker::before {
            content: "";
            width: 26px;
            height: 1px;
            background: rgba(216,7,7,.9);
        }
        .erah-assistant-form-lead {
            margin: 0;
            color: rgba(255,255,255,.68);
            font-size: 15px;
            line-height: 1.85;
        }
        .erah-assistant-form-group {
            position: relative;
            margin: 0;
            display: grid;
            gap: 14px;
            padding: 26px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,.1);
            background:
                linear-gradient(180deg, rgba(255,255,255,.035), rgba(6,7,11,.94));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.03),
                0 18px 42px rgba(0,0,0,.18);
            transition:
                border-color .28s ease,
                box-shadow .28s ease,
                background .28s ease,
                transform .28s ease;
        }
        .erah-assistant-form-group::after {
            content: "";
            position: absolute;
            inset: 1px;
            border-radius: 23px;
            background: radial-gradient(circle at top right, rgba(216,7,7,.1), transparent 32%);
            opacity: .85;
            pointer-events: none;
        }
        .erah-assistant-form-group:focus-within {
            border-color: rgba(216,7,7,.44);
            box-shadow:
                0 0 0 1px rgba(216,7,7,.18),
                0 28px 65px rgba(216,7,7,.12),
                0 18px 42px rgba(0,0,0,.22);
            transform: translateY(-1px);
        }
        .erah-assistant-form-group > * {
            position: relative;
            z-index: 1;
        }
        .erah-assistant-form-index {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.03);
            color: rgba(255,255,255,.62);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
        }
        .erah-assistant-field-copy {
            display: grid;
            gap: 8px;
            max-width: 740px;
        }
        .erah-assistant-field-label {
            margin: 0;
            color: #fff;
            font-size: 21px;
            font-weight: 600;
            letter-spacing: -.02em;
            line-height: 1.2;
        }
        .erah-assistant-field-help {
            margin: 0;
            color: rgba(255,255,255,.58);
            font-size: 14px;
            line-height: 1.75;
        }
        .erah-assistant-input-wrap {
            position: relative;
            border-radius: 22px;
            border: 1px solid rgba(255,255,255,.1);
            background:
                linear-gradient(180deg, rgba(2,3,7,.9), rgba(255,255,255,.03));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.03),
                0 16px 34px rgba(0,0,0,.18);
            transition:
                border-color .28s ease,
                box-shadow .28s ease,
                background .28s ease;
        }
        .erah-assistant-form-group:focus-within .erah-assistant-input-wrap {
            border-color: rgba(216,7,7,.32);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.05),
                0 18px 42px rgba(216,7,7,.09);
            background:
                linear-gradient(180deg, rgba(4,5,9,.96), rgba(255,255,255,.04));
        }
        .erah-assistant-input,
        .erah-assistant-input.tt-form-control {
            display: block;
            width: 100%;
            min-height: 176px;
            margin: 0;
            padding: 20px 22px;
            border: 0;
            border-radius: 22px;
            background: transparent;
            color: #fff;
            font-family: inherit;
            font-size: 17px;
            font-weight: 500;
            line-height: 1.75;
            letter-spacing: .01em;
            resize: vertical;
            appearance: none;
            -webkit-appearance: none;
            box-shadow: none;
        }
        .erah-assistant-input::placeholder {
            color: rgba(255,255,255,.3);
            opacity: 1;
        }
        .erah-assistant-input:hover {
            color: #fff;
        }
        .erah-assistant-input:focus {
            outline: none;
            box-shadow: none;
        }
        .erah-assistant-input:disabled {
            cursor: wait;
            opacity: .6;
        }
        .erah-assistant-actions {
            align-items: stretch;
            gap: 14px;
        }
        .erah-assistant-btn.tt-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 58px;
            padding: 0 28px;
            border-radius: 18px;
            text-align: center;
            transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease, opacity .24s ease;
        }
        .erah-assistant-btn.tt-btn:hover {
            transform: translateY(-1px);
        }
        .erah-assistant-btn.tt-btn-primary {
            box-shadow: 0 20px 38px rgba(216,7,7,.22);
        }
        .erah-assistant-btn.tt-btn-outline {
            background: rgba(255,255,255,.02);
            border-color: rgba(255,255,255,.16);
        }
        .erah-assistant-btn[disabled],
        .erah-assistant-btn.is-disabled {
            cursor: not-allowed;
            opacity: .52;
            pointer-events: none;
            transform: none;
            box-shadow: none;
        }
        .erah-assistant-btn-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
        }
        .erah-assistant-form-status {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,.42);
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .erah-assistant-form-status::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,.35);
            transition: background-color .24s ease, box-shadow .24s ease;
        }
        .erah-assistant-form.is-loading .erah-assistant-form-status::before {
            background: #d80707;
            box-shadow: 0 0 0 8px rgba(216,7,7,.12);
        }
        .erah-assistant-context-list {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }
        .erah-assistant-context-item {
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.03);
        }
        .erah-assistant-source {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .erah-assistant-response-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .erah-assistant-favorite-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.03);
            color: rgba(255,255,255,.82);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            transition: border-color .24s ease, background-color .24s ease, color .24s ease, transform .24s ease;
        }
        .erah-assistant-favorite-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(255,214,78,.38);
            color: #fff1b8;
        }
        .erah-assistant-favorite-btn.is-saving,
        .erah-assistant-favorite-btn:disabled {
            cursor: wait;
            opacity: .7;
            transform: none;
        }
        .erah-assistant-favorite-btn.is-saved {
            border-color: rgba(255,214,78,.42);
            background: rgba(255,214,78,.1);
            color: #ffe7a7;
        }
        .erah-assistant-favorite-icon {
            font-size: 16px;
            line-height: 1;
        }
        .erah-assistant-favorite-link {
            color: rgba(255,255,255,.62);
            font-size: 13px;
        }
        .erah-assistant-favorite-link:hover {
            color: #fff;
        }
        .erah-assistant-loader {
            display: none;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,.72);
        }
        .erah-assistant-loader.is-active { display: inline-flex; }
        .erah-assistant-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d80707;
            animation: erahAssistantPulse 1.2s infinite ease-in-out;
        }
        .erah-assistant-dot:nth-child(2) { animation-delay: .15s; }
        .erah-assistant-dot:nth-child(3) { animation-delay: .3s; }
        @keyframes erahAssistantPulse {
            0%, 80%, 100% { transform: scale(.5); opacity: .35; }
            40% { transform: scale(1); opacity: 1; }
        }
        @media (max-width: 1199.98px) {
            .erah-assistant-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 767.98px) {
            .erah-assistant-overlap { margin-top: -38px; }
            .erah-assistant-shell,
            .erah-assistant-panel,
            .erah-assistant-thread,
            .erah-assistant-context,
            .erah-assistant-composer { padding: 24px; border-radius: 24px; }
            .erah-assistant-thread { min-height: auto; }
            .erah-assistant-form-group { padding: 20px; border-radius: 20px; }
            .erah-assistant-input,
            .erah-assistant-input.tt-form-control {
                min-height: 152px;
                padding: 18px 18px 20px;
                font-size: 16px;
            }
            .erah-assistant-actions {
                flex-direction: column;
            }
            .erah-assistant-suggestion button {
                width: 100%;
                justify-self: stretch;
            }
            .erah-assistant-btn.tt-btn {
                width: 100%;
            }
            .erah-assistant-field-label {
                font-size: 19px;
            }
        }
    </style>
@endsection

@section('content')
    @php($activeArticle = collect($page['faq']['items'] ?? [])->firstWhere('slug', $page['filters']['article'] ?? null))
    <div id="page-header" class="ph-full ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">{{ $mode === 'console' ? 'Assistant in-app' : 'Assistant ERAH' }}</h2>
                    <h1 class="ph-caption-title">Posez une question a la plateforme.</h1>
                    <div class="ph-caption-description max-width-700">Un espace dedie pour interroger la base de connaissance ERAH, comprendre les mecanismes du produit et obtenir une reponse fiable avant d agir.</div>
                </div>
            </div>
        </div>
        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">{{ $mode === 'console' ? 'Support contextuel' : 'Knowledge base guidee' }}</h2>
                        <h1 class="ph-caption-title">Posez une question a la plateforme.</h1>
                        <div class="ph-caption-description max-width-700">L assistant ne devine pas. Il s appuie sur les articles, la FAQ, le glossaire et, si vous etes connecte, sur quelques donnees utiles de votre compte.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tt-scroll-down">
            <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
                <div class="tt-scrd-icon"></div>
                <svg viewBox="0 0 500 500">
                    <defs><path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="assistantcircle"></path></defs>
                    <text dy="30"><textPath xlink:href="#assistantcircle">ERAH Assistant - ERAH Assistant -</textPath></text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content" class="erah-assistant-wrap">
        <div class="tt-section no-padding-top padding-bottom-lg-100 padding-bottom-80">
            <div class="tt-section-inner tt-wrap">
                <div class="erah-assistant-overlap">
                    <div class="erah-assistant-grid">
                        <div>
                            <div class="erah-assistant-shell margin-bottom-25">
                                <div class="erah-assistant-overline">Comment il fonctionne</div>
                                <div class="tt-heading tt-heading-xlg margin-bottom-15"><h2 class="tt-heading-title">{{ $page['assistant']['title'] }}</h2></div>
                                <p class="text-muted">{{ $page['assistant']['description'] }}</p>
                                <div class="erah-assistant-pills margin-top-25">
                                    <span class="erah-assistant-chip">{{ $page['assistant']['status'] ?? 'Knowledge base' }}</span>
                                    <span class="erah-assistant-chip">{{ $page['overview']['faqs'] ?? 0 }} questions reelles</span>
                                    <span class="erah-assistant-chip">{{ $page['overview']['glossary'] ?? 0 }} termes utiles</span>
                                </div>
                                <div class="tt-btn-wrap margin-top-25">
                                    <a href="{{ $mode === 'console' ? route('console.help') : route('help.index') }}#faq-center" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="FAQ">Retour a la FAQ</span></a>
                                </div>
                            </div>

                            @if (! empty($page['assistant']['user_preview']))
                                <div class="erah-assistant-context margin-bottom-25">
                                    <div class="erah-assistant-overline">Contexte membre</div>
                                    <div class="tt-heading tt-heading-sm margin-bottom-12"><h3 class="tt-heading-title">{{ $page['assistant']['user_preview']['name'] }}</h3></div>
                                    <p class="text-muted">L assistant peut repondre en tenant compte de votre situation visible: ligue, points, XP, portefeuille et pistes d amelioration.</p>
                                    <div class="erah-assistant-context-list">
                                        <div class="erah-assistant-context-item">Ligue : {{ $page['assistant']['user_preview']['league'] }}</div>
                                        <div class="erah-assistant-context-item">Points : {{ $page['assistant']['user_preview']['points'] }}</div>
                                        <div class="erah-assistant-context-item">XP : {{ $page['assistant']['user_preview']['xp'] }}</div>
                                        <div class="erah-assistant-context-item">Portefeuille : {{ $page['assistant']['user_preview']['reward_balance'] }}</div>
                                    </div>
                                </div>
                            @endif

                            @if (! empty($activeArticle))
                                <div class="erah-assistant-panel margin-bottom-25">
                                    <div class="erah-assistant-overline">Question mise en contexte</div>
                                    <div class="tt-heading tt-heading-sm margin-bottom-12"><h3 class="tt-heading-title">{{ $activeArticle['title'] }}</h3></div>
                                    <p class="text-muted">{{ $activeArticle['short_answer'] ?: $activeArticle['summary'] }}</p>
                                </div>
                            @endif

                            <div class="erah-assistant-panel">
                                <div class="erah-assistant-overline">Questions suggerees</div>
                                <div class="erah-assistant-suggestion">
                                    @foreach ($page['assistant']['suggested_prompts'] ?? [] as $prompt)
                                        <button type="button" data-help-prompt="{{ $prompt }}">{{ $prompt }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="erah-assistant-thread">
                            <div class="erah-assistant-overline">Conversation</div>
                            <div class="tt-heading tt-heading-lg margin-bottom-18"><h2 class="tt-heading-title">Une interface dediee pour comprendre la plateforme sans improviser.</h2></div>
                            <div class="erah-assistant-composer margin-top-25">
                                <form id="erah-assistant-form" class="erah-assistant-form tt-form" novalidate aria-busy="false">
                                    <div class="erah-assistant-form-header">
                                        <div class="erah-assistant-form-kicker">Question libre</div>
                                        <div class="tt-heading tt-heading-sm margin-bottom-0">
                                            <h3 class="tt-heading-title">Expliquez clairement ce que vous voulez comprendre.</h3>
                                        </div>
                                        <p class="erah-assistant-form-lead">
                                            Demandez une explication sur les points, les missions, les matchs, les paris, le profil, les cadeaux ou la navigation. Plus votre question est concrete, plus la reponse sera utile.
                                        </p>
                                    </div>

                                    <div class="tt-form-group erah-assistant-form-group">
                                        <div class="erah-assistant-form-index" aria-hidden="true">01</div>
                                        <div class="erah-assistant-field-copy">
                                            <label for="erah-assistant-input" class="erah-assistant-field-label">Votre question</label>
                                            <p class="erah-assistant-field-help" id="erah-assistant-input-help">
                                                Decrivez le contexte, le blocage ou l action que vous cherchez. L assistant s appuie ensuite sur la base de connaissance ERAH pour rester fiable.
                                            </p>
                                        </div>
                                        <div class="erah-assistant-input-wrap">
                                            <textarea
                                                id="erah-assistant-input"
                                                class="tt-form-control erah-assistant-input"
                                                rows="6"
                                                aria-describedby="erah-assistant-input-help"
                                                placeholder="{{ $page['assistant']['placeholder'] }}"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <div class="erah-assistant-form-status" id="erah-assistant-form-status">
                                        Reponse guidee par la FAQ, les articles et le glossaire ERAH.
                                    </div>

                                    <div class="erah-assistant-actions">
                                        <button type="submit" id="erah-assistant-submit" class="tt-btn tt-btn-primary tt-magnetic-item erah-assistant-btn">
                                            <span class="erah-assistant-btn-label" data-hover="Envoyer la question" data-default-label="Envoyer la question" data-loading-label="Envoi en cours...">Envoyer la question</span>
                                        </button>
                                        <a href="{{ $mode === 'console' ? route('console.help') : route('help.index') }}#faq-center" class="tt-btn tt-btn-outline tt-magnetic-item erah-assistant-btn">
                                            <span data-hover="Revenir a la FAQ">Revenir a la FAQ</span>
                                        </a>
                                    </div>
                                </form>
                            </div>
                            <div class="erah-assistant-loader margin-top-20" id="erah-assistant-loader">
                                <span class="erah-assistant-dot"></span>
                                <span class="erah-assistant-dot"></span>
                                <span class="erah-assistant-dot"></span>
                                <span>Recherche d une reponse fiable...</span>
                            </div>
                            <div class="erah-assistant-messages margin-top-25" id="erah-assistant-messages">
                                <div class="erah-assistant-message">
                                    <div class="erah-assistant-message-head"><span>Assistant ERAH</span><span>Base de connaissance</span></div>
                                    <p>Bonjour. Je peux vous aider sur les matchs, paris, points, missions, clips, profil, cadeaux, notifications, duels et navigation sur la plateforme.</p>
                                    <p class="margin-top-15">Posez une question simple ou utilisez une suggestion. Si vous etes connecte, je peux aussi contextualiser ma reponse avec vos donnees visibles.</p>
                                </div>
                            </div>
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

            var form = document.getElementById('erah-assistant-form');
            var input = document.getElementById('erah-assistant-input');
            var messages = document.getElementById('erah-assistant-messages');
            var loader = document.getElementById('erah-assistant-loader');
            var token = document.querySelector('meta[name="csrf-token"]');
            var submitButton = document.getElementById('erah-assistant-submit');
            var submitLabel = submitButton ? submitButton.querySelector('.erah-assistant-btn-label') : null;
            var promptButtons = Array.prototype.slice.call(document.querySelectorAll('[data-help-prompt]'));
            var canSaveFavorites = @json(auth()->check());
            var favoriteStoreUrl = @json(auth()->check() ? route('assistant.favorites.store') : null);
            var favoriteProfileUrl = @json(auth()->check() ? route('profile.show').'#assistant-favorites' : null);
            var loginUrl = @json(route('login'));
            var isLoading = false;

            if (!form || !input || !messages || !loader || !token || !submitButton || !submitLabel) {
                return;
            }

            var syncComposerState = function () {
                var disabled = isLoading || !input.value.trim();
                form.classList.toggle('is-loading', isLoading);
                form.setAttribute('aria-busy', isLoading ? 'true' : 'false');
                submitButton.disabled = disabled;
                submitButton.classList.toggle('is-disabled', disabled);
                submitLabel.textContent = isLoading
                    ? (submitLabel.getAttribute('data-loading-label') || 'Envoi en cours...')
                    : (submitLabel.getAttribute('data-default-label') || 'Envoyer la question');

                promptButtons.forEach(function (button) {
                    button.disabled = isLoading;
                });
            };

            var appendMessage = function (role, label, content, extraHtml) {
                var wrapper = document.createElement('div');
                wrapper.className = 'erah-assistant-message' + (role === 'user' ? ' erah-assistant-message--user' : '');
                wrapper.innerHTML = '<div class="erah-assistant-message-head"><span>' + label + '</span><span>' + (role === 'user' ? 'Question' : 'Reponse') + '</span></div><div>' + content + '</div>' + (extraHtml || '');
                messages.appendChild(wrapper);
                requestAnimationFrame(function () {
                    wrapper.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                    });
                });

                return wrapper;
            };

            var resetConversation = function () {
                messages.innerHTML = '';
            };

            var escapeHtml = function (value) {
                return value
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            var buildFavoriteAction = function () {
                if (canSaveFavorites && favoriteStoreUrl) {
                    return '<div class="erah-assistant-response-actions"><button type="button" class="erah-assistant-favorite-btn" data-favorite-answer><span class="erah-assistant-favorite-icon" data-favorite-icon aria-hidden="true">☆</span><span data-favorite-label>Ajouter aux favoris</span></button><a href="' + favoriteProfileUrl + '" class="erah-assistant-favorite-link" data-favorite-profile hidden>Voir dans mon profil</a></div>';
                }

                return '<div class="erah-assistant-response-actions"><a href="' + loginUrl + '" class="erah-assistant-favorite-link">Connectez-vous pour enregistrer cette reponse</a></div>';
            };

            var setFavoriteButtonState = function (button, state) {
                var icon = button.querySelector('[data-favorite-icon]');
                var label = button.querySelector('[data-favorite-label]');

                button.classList.remove('is-saving', 'is-saved');
                button.disabled = false;

                if (state === 'saving') {
                    button.classList.add('is-saving');
                    button.disabled = true;
                    if (icon) icon.textContent = '✦';
                    if (label) label.textContent = 'Enregistrement...';

                    return;
                }

                if (state === 'saved') {
                    button.classList.add('is-saved');
                    button.disabled = true;
                    if (icon) icon.textContent = '★';
                    if (label) label.textContent = 'En favoris';

                    return;
                }

                if (icon) icon.textContent = '☆';
                if (label) label.textContent = 'Ajouter aux favoris';
            };

            var bindFavoriteButton = function (wrapper, question, data) {
                if (!canSaveFavorites || !favoriteStoreUrl || !wrapper) {
                    return;
                }

                var button = wrapper.querySelector('[data-favorite-answer]');
                var profileLink = wrapper.querySelector('[data-favorite-profile]');

                if (!button) {
                    return;
                }

                button.addEventListener('click', function () {
                    setFavoriteButtonState(button, 'saving');

                    fetch(favoriteStoreUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token.getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            question: question,
                            answer: data.answer || '',
                            details: Array.isArray(data.details) ? data.details : [],
                            sources: Array.isArray(data.sources) ? data.sources : [],
                            next_steps: Array.isArray(data.next_steps) ? data.next_steps : [],
                        }),
                    })
                        .then(function (response) {
                            if (response.ok) {
                                return response.json();
                            }

                            return response.json()
                                .catch(function () { return {}; })
                                .then(function (payload) {
                                    throw new Error(payload.message || 'Impossible d enregistrer cette reponse dans vos favoris.');
                                });
                        })
                        .then(function () {
                            setFavoriteButtonState(button, 'saved');

                            if (profileLink) {
                                profileLink.hidden = false;
                            }
                        })
                        .catch(function (error) {
                            setFavoriteButtonState(button, 'default');
                            alert(error.message || 'Impossible d enregistrer cette reponse dans vos favoris.');
                        });
                });
            };

            var ask = function (question) {
                if (isLoading || !question.trim()) {
                    return;
                }

                isLoading = true;
                syncComposerState();
                resetConversation();
                appendMessage('user', 'Vous', '<p>' + escapeHtml(question) + '</p>');
                input.value = '';
                input.blur();
                input.disabled = true;
                loader.classList.add('is-active');

                fetch(@json(route('help.assistant.ask')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token.getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ message: question }),
                })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        var data = payload.data || {};
                        var extra = '';

                        if (Array.isArray(data.sources) && data.sources.length) {
                            extra += '<div class="erah-assistant-source"><strong>Source</strong><div class="margin-top-10">';
                            data.sources.forEach(function (source) {
                                extra += '<div><a href="' + source.url + '">' + escapeHtml(source.title) + '</a></div>';
                            });
                            extra += '</div></div>';
                        }

                        if (Array.isArray(data.next_steps) && data.next_steps.length) {
                            extra += '<div class="erah-assistant-source"><strong>Et ensuite</strong><div class="erah-assistant-next margin-top-10">';
                            data.next_steps.forEach(function (step) {
                                extra += '<span class="erah-assistant-chip">' + escapeHtml(step) + '</span>';
                            });
                            extra += '</div></div>';
                        }

                        extra += buildFavoriteAction();

                        var assistantMessage = appendMessage('assistant', 'Assistant ERAH', '<p>' + escapeHtml(data.answer || 'Aucune reponse disponible pour le moment.') + '</p>', extra);
                        bindFavoriteButton(assistantMessage, question, data);
                    })
                    .catch(function () {
                        appendMessage('assistant', 'Assistant ERAH', '<p>Le service d assistance est temporairement indisponible. Revenez a la FAQ ou reessayez dans un instant.</p>');
                    })
                    .finally(function () {
                        isLoading = false;
                        input.disabled = false;
                        loader.classList.remove('is-active');
                        syncComposerState();
                        input.focus();
                    });
            };

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                ask(input.value);
            });

            input.addEventListener('input', syncComposerState);
            syncComposerState();

            document.querySelectorAll('[data-help-prompt]').forEach(function (button) {
                button.addEventListener('click', function () {
                    ask(button.getAttribute('data-help-prompt') || '');
                });
            });
        });
    </script>
@endsection
