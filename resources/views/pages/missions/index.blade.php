@extends('marketing.layouts.template')

@section('title', 'Missions | ERAH Plateforme')
@section('meta_description', 'Missions ERAH, progression utilisateur, missions decouverte et missions favorites.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <style>
        .mission-shell {
            display: grid;
            gap: 30px;
        }

        .mission-summary-grid,
        .mission-card-grid {
            display: grid;
            gap: 16px;
        }

        .mission-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .mission-card-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .mission-surface {
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 18px;
            padding: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, .03), rgba(8, 9, 14, .92));
            box-shadow: 0 18px 46px rgba(0, 0, 0, .18);
        }

        .mission-summary-card {
            min-height: 140px;
        }

        .mission-summary-kicker,
        .mission-card-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 11px;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .68);
        }

        .mission-summary-kicker::before,
        .mission-card-kicker::before {
            content: "";
            display: inline-block;
            width: 26px;
            height: 1px;
            background: #d80707;
        }

        .mission-summary-value {
            display: block;
            margin-top: 18px;
            font-size: 42px;
            line-height: 1;
            font-weight: 700;
        }

        .mission-summary-note {
            margin-top: 14px;
            color: rgba(255, 255, 255, .66);
            line-height: 1.65;
        }

        .mission-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .mission-section-title {
            margin: 12px 0 0;
            font-size: 32px;
            line-height: 1.05;
        }

        .mission-section-note {
            max-width: 720px;
            margin: 14px 0 0;
            color: rgba(255, 255, 255, .68);
            line-height: 1.65;
        }

        .mission-inline-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .mission-card {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .mission-card-head,
        .mission-card-foot,
        .mission-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
        }

        .mission-card-head {
            align-items: flex-start;
        }

        .mission-card-title {
            margin: 16px 0 0;
            font-size: 26px;
            line-height: 1.14;
        }

        .mission-card-description {
            margin: 16px 0 0;
            color: rgba(255, 255, 255, .74);
            line-height: 1.7;
        }

        .mission-card-meta {
            justify-content: flex-start;
            margin-top: 16px;
        }

        .mission-pill,
        .mission-status {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .14);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .mission-status.is-completed {
            border-color: rgba(92, 213, 144, .44);
            color: #d4ffe4;
        }

        .mission-status.is-pending {
            border-color: rgba(255, 215, 103, .34);
            color: #ffeebc;
        }

        .mission-status.is-expired {
            border-color: rgba(255, 125, 125, .34);
            color: #ffc8c8;
        }

        .mission-status.is-claimable {
            border-color: rgba(255, 170, 73, .38);
            color: #ffdcb0;
        }

        .mission-status.is-locked {
            border-color: rgba(130, 170, 255, .34);
            color: #cbddff;
        }

        .mission-progress {
            margin-top: 18px;
        }

        .mission-progress-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, .74);
            font-size: 13px;
        }

        .mission-progress-track {
            appearance: none;
            -webkit-appearance: none;
            width: 100%;
            height: 10px;
            border: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, .09);
            overflow: hidden;
        }

        .mission-progress-track::-webkit-progress-bar {
            background: rgba(255, 255, 255, .09);
            border-radius: 999px;
        }

        .mission-progress-track::-webkit-progress-value {
            background: linear-gradient(90deg, #d80707 0%, #ff6a3d 100%);
            border-radius: 999px;
        }

        .mission-progress-track::-moz-progress-bar {
            background: linear-gradient(90deg, #d80707 0%, #ff6a3d 100%);
            border-radius: 999px;
        }

        .mission-reward-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .mission-reward-chip {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .03);
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .mission-card-foot {
            margin-top: auto;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        .mission-focus-form {
            margin: 0;
        }

        .mission-empty {
            border: 1px dashed rgba(255, 255, 255, .12);
            border-radius: 16px;
            padding: 22px;
            color: rgba(255, 255, 255, .68);
            text-align: center;
        }

        .mission-history-list {
            display: grid;
            gap: 14px;
        }

        .mission-filter-form {
            display: grid;
            gap: 16px;
            margin-bottom: 24px;
        }

        .mission-filter-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .mission-filter-field {
            display: grid;
            gap: 8px;
        }

        .mission-filter-field label {
            font-size: 11px;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .62);
        }

        .mission-filter-field .tt-form-control {
            min-height: 56px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .03);
            color: rgba(255, 255, 255, .9);
        }

        .mission-history-card {
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 16px;
            padding: 20px;
            background: rgba(255, 255, 255, .02);
        }

        body.tt-lightmode-on .mission-surface,
        body.tt-lightmode-on .mission-history-card {
            border-color: rgba(148, 163, 184, .26);
            background: linear-gradient(180deg, rgba(255, 255, 255, .96), rgba(244, 247, 252, .94));
            box-shadow: 0 18px 40px rgba(148, 163, 184, .16);
        }

        body.tt-lightmode-on .mission-summary-kicker,
        body.tt-lightmode-on .mission-card-kicker,
        body.tt-lightmode-on .mission-section-note,
        body.tt-lightmode-on .mission-summary-note,
        body.tt-lightmode-on .mission-card-description,
        body.tt-lightmode-on .mission-progress-head,
        body.tt-lightmode-on .mission-filter-field label,
        body.tt-lightmode-on .mission-empty {
            color: rgba(51, 65, 85, .78);
        }

        body.tt-lightmode-on .mission-summary-value,
        body.tt-lightmode-on .mission-section-title,
        body.tt-lightmode-on .mission-card-title {
            color: #0f172a;
        }

        body.tt-lightmode-on .mission-pill,
        body.tt-lightmode-on .mission-status,
        body.tt-lightmode-on .mission-reward-chip {
            border-color: rgba(148, 163, 184, .26);
            background: rgba(255, 255, 255, .88);
            color: #0f172a;
        }

        body.tt-lightmode-on .mission-status.is-completed {
            border-color: rgba(34, 197, 94, .26);
            background: rgba(220, 252, 231, .9);
            color: #166534;
        }

        body.tt-lightmode-on .mission-status.is-pending,
        body.tt-lightmode-on .mission-status.is-claimable {
            border-color: rgba(249, 115, 22, .24);
            background: rgba(255, 237, 213, .92);
            color: #9a3412;
        }

        body.tt-lightmode-on .mission-status.is-expired {
            border-color: rgba(239, 68, 68, .24);
            background: rgba(254, 226, 226, .92);
            color: #991b1b;
        }

        body.tt-lightmode-on .mission-status.is-locked {
            border-color: rgba(59, 130, 246, .24);
            background: rgba(219, 234, 254, .92);
            color: #1d4ed8;
        }

        body.tt-lightmode-on .mission-progress-track {
            background: rgba(148, 163, 184, .18);
        }

        body.tt-lightmode-on .mission-card-foot {
            border-top-color: rgba(148, 163, 184, .2);
        }

        body.tt-lightmode-on .mission-empty {
            border-color: rgba(148, 163, 184, .28);
            background: rgba(255, 255, 255, .72);
        }

        body.tt-lightmode-on .mission-filter-field .tt-form-control {
            border-color: rgba(148, 163, 184, .28);
            background: rgba(255, 255, 255, .9);
            color: #0f172a;
        }

        .mission-pagin-item-disabled {
            opacity: .35;
            pointer-events: none;
        }

        @media (max-width: 1399.98px) {
            .mission-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .mission-card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .mission-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .mission-summary-grid,
            .mission-card-grid,
            .mission-filter-grid {
                grid-template-columns: 1fr;
            }

            .mission-surface {
                padding: 22px;
            }

            .mission-section-title {
                font-size: 26px;
            }

            .mission-summary-value {
                font-size: 34px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $missionSummary = $missionSummary ?? [];
        $discoveryCards = $discoveryCards ?? collect();
        $focusCards = $focusCards ?? collect();
        $allCards = $allCards ?? collect();
        $focusTemplateIds = $focusTemplateIds ?? [];
        $focusLimit = $focusLimit ?? 3;
        $dashboardRouteName = $dashboardRouteName ?? 'dashboard';
        $missionFilters = $missionFilters ?? ['type' => 'all', 'difficulty' => 'all', 'status' => 'all', 'duration' => 'all'];
        $missionFilterOptions = $missionFilterOptions ?? ['types' => [], 'difficulties' => [], 'statuses' => [], 'durations' => []];
    @endphp

    <div id="page-header" class="ph-full ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Progression Hub</h2>
                    <h1 class="ph-caption-title">Missions</h1>
                    <div class="ph-caption-description max-width-900">
                        XP pour progresser, points pour agir partout, missions decouverte pour guider vos prochaines etapes.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Progression Hub</h2>
                        <h1 class="ph-caption-title">Missions</h1>
                        <div class="ph-caption-description max-width-900">
                            Un board missions clair, personnalisable et pret pour monter en charge.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="mission-shell">
                    <section class="mission-summary-grid">
                        <article class="mission-surface mission-summary-card">
                            <span class="mission-summary-kicker">Progression</span>
                            <span class="mission-summary-value">{{ (int) ($missionSummary['level'] ?? 1) }}</span>
                            <p class="mission-summary-note">Niveau actuel. Rang associe : <strong>{{ $missionSummary['rank'] ?? 'Bronze' }}</strong>.</p>
                        </article>
                        <article class="mission-surface mission-summary-card">
                            <span class="mission-summary-kicker">XP total</span>
                            <span class="mission-summary-value">{{ number_format((int) ($missionSummary['xp_total'] ?? 0), 0, ',', ' ') }}</span>
                            <p class="mission-summary-note">{{ (int) ($missionSummary['progress_percent'] ?? 0) }}% vers le prochain niveau.</p>
                        </article>
                        <article class="mission-surface mission-summary-card">
                            <span class="mission-summary-kicker">Actives</span>
                            <span class="mission-summary-value">{{ (int) ($missionSummary['total_active'] ?? 0) }}</span>
                            <p class="mission-summary-note">{{ (int) ($missionSummary['completed'] ?? 0) }} terminees, {{ (int) ($missionSummary['pending'] ?? 0) }} encore ouvertes.</p>
                        </article>
                        <article class="mission-surface mission-summary-card">
                            <span class="mission-summary-kicker">Points</span>
                            <span class="mission-summary-value">{{ number_format((int) ($missionSummary['points_potential'] ?? 0), 0, ',', ' ') }}</span>
                            <p class="mission-summary-note">Potentiel points sur les missions actuellement visibles.</p>
                        </article>
                    </section>

                    <section class="mission-surface">
                        <div class="mission-section-head">
                            <div>
                                <span class="mission-card-kicker">Missions decouverte</span>
                                <h2 class="mission-section-title">Commencer par les bases utiles.</h2>
                                <p class="mission-section-note">
                                    Ces missions remontent en tete pour aider les nouveaux membres a comprendre la plateforme avant de pousser le volume.
                                </p>
                            </div>
                            <div class="mission-inline-actions">
                                <a href="{{ route('gifts.index') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Voir les cadeaux">Voir les cadeaux</span>
                                </a>
                                <a href="{{ route($dashboardRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Retour dashboard">Retour dashboard</span>
                                </a>
                            </div>
                        </div>

                        @if($discoveryCards->count())
                            <div class="mission-card-grid">
                                @foreach($discoveryCards as $mission)
                                    @include('pages.missions.partials.card', ['mission' => $mission, 'focusTemplateIds' => $focusTemplateIds, 'focusLimit' => $focusLimit, 'missionFocusStoreRoute' => $missionFocusStoreRoute, 'missionFocusDestroyRoute' => $missionFocusDestroyRoute])
                                @endforeach
                            </div>
                        @else
                            <div class="mission-empty">Aucune mission decouverte active pour le moment.</div>
                        @endif
                    </section>

                    <section class="mission-surface">
                        <div class="mission-section-head">
                            <div>
                                <span class="mission-card-kicker">Mes 3 missions favorites</span>
                                <h2 class="mission-section-title">Garder les priorites visibles.</h2>
                                <p class="mission-section-note">
                                    Vous pouvez ajouter jusqu a {{ (int) $focusLimit }} missions en favoris pour les retrouver ici et dans votre profil.
                                </p>
                            </div>
                        </div>

                        @if($focusCards->count())
                            <div class="mission-card-grid">
                                @foreach($focusCards as $mission)
                                    @include('pages.missions.partials.card', ['mission' => $mission, 'focusTemplateIds' => $focusTemplateIds, 'focusLimit' => $focusLimit, 'missionFocusStoreRoute' => $missionFocusStoreRoute, 'missionFocusDestroyRoute' => $missionFocusDestroyRoute])
                                @endforeach
                            </div>
                        @else
                            <div class="mission-empty">Aucune mission favorite pour le moment. Depuis la liste complete, ajoutez vos priorites.</div>
                        @endif
                    </section>

                    <section class="mission-surface">
                        <div class="mission-section-head">
                            <div>
                                <span class="mission-card-kicker">Toutes les missions</span>
                                <h2 class="mission-section-title">Le board complet de progression.</h2>
                                <p class="mission-section-note">
                                    Toutes les missions actives de la periode, avec leur statut, leur progression et leurs recompenses unifiees en XP + points.
                                </p>
                            </div>
                        </div>

                        <form method="GET" action="{{ route(request()->routeIs('app.*') ? 'app.missions.index' : 'missions.index') }}" class="mission-filter-form">
                            <div class="mission-filter-grid">
                                <div class="mission-filter-field">
                                    <label for="mission_filter_type">Type</label>
                                    <select id="mission_filter_type" name="type" class="tt-form-control" data-lenis-prevent>
                                        @foreach($missionFilterOptions['types'] ?? [] as $option)
                                            <option value="{{ $option['value'] }}" {{ ($missionFilters['type'] ?? 'all') === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mission-filter-field">
                                    <label for="mission_filter_difficulty">Difficulte</label>
                                    <select id="mission_filter_difficulty" name="difficulty" class="tt-form-control" data-lenis-prevent>
                                        @foreach($missionFilterOptions['difficulties'] ?? [] as $option)
                                            <option value="{{ $option['value'] }}" {{ ($missionFilters['difficulty'] ?? 'all') === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mission-filter-field">
                                    <label for="mission_filter_status">Statut</label>
                                    <select id="mission_filter_status" name="status" class="tt-form-control" data-lenis-prevent>
                                        @foreach($missionFilterOptions['statuses'] ?? [] as $option)
                                            <option value="{{ $option['value'] }}" {{ ($missionFilters['status'] ?? 'all') === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mission-filter-field">
                                    <label for="mission_filter_duration">Duree</label>
                                    <select id="mission_filter_duration" name="duration" class="tt-form-control" data-lenis-prevent>
                                        @foreach($missionFilterOptions['durations'] ?? [] as $option)
                                            <option value="{{ $option['value'] }}" {{ ($missionFilters['duration'] ?? 'all') === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mission-inline-actions">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Filtrer">Filtrer</span>
                                </button>
                                <a href="{{ route(request()->routeIs('app.*') ? 'app.missions.index' : 'missions.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Voir tout">Voir tout</span>
                                </a>
                            </div>
                        </form>

                        @if($allCards->count())
                            <div class="mission-card-grid">
                                @foreach($allCards as $mission)
                                    @include('pages.missions.partials.card', ['mission' => $mission, 'focusTemplateIds' => $focusTemplateIds, 'focusLimit' => $focusLimit, 'missionFocusStoreRoute' => $missionFocusStoreRoute, 'missionFocusDestroyRoute' => $missionFocusDestroyRoute])
                                @endforeach
                            </div>
                        @else
                            <div class="mission-empty">Aucune mission active pour le moment.</div>
                        @endif
                    </section>

                    <section class="mission-surface" id="mission-history">
                        <div class="mission-section-head">
                            <div>
                                <span class="mission-card-kicker">Historique</span>
                                <h2 class="mission-section-title">Dernieres missions suivies.</h2>
                                <p class="mission-section-note">
                                    L historique reste utile pour verifier les missions terminees, expirees ou encore en cours sur les dernieres periodes.
                                </p>
                            </div>
                        </div>

                        @if(($history ?? null) && $history->count())
                            <div class="mission-history-list">
                                @foreach($history as $mission)
                                    <article class="mission-history-card">
                                        <div class="mission-card-head">
                                            <div>
                                                <span class="mission-card-kicker">{{ $mission['scope_label'] ?? 'Mission' }}</span>
                                                <h3 class="mission-card-title">{{ $mission['title'] ?? 'Mission' }}</h3>
                                            </div>
                                            <span class="mission-status {{ $mission['status_class'] ?? '' }}">{{ $mission['status_label'] ?? 'En cours' }}</span>
                                        </div>

                                        <p class="mission-card-description">{{ $mission['short_description'] ?? '' }}</p>

                                        <div class="mission-card-meta">
                                            <span class="mission-pill">{{ $mission['event_label'] ?? 'Action libre' }}</span>
                                            <span class="mission-pill">{{ (int) ($mission['progress_count'] ?? 0) }} / {{ (int) ($mission['target_count'] ?? 0) }}</span>
                                            <span class="mission-pill">{{ optional($mission['updated_at'] ?? null)->format('d/m/Y H:i') ?? '-' }}</span>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            @if($history->hasPages())
                                @php
                                    $windowStart = max(1, $history->currentPage() - 1);
                                    $windowEnd = min($history->lastPage(), $history->currentPage() + 1);
                                @endphp
                                <div class="tt-pagination tt-pagin-center padding-top-60 tt-anim-fadeinup">
                                    <div class="tt-pagin-prev">
                                        <a href="{{ $history->previousPageUrl() ?: '#' }}" class="tt-pagin-item tt-magnetic-item {{ $history->onFirstPage() ? 'mission-pagin-item-disabled' : '' }}">
                                            <i class="fas fa-arrow-left"></i>
                                        </a>
                                    </div>
                                    <div class="tt-pagin-numbers">
                                        @for($page = $windowStart; $page <= $windowEnd; $page++)
                                            <a href="{{ $history->url($page) }}" class="tt-pagin-item tt-magnetic-item {{ $history->currentPage() === $page ? 'active' : '' }}">
                                                {{ $page }}
                                            </a>
                                        @endfor
                                    </div>
                                    <div class="tt-pagin-next">
                                        <a href="{{ $history->nextPageUrl() ?: '#' }}" class="tt-pagin-item tt-pagin-next tt-magnetic-item {{ $history->hasMorePages() ? '' : 'mission-pagin-item-disabled' }}">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="mission-empty">Historique vide pour le moment.</div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
