@extends('marketing.layouts.template')

@section('title', 'Admin Dashboard | ERAH Plateforme')
@section('meta_description', 'Console admin centrale pour piloter la plateforme ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-dashboard-list .tt-avlist-item {
            text-decoration: none;
        }

        .adm-dashboard-list .tt-avlist-col-info {
            display: flex;
            justify-content: flex-end;
        }

        .adm-dashboard-info {
            display: grid;
            justify-items: end;
            gap: 8px;
        }

        .adm-dashboard-info .tt-avlist-info {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .adm-dashboard-list .tt-avlist-item:hover .tt-avlist-title,
        .adm-dashboard-list .tt-avlist-item:hover .tt-avlist-description,
        .adm-dashboard-list .tt-avlist-item:hover .tt-avlist-info,
        .adm-dashboard-list .tt-avlist-item:hover .adm-pill {
            color: rgba(15, 19, 28, .94);
        }

        @media (max-width: 991.98px) {
            .adm-dashboard-list .tt-avlist-col-info {
                justify-content: flex-start;
            }

            .adm-dashboard-info {
                justify-items: start;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $stats = $stats ?? [];
        $managementLinks = collect($managementLinks ?? [])->values();
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Admin Dashboard',
        'heroDescription' => 'Vue globale administration: moderation, contenu, matchs, wallets, cadeaux et missions.',
        'heroMaskDescription' => 'Supervision complete et actions rapides.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Vue instantanee</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Resume live de l'etat de la plateforme et des operations en attente.</p>
                        </div>

                        <div class="adm-kpi-grid">
                            <article class="adm-kpi-card tt-anim-fadeinup">
                                <strong>{{ (int) ($stats['users_total'] ?? 0) }}</strong>
                                <span>Users total</span>
                            </article>
                            <article class="adm-kpi-card tt-anim-fadeinup">
                                <strong>{{ (int) ($stats['admins_total'] ?? 0) }}</strong>
                                <span>Admins</span>
                            </article>
                            <article class="adm-kpi-card tt-anim-fadeinup">
                                <strong>{{ (int) ($stats['clips_published'] ?? 0) }}</strong>
                                <span>Clips publies</span>
                            </article>
                            <article class="adm-kpi-card tt-anim-fadeinup">
                                <strong>{{ (int) ($stats['clips_draft'] ?? 0) }}</strong>
                                <span>Clips brouillons</span>
                            </article>
                            <article class="adm-kpi-card tt-anim-fadeinup">
                                <strong>{{ (int) ($stats['matches_open'] ?? 0) }}</strong>
                                <span>Matchs ouverts</span>
                            </article>
                            <article class="adm-kpi-card tt-anim-fadeinup">
                                <strong>{{ (int) ($stats['bets_pending'] ?? 0) }}</strong>
                                <span>Paris en attente</span>
                            </article>
                            <article class="adm-kpi-card tt-anim-fadeinup">
                                <strong>{{ (int) ($stats['redemptions_pending'] ?? 0) }}</strong>
                                <span>Redemptions pending</span>
                            </article>
                            <article class="adm-kpi-card tt-anim-fadeinup">
                                <strong>{{ (int) ($stats['wallet_volume_today'] ?? 0) }}</strong>
                                <span>Volume wallet jour</span>
                            </article>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Modules administration</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Acces direct vers chaque domaine de gestion de la plateforme.</p>
                        </div>

                        @if($managementLinks->count())
                            <div class="tt-avards-list adm-dashboard-list">
                                @foreach($managementLinks as $item)
                                    <a href="{{ $item['route'] }}" class="tt-avlist-item cursor-alter tt-anim-fadeinup">
                                        <div class="tt-avlist-item-inner">
                                            <div class="tt-avlist-col tt-avlist-col-count">
                                                <div class="tt-avlist-count"></div>
                                            </div>

                                            <div class="tt-avlist-col tt-avlist-col-title">
                                                <h4 class="tt-avlist-title">{{ $item['title'] }}</h4>
                                            </div>

                                            <div class="tt-avlist-col tt-avlist-col-description">
                                                <div class="tt-avlist-description">{{ $item['description'] }}</div>
                                            </div>

                                            <div class="tt-avlist-col tt-avlist-col-info">
                                                <div class="adm-dashboard-info">
                                                    <span class="adm-pill">{{ (int) ($item['count'] ?? 0) }}</span>
                                                    <div class="tt-avlist-info">{{ $item['action'] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="adm-empty">Aucun module admin disponible.</div>
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
