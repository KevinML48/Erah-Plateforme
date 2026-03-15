@extends('marketing.layouts.template')

@section('title', 'Cockpit operations admin | ERAH Plateforme')
@section('meta_description', 'Cockpit admin operationnel pour superviser, filtrer, investiguer et agir en temps reel.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-kpi-grid-ops {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .adm-alert-list {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .adm-alert-item {
            border: 1px solid var(--adm-border);
            border-radius: 18px;
            padding: 14px;
            background: var(--adm-surface-bg);
            display: grid;
            gap: 10px;
        }

        .adm-alert-item.is-critical {
            border-color: rgba(241, 67, 67, .44);
        }

        .adm-alert-item.is-warning {
            border-color: rgba(255, 183, 94, .42);
        }

        .adm-alert-item.is-idle {
            opacity: .72;
        }

        .adm-alert-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .adm-alert-head h3 {
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
        }

        .adm-alert-item p {
            margin: 0;
            color: var(--adm-text-soft);
        }

        .adm-ops-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .adm-ops-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .adm-ops-card {
            border: 1px solid var(--adm-border);
            border-radius: 18px;
            padding: 14px;
            background: var(--adm-surface-bg);
            display: grid;
            gap: 12px;
        }

        .adm-ops-card h3 {
            margin: 0;
            font-size: 20px;
            line-height: 1.05;
        }

        .adm-mini-list {
            display: grid;
            gap: 10px;
        }

        .adm-mini-item {
            border: 1px solid var(--adm-border-soft);
            border-radius: 14px;
            padding: 12px;
            display: grid;
            gap: 10px;
            background: rgba(255, 255, 255, .02);
        }

        .adm-mini-item strong {
            display: block;
            color: var(--adm-text);
            line-height: 1.25;
        }

        .adm-mini-item p {
            margin: 6px 0 0;
            color: var(--adm-text-soft);
            font-size: 13px;
        }

        .adm-inline-form-stacked {
            align-items: stretch;
        }

        .adm-inline-form-stacked .adm-inline-input {
            width: 100%;
            min-width: 0;
        }

        .adm-search-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .adm-search-card {
            border: 1px solid var(--adm-border);
            border-radius: 18px;
            padding: 14px;
            background: var(--adm-surface-bg);
            display: grid;
            gap: 10px;
        }

        .adm-search-card h3 {
            margin: 0;
            font-size: 20px;
            line-height: 1.05;
        }

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

        @media (max-width: 1399.98px) {
            .adm-kpi-grid-ops {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1199.98px) {
            .adm-alert-list,
            .adm-ops-grid,
            .adm-search-grid {
                grid-template-columns: 1fr;
            }

            .adm-dashboard-list .tt-avlist-col-info {
                justify-content: flex-start;
            }

            .adm-dashboard-info {
                justify-items: start;
            }
        }

        @media (max-width: 767.98px) {
            .adm-kpi-grid-ops {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $kpis = $kpis ?? [];
        $alerts = $alerts ?? [];
        $pending = $pending ?? [];
        $feed = $feed ?? null;
        $feedItems = collect($feed?->items() ?? []);
        $feedFilters = $feed_filters ?? [];
        $feedOptions = $feed_options ?? ['sources' => [], 'modules' => [], 'types' => []];
        $quickLinks = collect($quick_links ?? []);
        $search = $search ?? ['query' => '', 'groups' => [], 'total_hits' => 0];
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Administration ERAH',
        'heroTitle' => 'Cockpit operations',
        'heroDescription' => 'Supervision unifiee des modules critiques, flux d activite filtrable et actions rapides admin.',
        'heroMaskDescription' => 'Pilotage operationnel live de la plateforme.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Recherche globale admin</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Recherche transverse: utilisateur, cadeau, achat shop, match, clip, avis et supporter.</p>
                        </div>

                        <form method="GET" action="{{ route('admin.dashboard') }}" class="tt-form tt-form-creative adm-form">
                            <div class="adm-form-grid">
                                <div class="tt-form-group">
                                    <label for="global-q">Recherche ID, nom, email, titre</label>
                                    <input id="global-q" name="q" class="tt-form-control" value="{{ $search['query'] ?? '' }}" placeholder="Ex: 42, erah@..., Rocket, clip, cadeau...">
                                </div>
                                <div class="tt-form-group adm-form-cta">
                                    <p class="adm-form-cta-copy">Acces détail direct sans quitter le cockpit.</p>
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Rechercher">Rechercher</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">KPI globaux</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Vue synthese pour supervision immediate des flux critiques.</p>
                        </div>

                        <div class="adm-kpi-grid adm-kpi-grid-ops">
                            <article class="adm-kpi-card"><strong data-kpi-key="users_total">{{ (int) ($kpis['users_total'] ?? 0) }}</strong><span>Utilisateurs</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="users_recent">{{ (int) ($kpis['users_recent'] ?? 0) }}</strong><span>Nouveaux 7j</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="admins_total">{{ (int) ($kpis['admins_total'] ?? 0) }}</strong><span>Admins</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="clips_published">{{ (int) ($kpis['clips_published'] ?? 0) }}</strong><span>Clips publies</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="clips_draft">{{ (int) ($kpis['clips_draft'] ?? 0) }}</strong><span>Clips brouillons</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="matches_open">{{ (int) ($kpis['matches_open'] ?? 0) }}</strong><span>Matchs ouverts</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="matches_live">{{ (int) ($kpis['matches_live'] ?? 0) }}</strong><span>Matchs live</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="matches_to_close">{{ (int) ($kpis['matches_to_close'] ?? 0) }}</strong><span>Matchs a cloturer</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="bets_pending">{{ (int) ($kpis['bets_pending'] ?? 0) }}</strong><span>Paris en attente</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="bets_to_settle">{{ (int) ($kpis['bets_to_settle'] ?? 0) }}</strong><span>Paris a settle</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="gift_redemptions_pending">{{ (int) ($kpis['gift_redemptions_pending'] ?? 0) }}</strong><span>Cadeaux pending</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="gift_redemptions_approved">{{ (int) ($kpis['gift_redemptions_approved'] ?? 0) }}</strong><span>Cadeaux approved</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="gift_redemptions_shipped">{{ (int) ($kpis['gift_redemptions_shipped'] ?? 0) }}</strong><span>Cadeaux shipped</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="points_volume_today">{{ (int) ($kpis['points_volume_today'] ?? 0) }}</strong><span>Volume points jour</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="shop_purchases_today">{{ (int) ($kpis['shop_purchases_today'] ?? 0) }}</strong><span>Achats shop jour</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="gift_redemptions_today">{{ (int) ($kpis['gift_redemptions_today'] ?? 0) }}</strong><span>Redemptions jour</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="reviews_pending">{{ (int) ($kpis['reviews_pending'] ?? 0) }}</strong><span>Avis pending</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="supporters_active">{{ (int) ($kpis['supporters_active'] ?? 0) }}</strong><span>Supporters actifs</span></article>
                            <article class="adm-kpi-card"><strong data-kpi-key="low_stock_total">{{ (int) ($kpis['low_stock_total'] ?? 0) }}</strong><span>Objets stock faible</span></article>
                        </div>
                    </section>

                    <section class="adm-surface" id="admin-alerts">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Alertes operationnelles</h2>
                        </div>
                        <div data-alerts-container>
                            @include('pages.admin.partials.operations-alerts', ['alerts' => $alerts])
                        </div>
                    </section>

                    <section class="adm-surface" id="admin-pending-ops">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Operations en attente et actions rapides</h2>
                        </div>
                        <div data-pending-container>
                            @include('pages.admin.partials.operations-pending', ['pending' => $pending])
                        </div>
                    </section>

                    @include('pages.admin.partials.operations-search-results', ['search' => $search])

                    <section class="adm-surface" id="admin-feed">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Flux activite admin</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Flux centralise, filtrable, trie du plus recent au plus ancien.</p>
                        </div>

                        <form method="GET" action="{{ route('admin.dashboard') }}" class="adm-form margin-bottom-20">
                            <input type="hidden" name="q" value="{{ $search['query'] ?? '' }}">
                            <div class="adm-form-grid-4">
                                <div class="tt-form-group">
                                    <label for="feed-source">Source</label>
                                    <select id="feed-source" name="feed_source" class="tt-form-control">
                                        @foreach(($feedOptions['sources'] ?? []) as $key => $label)
                                            <option value="{{ $key }}" @selected(($feedFilters['source'] ?? 'all') === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tt-form-group">
                                    <label for="feed-module">Module</label>
                                    <select id="feed-module" name="feed_module" class="tt-form-control">
                                        @foreach(($feedOptions['modules'] ?? []) as $key => $label)
                                            <option value="{{ $key }}" @selected(($feedFilters['module'] ?? 'all') === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tt-form-group">
                                    <label for="feed-type">Type</label>
                                    <select id="feed-type" name="feed_type" class="tt-form-control">
                                        @foreach(($feedOptions['types'] ?? []) as $key => $label)
                                            <option value="{{ $key }}" @selected(($feedFilters['type'] ?? 'all') === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tt-form-group">
                                    <label for="feed-q">Recherche flux</label>
                                    <input id="feed-q" name="feed_q" class="tt-form-control" value="{{ $feedFilters['feed_q'] ?? '' }}" placeholder="ID, user, cible, resume...">
                                </div>
                            </div>
                            <div class="adm-row-actions">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Filtrer">Filtrer</span></button>
                                <a href="{{ route('admin.dashboard', ['q' => $search['query'] ?? '']) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Reset">Reset</span></a>
                            </div>
                        </form>

                        <div class="adm-table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Source</th>
                                        <th>Type</th>
                                        <th>Module</th>
                                        <th>Date</th>
                                        <th>Utilisateur</th>
                                        <th>Cible</th>
                                        <th>Resume</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody data-feed-rows>
                                    @include('pages.admin.partials.operations-feed-rows', ['feedItems' => $feedItems])
                                </tbody>
                            </table>
                        </div>

                        @if($feed)
                            <div class="adm-pagin">{{ $feed->onEachSide(1)->links('vendor.pagination.admin') }}</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Acces rapides modules</h2>
                        </div>

                        @if($quickLinks->isNotEmpty())
                            <div class="tt-avards-list adm-dashboard-list">
                                @foreach($quickLinks as $item)
                                    <a href="{{ $item['route'] }}" class="tt-avlist-item cursor-alter tt-anim-fadeinup">
                                        <div class="tt-avlist-item-inner">
                                            <div class="tt-avlist-col tt-avlist-col-count"><div class="tt-avlist-count"></div></div>
                                            <div class="tt-avlist-col tt-avlist-col-title"><h4 class="tt-avlist-title">{{ $item['title'] }}</h4></div>
                                            <div class="tt-avlist-col tt-avlist-col-description"><div class="tt-avlist-description">{{ $item['description'] }}</div></div>
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
    <script id="admin-ops-live-data" type="application/json">@json(['endpoint' => route('admin.dashboard.live'), 'interval_ms' => 25000])</script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dataEl = document.getElementById('admin-ops-live-data');
            if (!dataEl || !window.fetch) {
                return;
            }

            var config = {};
            try {
                config = JSON.parse(dataEl.textContent || '{}');
            } catch (error) {
                return;
            }

            if (!config.endpoint) {
                return;
            }

            var kpiNodes = document.querySelectorAll('[data-kpi-key]');
            var alertsContainer = document.querySelector('[data-alerts-container]');
            var pendingContainer = document.querySelector('[data-pending-container]');
            var feedRows = document.querySelector('[data-feed-rows]');
            var feedSource = document.getElementById('feed-source');
            var feedModule = document.getElementById('feed-module');
            var feedType = document.getElementById('feed-type');
            var feedQuery = document.getElementById('feed-q');

            function updateKpis(payload) {
                kpiNodes.forEach(function (node) {
                    var key = node.getAttribute('data-kpi-key');
                    if (!key) {
                        return;
                    }

                    var value = payload[key];
                    if (typeof value === 'number') {
                        node.textContent = String(value);
                    }
                });
            }

            async function refreshCockpit() {
                var query = new URLSearchParams({
                    feed_source: feedSource ? feedSource.value : 'all',
                    feed_module: feedModule ? feedModule.value : 'all',
                    feed_type: feedType ? feedType.value : 'all',
                    feed_q: feedQuery ? feedQuery.value : ''
                });

                try {
                    var response = await window.fetch(config.endpoint + '?' + query.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        return;
                    }

                    var payload = await response.json();

                    updateKpis(payload.kpis || {});

                    if (alertsContainer && typeof payload.alerts_html === 'string') {
                        alertsContainer.innerHTML = payload.alerts_html;
                    }

                    if (pendingContainer && typeof payload.pending_html === 'string') {
                        pendingContainer.innerHTML = payload.pending_html;
                    }

                    if (feedRows && typeof payload.feed_rows_html === 'string') {
                        feedRows.innerHTML = payload.feed_rows_html;
                    }
                } catch (error) {
                    return;
                }
            }

            window.setInterval(refreshCockpit, Number(config.interval_ms || 25000));
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'visible') {
                    refreshCockpit();
                }
            });
        });
    </script>
@endsection
