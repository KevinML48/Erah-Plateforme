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

        $categories = [
            'duel' => [
                'label' => 'Duels',
                'description' => 'Invitations, reponses et rappels de duel.',
                'icon' => 'fa-solid fa-crosshairs',
                'tone' => 'tone-duel',
            ],
            'clips' => [
                'label' => 'Clips',
                'description' => 'Likes, commentaires, favoris et tendances.',
                'icon' => 'fa-solid fa-clapperboard',
                'tone' => 'tone-clips',
            ],
            'comment' => [
                'label' => 'Commentaires',
                'description' => 'Reponses, nouvelles discussions et suivi des echanges.',
                'icon' => 'fa-solid fa-comments',
                'tone' => 'tone-clips',
            ],
            'mission' => [
                'label' => 'Missions',
                'description' => 'Validation, progression et bonus journaliers.',
                'icon' => 'fa-solid fa-list-check',
                'tone' => 'tone-system',
            ],
            'quiz' => [
                'label' => 'Quiz',
                'description' => 'Ouverture des quiz, tentatives et validations.',
                'icon' => 'fa-solid fa-circle-question',
                'tone' => 'tone-system',
            ],
            'live_code' => [
                'label' => 'Codes live',
                'description' => 'Codes temporaires, redemptions et campagnes live.',
                'icon' => 'fa-solid fa-bolt',
                'tone' => 'tone-system',
            ],
            'achievement' => [
                'label' => 'Succes',
                'description' => 'Deblocages permanents et badges communautaires.',
                'icon' => 'fa-solid fa-medal',
                'tone' => 'tone-system',
            ],
            'event' => [
                'label' => 'Evenements',
                'description' => 'Fenetres bonus, double XP et operations speciales.',
                'icon' => 'fa-solid fa-calendar-days',
                'tone' => 'tone-match',
            ],
            'system' => [
                'label' => 'Systeme',
                'description' => 'Infos compte, securite et annonces plateforme.',
                'icon' => 'fa-solid fa-shield-halved',
                'tone' => 'tone-system',
            ],
            'match' => [
                'label' => 'Matchs',
                'description' => 'Etat des matchs, timing et resultats.',
                'icon' => 'fa-solid fa-trophy',
                'tone' => 'tone-match',
            ],
            'bet' => [
                'label' => 'Paris',
                'description' => 'Placements, annulations et reglements de paris.',
                'icon' => 'fa-solid fa-coins',
                'tone' => 'tone-bet',
            ],
        ];

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

                <form method="POST" action="{{ route($preferencesUpdateRouteName) }}" class="tt-anim-fadeinup">
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
                        <h3>Regles par categorie</h3>
                        <p>{{ $emailActiveCount }} categorie(s) email actives - {{ $pushActiveCount }} categorie(s) push actives.</p>

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
@endsection
