@extends('marketing.layouts.template')

@section('title', 'Gerer evenement | Admin ERAH')
@section('meta_description', 'Pilotage detaille d un match classique ou d un tournoi Rocket League.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-manage-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(420px, .95fr);
            gap: 18px;
            align-items: start;
        }

        .adm-manage-card {
            border: 1px solid var(--adm-border);
            border-radius: 20px;
            padding: 16px;
            background: rgba(255,255,255,.025);
            display: grid;
            gap: 12px;
        }

        .adm-manage-list {
            display: grid;
            gap: 12px;
        }

        .adm-child-card {
            border: 1px solid var(--adm-border-soft);
            border-radius: 18px;
            padding: 14px 16px;
            background: rgba(255,255,255,.025);
            display: grid;
            gap: 10px;
        }

        .adm-child-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .adm-manage-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .adm-match-meta {
            border: 1px solid var(--adm-border-soft);
            border-radius: 16px;
            padding: 12px 14px;
            background: rgba(255,255,255,.025);
        }

        .adm-match-meta span {
            display: block;
            margin-bottom: 6px;
            color: var(--adm-text-soft);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .adm-match-meta strong {
            color: var(--adm-text);
            line-height: 1.4;
        }

        @media (max-width: 1199.98px) {
            .adm-manage-grid,
            .adm-manage-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $teamA = (string) ($match->team_a_name ?: $match->home_team ?: 'Equipe A');
        $teamB = (string) ($match->team_b_name ?: $match->away_team ?: 'Equipe B');
        $isTournament = $match->event_type === \App\Models\EsportMatch::EVENT_TYPE_TOURNAMENT_RUN;
        $currentStatus = (string) ($match->status ?? '-');
        $currentResult = (string) ($match->result ?? '');
        $currentResultInput = match ($currentResult) {
            \App\Models\EsportMatch::RESULT_HOME => \App\Models\EsportMatch::RESULT_TEAM_A,
            \App\Models\EsportMatch::RESULT_AWAY => \App\Models\EsportMatch::RESULT_TEAM_B,
            default => $currentResult,
        };
        $statusOptions = $statusOptions ?? [];
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => $isTournament ? 'Gerer tournoi Rocket League' : 'Gerer match',
        'heroDescription' => $match->displayTitle(),
        'heroMaskDescription' => $eventTypeLabel.' - '.$gameLabel,
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">{{ $match->displayTitle() }}</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">{{ $match->displaySubtitle() ?: 'Tableau de pilotage complet avec etat de l evenement, resultat, reglement des pronostics et lien parent/enfant.' }}</p>
                        </div>

                        <div class="adm-row-actions margin-bottom-20">
                            <a href="{{ route('admin.matches.edit', $match->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Modifier">Modifier</span>
                            </a>
                            <a href="{{ route('matches.show', $match->id) }}" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener">
                                <span data-hover="Voir la page publique">Voir la page publique</span>
                            </a>
                            @if($isTournament && $match->hasUnlockedChildMatches())
                                <a href="{{ route('admin.matches.create', ['parent_match_id' => $match->id, 'game_key' => $match->game_key, 'event_type' => \App\Models\EsportMatch::EVENT_TYPE_HEAD_TO_HEAD, 'market_preset' => 'rocket_league_bo5']) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Creer un match lie">Creer un match lie</span>
                                </a>
                            @endif
                            <a href="{{ route('admin.matches.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour au listing">Retour au listing</span>
                            </a>
                        </div>

                        <div class="adm-compact-kpis">
                            <article class="adm-compact-kpi"><strong>{{ $matchLabelResolver->labelForStatus($currentStatus) }}</strong><span>Etat</span></article>
                            <article class="adm-compact-kpi"><strong>{{ $matchLabelResolver->labelForResult($match, $currentResult) }}</strong><span>Resultat</span></article>
                            <article class="adm-compact-kpi"><strong>{{ (int) $match->bets_count }}</strong><span>Pronostics</span></article>
                            <article class="adm-compact-kpi"><strong>{{ $gameLabel }}</strong><span>Jeu</span></article>
                            <article class="adm-compact-kpi"><strong>{{ $eventTypeLabel }}</strong><span>Type</span></article>
                            <article class="adm-compact-kpi"><strong>{{ $match->best_of ? 'BO'.$match->best_of : '-' }}</strong><span>Format</span></article>
                        </div>
                    </section>

                    <div class="adm-manage-grid">
                        <div class="adm-sub-stack">
                            <section class="adm-surface">
                                <h3 class="adm-surface-title">Pilotage evenement</h3>

                                <div class="adm-manage-list">
                                    <article class="adm-manage-card">
                                        <h4 class="adm-surface-title" style="font-size:24px">Changer l etat</h4>
                                        <form method="POST" action="{{ route('admin.matches.status', $match->id) }}" class="adm-form tt-form tt-form-creative">
                                            @csrf
                                            <div class="tt-form-group">
                                                <label for="status">Etat de l evenement</label>
                                                <select class="tt-form-control" id="status" name="status" required data-lenis-prevent>
                                                    @foreach($statuses as $statusValue)
                                                        <option value="{{ $statusValue }}" {{ $currentStatus === $statusValue ? 'selected' : '' }}>{{ $statusOptions[$statusValue] ?? ucfirst($statusValue) }}</option>
                                                    @endforeach
                                                </select>
                                                @if($matchLabelResolver->descriptionForStatus($currentStatus))
                                                    <p class="adm-meta">{{ $matchLabelResolver->descriptionForStatus($currentStatus) }}</p>
                                                @endif
                                            </div>
                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Mettre a jour">Mettre a jour</span>
                                            </button>
                                        </form>
                                    </article>

                                    <article class="adm-manage-card">
                                        <h4 class="adm-surface-title" style="font-size:24px">{{ $isTournament ? 'Definir parcours final' : 'Definir resultat' }}</h4>
                                        <form method="POST" action="{{ route('admin.matches.result', $match->id) }}" class="adm-form tt-form tt-form-creative">
                                            @csrf
                                            <div class="adm-form-grid {{ $isTournament ? '' : 'adm-form-grid-3' }}">
                                                <div class="tt-form-group">
                                                    <label for="result">Resultat</label>
                                                    <select class="tt-form-control" id="result" name="result" required data-lenis-prevent>
                                                        @foreach($resultOptions as $resultValue => $resultLabel)
                                                            <option value="{{ $resultValue }}" {{ $currentResultInput === $resultValue ? 'selected' : '' }}>{{ $resultLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                @unless($isTournament)
                                                    <div class="tt-form-group">
                                                        <label for="team_a_score">Score {{ $teamA }}</label>
                                                        <input class="tt-form-control" id="team_a_score" name="team_a_score" type="number" min="0" max="20" value="{{ old('team_a_score', $match->team_a_score) }}">
                                                    </div>

                                                    <div class="tt-form-group">
                                                        <label for="team_b_score">Score {{ $teamB }}</label>
                                                        <input class="tt-form-control" id="team_b_score" name="team_b_score" type="number" min="0" max="20" value="{{ old('team_b_score', $match->team_b_score) }}">
                                                    </div>
                                                @endunless
                                            </div>

                                            <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                <span data-hover="Appliquer resultat">Appliquer resultat</span>
                                            </button>
                                        </form>
                                    </article>

                                    @if($isTournament)
                                        <article class="adm-manage-card">
                                            <h4 class="adm-surface-title" style="font-size:24px">Phase matchs Rocket League</h4>
                                            <p class="adm-meta">{{ $match->hasUnlockedChildMatches() ? 'La phase matchs est deja ouverte. Vous pouvez maintenant publier les vrais matchs lies a ce tournoi.' : 'Tant que la phase matchs est fermee, les utilisateurs predisent seulement le parcours final de ERAH dans le tournoi.' }}</p>
                                            @if(! $match->hasUnlockedChildMatches())
                                                <form method="POST" action="{{ route('admin.matches.unlock-child-matches', $match->id) }}">
                                                    @csrf
                                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                        <span data-hover="Ouvrir la phase matchs">Ouvrir la phase matchs</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </article>
                                    @endif

                                    <article class="adm-manage-card">
                                        <h4 class="adm-surface-title" style="font-size:24px">Reglement des pronostics</h4>
                                        <p class="adm-meta">Cette action applique le resultat a tous les marches actifs de l evenement. Pour Rocket League en BO, ajoutez aussi le score final si le marche score exact est actif.</p>
                                        <form method="POST" action="{{ route('admin.matches.settle', $match->id) }}" class="adm-form tt-form tt-form-creative">
                                            @csrf

                                            <div class="adm-form-grid {{ $isTournament ? '' : 'adm-form-grid-4' }}">
                                                <div class="tt-form-group">
                                                    <label for="settle_result">Resultat a appliquer</label>
                                                    <select class="tt-form-control" id="settle_result" name="result" required data-lenis-prevent>
                                                        @foreach($resultOptions as $resultValue => $resultLabel)
                                                            <option value="{{ $resultValue }}" {{ $currentResultInput === $resultValue ? 'selected' : '' }}>{{ $resultLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                @unless($isTournament)
                                                    <div class="tt-form-group">
                                                        <label for="settle_team_a_score">Score {{ $teamA }}</label>
                                                        <input class="tt-form-control" id="settle_team_a_score" name="team_a_score" type="number" min="0" max="20" value="{{ old('team_a_score', $match->team_a_score) }}">
                                                    </div>

                                                    <div class="tt-form-group">
                                                        <label for="settle_team_b_score">Score {{ $teamB }}</label>
                                                        <input class="tt-form-control" id="settle_team_b_score" name="team_b_score" type="number" min="0" max="20" value="{{ old('team_b_score', $match->team_b_score) }}">
                                                    </div>
                                                @endunless

                                                <div class="tt-form-group">
                                                    <label for="idempotency_key">Cle d idempotence</label>
                                                    <input class="tt-form-control" id="idempotency_key" name="idempotency_key" value="settle-{{ $match->id }}-{{ now()->timestamp }}" required>
                                                </div>
                                            </div>

                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Regler les pronostics">Regler les pronostics</span>
                                            </button>
                                        </form>
                                    </article>
                                </div>
                            </section>
                        </div>

                        <div class="adm-sub-stack">
                            <section class="adm-surface">
                                <h3 class="adm-surface-title">Meta evenement</h3>
                                <div class="adm-manage-meta-grid">
                                    <article class="adm-match-meta"><span>Competition</span><strong>{{ $match->competition_name ?: '-' }}</strong></article>
                                    <article class="adm-match-meta"><span>Phase</span><strong>{{ $match->competition_stage ?: '-' }}</strong></article>
                                    <article class="adm-match-meta"><span>Split</span><strong>{{ $match->competition_split ?: '-' }}</strong></article>
                                    <article class="adm-match-meta"><span>Debut</span><strong>{{ $match->starts_at?->format('d/m/Y H:i') ?? '-' }}</strong></article>
                                    <article class="adm-match-meta"><span>Cloture des predictions</span><strong>{{ $match->locked_at?->format('d/m/Y H:i') ?? '-' }}</strong></article>
                                    <article class="adm-match-meta"><span>Fin</span><strong>{{ $match->ends_at?->format('d/m/Y H:i') ?? '-' }}</strong></article>
                                    <article class="adm-match-meta"><span>Tournoi parent</span><strong>{{ $match->parentMatch?->event_name ?: $match->parentMatch?->competition_name ?: '-' }}</strong></article>
                                    <article class="adm-match-meta"><span>Pronostics regles le</span><strong>{{ optional($match->settlement?->processed_at)->format('d/m/Y H:i') ?: '-' }}</strong></article>
                                    <article class="adm-match-meta"><span>Score final</span><strong>{{ $match->team_a_score !== null && $match->team_b_score !== null ? $match->team_a_score.' - '.$match->team_b_score : '-' }}</strong></article>
                                </div>
                            </section>

                            <section class="adm-surface">
                                <h3 class="adm-surface-title">{{ $isTournament ? 'Matchs enfants lies' : 'Markets actifs' }}</h3>

                                @if($isTournament)
                                    @if($match->childMatches->count())
                                        <div class="adm-manage-list">
                                            @foreach($match->childMatches as $childMatch)
                                                <article class="adm-child-card">
                                                    <div class="adm-child-head">
                                                        <div>
                                                            <strong>{{ $childMatch->displayTitle() }}</strong>
                                                            <p class="adm-meta">{{ $childMatch->starts_at?->format('d/m/Y H:i') ?? '-' }}{{ $childMatch->best_of ? ' - BO'.$childMatch->best_of : '' }}</p>
                                                        </div>
                                                        <div class="adm-row-actions">
                                                            <a href="{{ route('admin.matches.manage', $childMatch->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                                <span data-hover="Gerer">Gerer</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="adm-match-pills">
                                                        <span class="adm-pill">{{ $matchLabelResolver->labelForStatus($childMatch->status, true) }}</span>
                                                        <span class="adm-pill">Pronostics {{ (int) $childMatch->bets_count }}</span>
                                                        <span class="adm-pill">Resultat {{ $matchLabelResolver->labelForResult($childMatch, $childMatch->result) }}</span>
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="adm-empty">Aucun match enfant cree pour ce tournoi.</div>
                                    @endif
                                @else
                                    @if($match->markets->count())
                                        <div class="adm-manage-list">
                                            @foreach($match->markets as $market)
                                                <article class="adm-child-card">
                                                    <div class="adm-child-head">
                                                        <div>
                                                            <strong>{{ $market->title }}</strong>
                                                            <p class="adm-meta">{{ $matchLabelResolver->labelForMarketKey($market->key) }} - {{ $market->is_active ? 'actif' : 'desactive' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="adm-match-pills">
                                                        @foreach($market->selections as $selection)
                                                            <span class="adm-pill">{{ $selection->label }} / {{ number_format((float) $selection->odds, 3) }}</span>
                                                        @endforeach
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="adm-empty">Aucun market configure.</div>
                                    @endif
                                @endif
                            </section>
                        </div>
                    </div>

                    <section class="adm-surface">
                        <h3 class="adm-surface-title">Derniers bets (50)</h3>

                        @if($match->bets->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Pronostic</th>
                                            <th>Utilisateur</th>
                                            <th>Marche</th>
                                            <th>Choix</th>
                                            <th>Mise</th>
                                            <th>Etat</th>
                                            <th>Gain</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($match->bets as $bet)
                                            @php
                                                $betMarket = $match->markets->firstWhere('key', $bet->market_key);
                                                $betSelection = $betMarket?->selections->firstWhere('key', $bet->selection_key);
                                            @endphp
                                            <tr>
                                                <td>#{{ $bet->id }}</td>
                                                <td>{{ $bet->user->name ?? 'Utilisateur indisponible' }}</td>
                                                <td>{{ $matchLabelResolver->labelForMarketKey($bet->market_key) }}</td>
                                                <td>{{ $betSelection?->label ?? $bet->selection_key }}</td>
                                                <td>{{ (int) $bet->stake_points }}</td>
                                                <td><span class="adm-pill">{{ $matchLabelResolver->labelForBetStatus($bet->status) }}</span></td>
                                                <td>{{ (int) ($bet->payout ?? 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="adm-empty">Aucun bet sur cet evenement.</div>
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
