@extends('marketing.layouts.template')

@section('title', 'Admin Matchs | ERAH Plateforme')
@section('meta_description', 'Pilotage admin des matchs classiques et parcours tournoi Rocket League.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-match-filter-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) repeat(3, minmax(180px, 1fr));
            gap: 12px;
        }

        .adm-match-list {
            display: grid;
            gap: 14px;
        }

        .adm-match-card {
            border: 1px solid var(--adm-border);
            border-radius: 22px;
            padding: 18px 20px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                var(--adm-surface-bg);
            display: grid;
            gap: 16px;
        }

        .adm-match-head {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 16px;
            align-items: start;
        }

        .adm-match-title {
            margin: 0;
            font-size: clamp(24px, 3vw, 42px);
            line-height: .94;
            color: var(--adm-text);
        }

        .adm-match-subtitle {
            margin: 6px 0 0;
            color: var(--adm-text-soft);
        }

        .adm-match-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .adm-match-meta {
            border: 1px solid var(--adm-border-soft);
            border-radius: 16px;
            padding: 12px 14px;
            background: rgba(255,255,255,.025);
            display: grid;
            gap: 6px;
        }

        .adm-match-meta span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--adm-text-soft);
        }

        .adm-match-meta strong {
            color: var(--adm-text);
            font-size: 15px;
            line-height: 1.35;
        }

        .adm-match-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        @media (max-width: 1199.98px) {
            .adm-match-filter-grid,
            .adm-match-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .adm-match-filter-grid,
            .adm-match-grid,
            .adm-match-head {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $stats = $stats ?? ['total' => 0, 'tournaments' => 0, 'rocket_league_matches' => 0, 'scheduled' => 0];
        $statusOptions = $statusOptions ?? [];
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Pilotage matchs & tournois',
        'heroDescription' => 'Retrouver en un seul endroit les matchs directs, les tournois Rocket League et les matchs enfants lies au TOP 16.',
        'heroMaskDescription' => 'Filtres clairs, etats lisibles et gestion simple des evenements esport.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Vue d ensemble</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Le listing separe les matchs directs, les tournois Rocket League et les rencontres creees apres ouverture de la phase TOP 16.</p>
                        </div>

                        <div class="adm-compact-kpis">
                            <article class="adm-compact-kpi">
                                <strong>{{ (int) $stats['total'] }}</strong>
                                <span>Total evenements</span>
                            </article>
                            <article class="adm-compact-kpi">
                                <strong>{{ (int) $stats['tournaments'] }}</strong>
                                <span>Tournois RL</span>
                            </article>
                            <article class="adm-compact-kpi">
                                <strong>{{ (int) $stats['rocket_league_matches'] }}</strong>
                                <span>Matchs RL</span>
                            </article>
                            <article class="adm-compact-kpi">
                                <strong>{{ (int) $stats['scheduled'] }}</strong>
                                <span>Programmes</span>
                            </article>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Filtres admin</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Recherche, jeu, type d evenement et etat de diffusion. Le bouton de creation ouvre ensuite le bon formulaire selon le contexte.</p>
                        </div>

                        <form method="GET" action="{{ route('admin.matches.index') }}" class="adm-form">
                            <div class="adm-match-filter-grid">
                                <div class="tt-form-group">
                                    <label for="match_search">Recherche</label>
                                    <input class="tt-form-control" id="match_search" name="q" value="{{ $search }}" placeholder="Nom du tournoi, equipes, stage, split...">
                                </div>

                                <div class="tt-form-group">
                                    <label for="match_status">Statut</label>
                                    <select class="tt-form-control" id="match_status" name="status" data-lenis-prevent>
                                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tous</option>
                                        @foreach($statuses as $statusValue)
                                            <option value="{{ $statusValue }}" {{ $status === $statusValue ? 'selected' : '' }}>{{ $statusOptions[$statusValue] ?? ucfirst($statusValue) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="tt-form-group">
                                    <label for="match_game">Jeu</label>
                                    <select class="tt-form-control" id="match_game" name="game" data-lenis-prevent>
                                        <option value="all" {{ $game === 'all' ? 'selected' : '' }}>Tous</option>
                                        @foreach($gameOptions as $gameKey => $gameLabel)
                                            <option value="{{ $gameKey }}" {{ $game === $gameKey ? 'selected' : '' }}>{{ $gameLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="tt-form-group">
                                    <label for="match_event_type">Type evenement</label>
                                    <select class="tt-form-control" id="match_event_type" name="event_type" data-lenis-prevent>
                                        <option value="all" {{ $eventType === 'all' ? 'selected' : '' }}>Tous</option>
                                        @foreach($eventTypeOptions as $eventTypeKey => $eventTypeLabel)
                                            <option value="{{ $eventTypeKey }}" {{ $eventType === $eventTypeKey ? 'selected' : '' }}>{{ $eventTypeLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="adm-row-actions">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Appliquer">Appliquer</span>
                                </button>
                                <a href="{{ route('admin.matches.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Reinitialiser">Reinitialiser</span>
                                </a>
                                <a href="{{ route('admin.matches.create') }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                    <span data-hover="Creer un evenement">Creer un evenement</span>
                                </a>
                            </div>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Bibliotheque evenements</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Chaque carte resume le contexte du match, l etat des predictions, le tournoi parent eventuel et les actions rapides disponibles.</p>
                        </div>

                        @if($matches->count())
                            <div class="adm-match-list">
                                @foreach($matches as $match)
                                    @php
                                        $isTournament = $match->event_type === \App\Models\EsportMatch::EVENT_TYPE_TOURNAMENT_RUN;
                                        $title = $match->displayTitle();
                                        $subtitle = $match->displaySubtitle();
                                    @endphp
                                    <article class="adm-match-card tt-anim-fadeinup">
                                        <div class="adm-match-head">
                                            <div>
                                                <div class="adm-match-pills margin-bottom-10">
                                                    <span class="adm-pill">{{ $matchLabelResolver->labelForGame($match->game_key) }}</span>
                                                    <span class="adm-pill">{{ $matchLabelResolver->labelForEventType($match->event_type) }}</span>
                                                    <span class="adm-pill {{ in_array($match->status, ['live', 'locked'], true) ? 'adm-pill-live' : '' }}">{{ $matchLabelResolver->labelForStatus($match->status, true) }}</span>
                                                    @if($match->best_of)
                                                        <span class="adm-pill">BO{{ $match->best_of }}</span>
                                                    @endif
                                                    @if($match->parentMatch)
                                                        <span class="adm-pill">Tournoi parent : {{ $match->parentMatch->event_name ?: $match->parentMatch->competition_name ?: '#'.$match->parentMatch->id }}</span>
                                                    @endif
                                                </div>
                                                <h3 class="adm-match-title">{{ $title }}</h3>
                                                @if($subtitle)
                                                    <p class="adm-match-subtitle">{{ $subtitle }}</p>
                                                @endif
                                            </div>

                                            <div class="adm-row-actions">
                                                <a href="{{ route('admin.matches.manage', $match->id) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                    <span data-hover="Gerer">Gerer</span>
                                                </a>
                                                <a href="{{ route('admin.matches.edit', $match->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                    <span data-hover="Modifier">Modifier</span>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="adm-match-grid">
                                            <article class="adm-match-meta">
                                                <span>Debut</span>
                                                <strong>{{ $match->starts_at?->format('d/m/Y H:i') ?? '-' }}</strong>
                                            </article>
                                            <article class="adm-match-meta">
                                                <span>Cloture des predictions</span>
                                                <strong>{{ $match->locked_at?->format('d/m/Y H:i') ?? '-' }}</strong>
                                            </article>
                                            <article class="adm-match-meta">
                                                <span>Pronostics</span>
                                                <strong>{{ (int) $match->bets_count }}</strong>
                                            </article>
                                            <article class="adm-match-meta">
                                                <span>Resultat</span>
                                                <strong>{{ $matchLabelResolver->labelForResult($match, $match->result) }}</strong>
                                            </article>
                                            <article class="adm-match-meta">
                                                <span>Competition</span>
                                                <strong>{{ $match->competition_name ?: '-' }}</strong>
                                            </article>
                                            <article class="adm-match-meta">
                                                <span>Phase</span>
                                                <strong>{{ $match->competition_stage ?: '-' }}</strong>
                                            </article>
                                            <article class="adm-match-meta">
                                                <span>Split</span>
                                                <strong>{{ $match->competition_split ?: '-' }}</strong>
                                            </article>
                                            <article class="adm-match-meta">
                                                <span>{{ $isTournament ? 'Phase matchs' : 'Pronostics regles le' }}</span>
                                                <strong>
                                                    @if($isTournament)
                                                        {{ (int) $match->child_matches_count }} match(s) {{ $match->child_matches_unlocked_at ? '- phase ouverte' : '- phase fermee' }}
                                                    @else
                                                        {{ $match->settlement?->processed_at?->format('d/m/Y H:i') ?? '-' }}
                                                    @endif
                                                </strong>
                                            </article>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $matches->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun evenement ne correspond a ces filtres.</div>
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
