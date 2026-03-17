@extends('marketing.layouts.template')

@section('title', 'Preferences notifications | ERAH Plateforme')
@section('meta_description', 'Configuration des canaux et categories de notifications ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .pref-top-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .pref-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .pref-kpi-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 14px 16px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
        }

        .pref-kpi-card strong {
            display: block;
            font-size: 32px;
            line-height: 1;
            margin-bottom: 6px;
        }

        .pref-kpi-card span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: rgba(255, 255, 255, .72);
        }

        .pref-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 16px;
            background: rgba(255, 255, 255, .02);
        }

        .pref-quick-grid {
            display: grid;
            grid-template-columns: 1.2fr .9fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .pref-quick-panel,
        .pref-preset-panel {
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 12px;
            padding: 16px;
            background: rgba(0, 0, 0, .14);
        }

        .pref-section-title {
            margin: 0 0 6px;
            font-size: 18px;
            line-height: 1.2;
        }

        .pref-section-copy {
            margin: 0 0 14px;
            color: rgba(255, 255, 255, .68);
            font-size: 13px;
        }

        .pref-actions-toolbar,
        .pref-preset-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pref-action-btn,
        .pref-preset-btn {
            min-height: 42px;
        }

        .pref-preset-btn.is-active {
            border-color: rgba(80, 211, 147, .6);
            box-shadow: 0 0 0 1px rgba(80, 211, 147, .25) inset;
        }

        .pref-preset-btn[data-recommended="true"]::after {
            content: 'Recommande';
            display: inline-flex;
            align-items: center;
            margin-left: 8px;
            padding: 2px 7px;
            border-radius: 999px;
            border: 1px solid rgba(80, 211, 147, .35);
            background: rgba(80, 211, 147, .14);
            font-size: 10px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .pref-helper-note {
            margin-top: 12px;
            min-height: 20px;
            color: rgba(255, 255, 255, .72);
            font-size: 13px;
        }

        .pref-helper-note.is-warning {
            color: #ffd7a8;
        }

        .pref-helper-note.is-success {
            color: #baf3cf;
        }

        .pref-card h3 {
            margin: 0 0 6px;
            font-size: 28px;
            line-height: 1;
        }

        .pref-card p {
            margin: 0;
            color: rgba(255, 255, 255, .72);
        }

        .pref-stack {
            display: grid;
            gap: 12px;
            margin-top: 16px;
        }

        .pref-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            align-items: center;
            gap: 14px;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 12px;
            padding: 12px 14px;
            background: rgba(0, 0, 0, .14);
        }

        .pref-row-main {
            min-width: 0;
        }

        .pref-row-label {
            margin: 0;
            font-size: 17px;
            line-height: 1.2;
        }

        .pref-row-desc {
            margin: 4px 0 0;
            font-size: 13px;
            color: rgba(255, 255, 255, .65);
        }

        .pref-col-head {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 78px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255, 255, 255, .62);
        }

        .pref-category-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 999px;
            padding: 2px 9px;
            margin-bottom: 6px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .pref-category-chip.tone-duel {
            border-color: rgba(255, 224, 118, .52);
            color: #fff0c4;
        }

        .pref-category-chip.tone-clips {
            border-color: rgba(131, 241, 206, .52);
            color: #dbfff3;
        }

        .pref-category-chip.tone-system {
            border-color: rgba(168, 193, 255, .52);
            color: #e4ecff;
        }

        .pref-category-chip.tone-match {
            border-color: rgba(255, 153, 153, .52);
            color: #ffe2e2;
        }

        .pref-category-chip.tone-bet {
            border-color: rgba(207, 177, 255, .52);
            color: #efe2ff;
        }

        .pref-switch {
            position: relative;
            display: inline-flex;
            width: 56px;
            height: 32px;
            flex: 0 0 auto;
        }

        .pref-switch input {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            margin: 0;
            cursor: pointer;
        }

        .pref-switch-slider {
            width: 100%;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .24);
            background: rgba(255, 255, 255, .08);
            position: relative;
            transition: .2s ease;
        }

        .pref-switch-slider::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fff;
            transition: .2s ease;
        }

        .pref-switch input:checked + .pref-switch-slider {
            border-color: rgba(80, 211, 147, .62);
            background: rgba(80, 211, 147, .24);
        }

        .pref-switch input:checked + .pref-switch-slider::after {
            transform: translateX(24px);
        }

        .pref-switch input:disabled + .pref-switch-slider {
            opacity: .45;
            cursor: not-allowed;
        }

        .pref-global-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 12px;
            padding: 12px 14px;
            background: rgba(0, 0, 0, .14);
        }

        .pref-global-row + .pref-global-row {
            margin-top: 10px;
        }

        .pref-global-title {
            margin: 0;
            font-size: 16px;
            line-height: 1.2;
        }

        .pref-global-desc {
            margin: 4px 0 0;
            color: rgba(255, 255, 255, .65);
            font-size: 13px;
        }

        .pref-note {
            margin-top: 12px;
            border: 1px dashed rgba(255, 255, 255, .18);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
            color: rgba(255, 255, 255, .72);
        }

        .pref-footer {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px dashed rgba(255, 255, 255, .16);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pref-footer-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 1199.98px) {
            .pref-kpi-grid {
                grid-template-columns: 1fr;
            }

            .pref-quick-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 991.98px) {
            .pref-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .pref-col-head {
                width: auto;
                justify-content: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.notifications.index' : 'notifications.index';
        $preferencesRouteName = $isPublicApp ? 'app.notifications.preferences' : 'notifications.preferences';
        $preferencesUpdateRouteName = $isPublicApp ? 'app.notifications.preferences.update' : 'notifications.preferences.update';

        $channelsData = $channels ?? null;
        $prefs = $preferences ?? collect();
        $hasActiveDevice = (bool) ($hasActiveDevice ?? false);

        $categories = $preferenceCategories ?? [];
        $presets = $preferencePresets ?? [];

        $emailActiveCount = 0;
        $pushActiveCount = 0;
        foreach ($categories as $categoryKey => $categoryMeta) {
            $pref = $prefs->get($categoryKey);
            if ((bool) ($pref?->email_enabled ?? true)) {
                $emailActiveCount++;
            }
            if ((bool) ($pref?->push_enabled ?? true)) {
                $pushActiveCount++;
            }
        }
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Inbox</h2>
                    <h1 class="ph-caption-title">Preferences notifications</h1>
                    <div class="ph-caption-description max-width-800">
                        Configurez les canaux globaux et les categories de notifications.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Inbox</h2>
                        <h1 class="ph-caption-title">Preferences notifications</h1>
                        <div class="ph-caption-description max-width-800">
                            In-app reste actif en permanence.
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
                        <textPath xlink:href="#textcircle">Notification Settings - Notification Settings -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="pref-top-actions">
                    <a href="{{ route($indexRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                        <span data-hover="Retour notifications">Retour notifications</span>
                    </a>
                </div>

                <section class="pref-kpi-grid">
                    <article class="pref-kpi-card tt-anim-fadeinup">
                        <strong>{{ (bool) ($channelsData?->email_opt_in ?? false) ? 'ON' : 'OFF' }}</strong>
                        <span>Email global</span>
                    </article>
                    <article class="pref-kpi-card tt-anim-fadeinup">
                        <strong>{{ (bool) ($channelsData?->push_opt_in ?? false) ? 'ON' : 'OFF' }}</strong>
                        <span>Push global</span>
                    </article>
                    <article class="pref-kpi-card tt-anim-fadeinup">
                        <strong>{{ $hasActiveDevice ? 'OK' : 'NONE' }}</strong>
                        <span>Device push actif</span>
                    </article>
                </section>

                <form method="POST" action="{{ route($preferencesUpdateRouteName) }}" class="tt-anim-fadeinup" id="notification-preferences-form" data-has-active-device="{{ $hasActiveDevice ? '1' : '0' }}" data-presets="{{ e(json_encode($presets, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)) }}">
                    @csrf

                    <section class="pref-card">
                        <h3>Canaux globaux</h3>
                        <p>Le canal In-app est toujours actif. Les options ci-dessous pilotent Email et Push.</p>

                        <div class="margin-top-20">
                            <input type="hidden" name="email_opt_in" value="0">
                            <div class="pref-global-row">
                                <div>
                                    <h4 class="pref-global-title">Email global</h4>
                                    <p class="pref-global-desc">Envoi des notifications par email selon vos categories.</p>
                                </div>
                                <label class="pref-switch" aria-label="Activer email global">
                                    <input
                                        type="checkbox"
                                        name="email_opt_in"
                                        value="1"
                                        data-pref-global="email"
                                        @checked((bool) old('email_opt_in', $channelsData?->email_opt_in ?? false))
                                    >
                                    <span class="pref-switch-slider"></span>
                                </label>
                            </div>

                            <input type="hidden" name="push_opt_in" value="0">
                            <div class="pref-global-row">
                                <div>
                                    <h4 class="pref-global-title">Push global</h4>
                                    <p class="pref-global-desc">Notifications push sur vos devices actifs.</p>
                                </div>
                                <label class="pref-switch" aria-label="Activer push global">
                                    <input
                                        type="checkbox"
                                        name="push_opt_in"
                                        value="1"
                                        data-pref-global="push"
                                        @checked((bool) old('push_opt_in', $channelsData?->push_opt_in ?? false))
                                        @disabled(! $hasActiveDevice)
                                    >
                                    <span class="pref-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        @if(! $hasActiveDevice)
                            <div class="pref-note">
                                Aucun device actif detecte: activez un device pour debloquer les switches push.
                            </div>
                        @endif
                    </section>

                    <section class="pref-card">
                        <h3>Actions rapides</h3>
                        <p>Pilotez vos preferences en un clic, puis ajustez manuellement si besoin avant d enregistrer.</p>

                        <div class="pref-quick-grid margin-top-20">
                            <div class="pref-quick-panel">
                                <h4 class="pref-section-title">Actions groupees</h4>
                                <p class="pref-section-copy">Activez ou coupez rapidement tous les reglages, ou un canal complet.</p>
                                <div class="pref-actions-toolbar">
                                    <button type="button" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item pref-action-btn" data-bulk-action="all-on">
                                        <span data-hover="Tout activer">Tout activer</span>
                                    </button>
                                    <button type="button" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item pref-action-btn" data-bulk-action="all-off">
                                        <span data-hover="Tout desactiver">Tout desactiver</span>
                                    </button>
                                    <button type="button" class="tt-btn tt-btn-secondary tt-btn-sm tt-magnetic-item pref-action-btn" data-bulk-action="email-on">
                                        <span data-hover="Tout activer Email">Tout activer Email</span>
                                    </button>
                                    <button type="button" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item pref-action-btn" data-bulk-action="email-off">
                                        <span data-hover="Tout desactiver Email">Tout desactiver Email</span>
                                    </button>
                                    <button type="button" class="tt-btn tt-btn-secondary tt-btn-sm tt-magnetic-item pref-action-btn" data-bulk-action="push-on" @disabled(! $hasActiveDevice)>
                                        <span data-hover="Tout activer Push">Tout activer Push</span>
                                    </button>
                                    <button type="button" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item pref-action-btn" data-bulk-action="push-off" @disabled(! $hasActiveDevice)>
                                        <span data-hover="Tout desactiver Push">Tout desactiver Push</span>
                                    </button>
                                </div>
                            </div>

                            <div class="pref-preset-panel">
                                <h4 class="pref-section-title">Presets intelligents</h4>
                                <p class="pref-section-copy">Appliquez une base coherente pour limiter le bruit ou suivre l activite en temps reel.</p>
                                <div class="pref-preset-toolbar">
                                    @foreach($presets as $presetKey => $preset)
                                        @if(in_array($presetKey, ['recommended', 'essential'], true))
                                            <button
                                                type="button"
                                                class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item pref-preset-btn"
                                                data-preset="{{ $presetKey }}"
                                                data-recommended="{{ !empty($preset['recommended']) ? 'true' : 'false' }}"
                                                aria-pressed="false"
                                            >
                                                <span data-hover="{{ $preset['label'] }}">{{ $preset['label'] }}</span>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="pref-helper-note" id="pref-live-feedback">Recommande pour la plupart des membres : activez surtout le preset Reglages recommandes.</div>
                            </div>
                        </div>
                    </section>

                    <section class="pref-card">
                        <h3>Regles par categorie</h3>
                        <p><span id="pref-email-count">{{ $emailActiveCount }}</span> categorie(s) email actives - <span id="pref-push-count">{{ $pushActiveCount }}</span> categorie(s) push actives.</p>

                        <div class="pref-stack">
                            <div class="pref-row" aria-hidden="true">
                                <div class="pref-row-main">
                                    <p class="pref-row-label">Categorie</p>
                                </div>
                                <span class="pref-col-head">Email</span>
                                <span class="pref-col-head">Push</span>
                            </div>

                            @foreach($categories as $categoryKey => $categoryMeta)
                                @php($pref = $prefs->get($categoryKey))
                                <div class="pref-row">
                                    <div class="pref-row-main">
                                        <span class="pref-category-chip {{ $categoryMeta['tone'] }}">
                                            <i class="{{ $categoryMeta['icon'] }}"></i>
                                            {{ $categoryMeta['label'] }}
                                        </span>
                                        <h4 class="pref-row-label">{{ $categoryMeta['label'] }}</h4>
                                        <p class="pref-row-desc">{{ $categoryMeta['description'] }}</p>
                                    </div>

                                    <div>
                                        <input type="hidden" name="{{ $categoryKey }}_email" value="0">
                                        <label class="pref-switch" aria-label="Email {{ $categoryMeta['label'] }}">
                                            <input
                                                type="checkbox"
                                                name="{{ $categoryKey }}_email"
                                                value="1"
                                                data-pref-channel="email"
                                                data-pref-category="{{ $categoryKey }}"
                                                @checked((bool) old($categoryKey.'_email', $pref?->email_enabled ?? true))
                                            >
                                            <span class="pref-switch-slider"></span>
                                        </label>
                                    </div>

                                    <div>
                                        <input type="hidden" name="{{ $categoryKey }}_push" value="0">
                                        <label class="pref-switch" aria-label="Push {{ $categoryMeta['label'] }}">
                                            <input
                                                type="checkbox"
                                                name="{{ $categoryKey }}_push"
                                                value="1"
                                                data-pref-channel="push"
                                                data-pref-category="{{ $categoryKey }}"
                                                @checked((bool) old($categoryKey.'_push', $pref?->push_enabled ?? true))
                                                @disabled(! $hasActiveDevice)
                                            >
                                            <span class="pref-switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pref-footer">
                            <small class="pref-note" style="margin:0;">In-app: actif en permanence sur toutes les categories.</small>
                            <div class="pref-footer-actions">
                                <a href="{{ route($preferencesRouteName) }}" class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item">
                                    <span data-hover="Reset visuel">Reset visuel</span>
                                </a>
                                <button type="submit" class="tt-btn tt-btn-primary tt-btn-sm tt-magnetic-item">
                                    <span data-hover="Enregistrer">Enregistrer</span>
                                </button>
                            </div>
                        </div>
                    </section>
                </form>
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
            var form = document.getElementById('notification-preferences-form');
            if (!form) {
                return;
            }

            var hasActiveDevice = form.dataset.hasActiveDevice === '1';
            var feedback = document.getElementById('pref-live-feedback');
            var emailCountNode = document.getElementById('pref-email-count');
            var pushCountNode = document.getElementById('pref-push-count');
            var globalEmail = form.querySelector('[data-pref-global="email"]');
            var globalPush = form.querySelector('[data-pref-global="push"]');
            var emailInputs = Array.from(form.querySelectorAll('[data-pref-channel="email"]'));
            var pushInputs = Array.from(form.querySelectorAll('[data-pref-channel="push"]'));
            var presetButtons = Array.from(form.querySelectorAll('[data-preset]'));
            var presetConfig = JSON.parse(form.dataset.presets || '{}');

            function updateCounts() {
                if (emailCountNode) {
                    emailCountNode.textContent = String(emailInputs.filter(function (input) { return input.checked; }).length);
                }

                if (pushCountNode) {
                    pushCountNode.textContent = String(pushInputs.filter(function (input) { return input.checked; }).length);
                }
            }

            function setFeedback(message, tone) {
                if (!feedback) {
                    return;
                }

                feedback.textContent = message;
                feedback.classList.remove('is-warning', 'is-success');
                if (tone) {
                    feedback.classList.add(tone);
                }
            }

            function clearPresetState() {
                presetButtons.forEach(function (button) {
                    button.classList.remove('is-active');
                    button.setAttribute('aria-pressed', 'false');
                });
            }

            function markPresetActive(presetKey) {
                clearPresetState();

                var activeButton = form.querySelector('[data-preset="' + presetKey + '"]');
                if (!activeButton) {
                    return;
                }

                activeButton.classList.add('is-active');
                activeButton.setAttribute('aria-pressed', 'true');
            }

            function setChannel(inputs, checked) {
                inputs.forEach(function (input) {
                    if (input.disabled && checked) {
                        input.checked = false;
                        return;
                    }

                    input.checked = checked;
                });
            }

            function applyEmail(checked) {
                if (globalEmail) {
                    globalEmail.checked = checked;
                }

                setChannel(emailInputs, checked);
            }

            function applyPush(checked) {
                if (!hasActiveDevice) {
                    if (globalPush) {
                        globalPush.checked = false;
                    }

                    setChannel(pushInputs, false);

                    if (checked) {
                        setFeedback('Impossible d activer les notifications push sans appareil ou abonnement push actif.', 'is-warning');
                    }

                    return false;
                }

                if (globalPush) {
                    globalPush.checked = checked;
                }

                setChannel(pushInputs, checked);

                return true;
            }

            function applyPreset(presetKey) {
                var preset = presetConfig[presetKey];
                if (!preset) {
                    return;
                }

                var emailTargets = new Set(preset.email || []);
                var pushTargets = new Set(preset.push || []);

                emailInputs.forEach(function (input) {
                    input.checked = emailTargets.has(input.dataset.prefCategory);
                });

                var appliedPush = hasActiveDevice;
                pushInputs.forEach(function (input) {
                    input.checked = hasActiveDevice && pushTargets.has(input.dataset.prefCategory);
                });

                if (globalEmail) {
                    globalEmail.checked = emailTargets.size > 0;
                }

                if (globalPush) {
                    globalPush.checked = hasActiveDevice && pushTargets.size > 0;
                }

                markPresetActive(presetKey);
                updateCounts();

                if (!hasActiveDevice && pushTargets.size > 0) {
                    setFeedback(preset.label + ' applique pour Email. Le push reste indisponible tant qu aucun appareil actif n est detecte.', 'is-warning');
                    return;
                }

                setFeedback(preset.hint, 'is-success');
            }

            form.querySelectorAll('[data-bulk-action]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var action = button.dataset.bulkAction;
                    clearPresetState();

                    switch (action) {
                        case 'all-on':
                            applyEmail(true);
                            applyPush(true);
                            setFeedback(hasActiveDevice
                                ? 'Tous les canaux modifiables sont actives.'
                                : 'Email active partout. Le push reste indisponible sans appareil actif.', hasActiveDevice ? 'is-success' : 'is-warning');
                            break;
                        case 'all-off':
                            applyEmail(false);
                            applyPush(false);
                            setFeedback('Email et Push sont desactives sur toutes les categories.', 'is-success');
                            break;
                        case 'email-on':
                            applyEmail(true);
                            setFeedback('Toutes les notifications Email sont activees.', 'is-success');
                            break;
                        case 'email-off':
                            applyEmail(false);
                            setFeedback('Toutes les notifications Email sont desactivees.', 'is-success');
                            break;
                        case 'push-on':
                            applyPush(true);
                            if (hasActiveDevice) {
                                setFeedback('Toutes les notifications Push compatibles sont activees.', 'is-success');
                            }
                            break;
                        case 'push-off':
                            applyPush(false);
                            setFeedback('Toutes les notifications Push sont desactivees.', 'is-success');
                            break;
                    }

                    updateCounts();
                });
            });

            presetButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    applyPreset(button.dataset.preset);
                });
            });

            emailInputs.concat(pushInputs).concat([globalEmail, globalPush].filter(Boolean)).forEach(function (input) {
                input.addEventListener('change', function () {
                    clearPresetState();
                    updateCounts();

                    if (!hasActiveDevice && input && input.dataset && input.dataset.prefChannel === 'push') {
                        setFeedback('Le push reste indisponible tant qu aucun appareil actif n est detecte.', 'is-warning');
                        return;
                    }

                    setFeedback('Preferences modifiees localement. Enregistrez pour appliquer ces reglages.', 'is-success');
                });
            });

            updateCounts();
        });
    </script>
@endsection
