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
        .erah-assistant-chip,
        .erah-assistant-suggestion button {
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            padding: 0 18px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.03);
            color: rgba(255,255,255,.82);
            font-size: 12px;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        .erah-assistant-suggestion button:hover { border-color: rgba(216,7,7,.55); color: #fff; }
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
        .erah-assistant-composer textarea {
            min-height: 140px;
            resize: vertical;
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
                                    <p class="text-muted">L assistant peut repondre en tenant compte de votre situation visible: ligue, points, XP, reward wallet et pistes d amelioration.</p>
                                    <div class="erah-assistant-context-list">
                                        <div class="erah-assistant-context-item">Ligue : {{ $page['assistant']['user_preview']['league'] }}</div>
                                        <div class="erah-assistant-context-item">Points : {{ $page['assistant']['user_preview']['points'] }}</div>
                                        <div class="erah-assistant-context-item">XP : {{ $page['assistant']['user_preview']['xp'] }}</div>
                                        <div class="erah-assistant-context-item">Reward wallet : {{ $page['assistant']['user_preview']['reward_balance'] }}</div>
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
                            <div class="erah-assistant-messages" id="erah-assistant-messages">
                                <div class="erah-assistant-message">
                                    <div class="erah-assistant-message-head"><span>Assistant ERAH</span><span>Base de connaissance</span></div>
                                    <p>Bonjour. Je peux vous aider sur les matchs, paris, points, missions, clips, profil, cadeaux, notifications, duels et navigation sur la plateforme.</p>
                                    <p class="margin-top-15">Posez une question simple ou utilisez une suggestion. Si vous etes connecte, je peux aussi contextualiser ma reponse avec vos donnees visibles.</p>
                                </div>
                            </div>
                            <div class="erah-assistant-loader margin-top-20" id="erah-assistant-loader">
                                <span class="erah-assistant-dot"></span>
                                <span class="erah-assistant-dot"></span>
                                <span class="erah-assistant-dot"></span>
                                <span>Recherche d une reponse fiable...</span>
                            </div>
                            <div class="erah-assistant-composer margin-top-25">
                                <form id="erah-assistant-form">
                                    <div class="tt-form-group">
                                        <label>Votre question</label>
                                        <textarea id="erah-assistant-input" placeholder="{{ $page['assistant']['placeholder'] }}"></textarea>
                                    </div>
                                    <div class="erah-assistant-actions margin-top-20">
                                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Envoyer">Envoyer la question</span></button>
                                        <a href="{{ $mode === 'console' ? route('console.help') : route('help.index') }}#faq-center" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="FAQ">Revenir a la FAQ</span></a>
                                    </div>
                                </form>
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

            if (!form || !input || !messages || !loader || !token) {
                return;
            }

            var appendMessage = function (role, label, content, extraHtml) {
                var wrapper = document.createElement('div');
                wrapper.className = 'erah-assistant-message' + (role === 'user' ? ' erah-assistant-message--user' : '');
                wrapper.innerHTML = '<div class="erah-assistant-message-head"><span>' + label + '</span><span>' + (role === 'user' ? 'Question' : 'Reponse') + '</span></div><div>' + content + '</div>' + (extraHtml || '');
                messages.appendChild(wrapper);
                messages.scrollTop = messages.scrollHeight;
            };

            var escapeHtml = function (value) {
                return value
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            var ask = function (question) {
                if (!question.trim()) {
                    return;
                }

                appendMessage('user', 'Vous', '<p>' + escapeHtml(question) + '</p>');
                input.value = '';
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

                        appendMessage('assistant', 'Assistant ERAH', '<p>' + escapeHtml(data.answer || 'Aucune reponse disponible pour le moment.') + '</p>', extra);
                    })
                    .catch(function () {
                        appendMessage('assistant', 'Assistant ERAH', '<p>Le service d assistance est temporairement indisponible. Revenez a la FAQ ou reessayez dans un instant.</p>');
                    })
                    .finally(function () {
                        loader.classList.remove('is-active');
                    });
            };

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                ask(input.value);
            });

            document.querySelectorAll('[data-help-prompt]').forEach(function (button) {
                button.addEventListener('click', function () {
                    ask(button.getAttribute('data-help-prompt') || '');
                });
            });
        });
    </script>
@endsection
