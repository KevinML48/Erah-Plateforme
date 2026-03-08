@extends('marketing.layouts.template')

@section('title', ($match ? 'Modifier evenement | Admin ERAH' : 'Creer evenement | Admin ERAH'))
@section('meta_description', 'Formulaire admin pour matchs classiques, tournois Rocket League et matchs enfants.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-match-form-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(380px, .8fr);
            gap: 18px;
            align-items: start;
        }

        .adm-market-stack {
            display: grid;
            gap: 14px;
        }

        .adm-market-card {
            border: 1px solid var(--adm-border);
            border-radius: 20px;
            padding: 16px;
            background: rgba(255,255,255,.025);
            display: grid;
            gap: 12px;
        }

        .adm-market-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .adm-selection-grid {
            display: grid;
            gap: 10px;
        }

        .adm-selection-row {
            display: grid;
            grid-template-columns: minmax(150px, .8fr) minmax(0, 1.4fr) minmax(120px, .6fr) 86px auto;
            gap: 10px;
            align-items: center;
        }

        .adm-side-note {
            display: grid;
            gap: 14px;
        }

        .adm-side-card {
            border: 1px solid var(--adm-border);
            border-radius: 20px;
            padding: 18px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.015)),
                var(--adm-surface-bg);
            display: grid;
            gap: 10px;
        }

        .adm-side-card h3 {
            margin: 0;
            color: var(--adm-text);
            font-size: 24px;
            line-height: 1;
        }

        .adm-section-hidden {
            display: none !important;
        }

        @media (max-width: 1199.98px) {
            .adm-match-form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .adm-selection-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $match = $match ?? null;
        $action = $action ?? route('admin.matches.store');
        $method = $method ?? 'POST';
        $formContext = $formContext ?? [];
        $isTournament = ($formContext['event_type'] ?? \App\Models\EsportMatch::EVENT_TYPE_HEAD_TO_HEAD) === \App\Models\EsportMatch::EVENT_TYPE_TOURNAMENT_RUN;
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => $match ? 'Modifier evenement esport' : 'Creer un evenement esport',
        'heroDescription' => $match
            ? 'Edition d un match classique, d un tournoi Rocket League ou d un match enfant TOP 16.'
            : 'Formulaire intelligent: match direct, tournoi Rocket League, puis matchs enfants lies au tournoi parent.',
        'heroMaskDescription' => 'Preset de marches, parcours tournoi RL, BO et relation parent/enfant.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <form method="POST" action="{{ $action }}" class="adm-match-form-grid">
                        @csrf
                        @if($method !== 'POST')
                            @method($method)
                        @endif

                        <div class="adm-sub-stack">
                            <section class="adm-surface">
                                <div class="tt-heading tt-heading-lg margin-bottom-30">
                                    <h2 class="tt-heading-title tt-text-reveal">{{ $match ? 'Evenement #'.$match->id : 'Parametrage principal' }}</h2>
                                    <p class="max-width-700 tt-anim-fadeinup text-gray">Le choix type + jeu + preset prepare automatiquement la bonne structure. Vous pouvez ensuite ajuster les marches et les choix avant l enregistrement.</p>
                                </div>

                                <div class="adm-form tt-form tt-form-creative">
                                    <div class="adm-form-grid-3">
                                        <div class="tt-form-group">
                                            <label for="event_type">Type d evenement</label>
                                            <select class="tt-form-control" id="event_type" name="event_type" data-match-event-type data-lenis-prevent>
                                                @foreach($eventTypeOptions as $eventTypeKey => $eventTypeLabel)
                                                    <option value="{{ $eventTypeKey }}" {{ old('event_type', $formContext['event_type'] ?? '') === $eventTypeKey ? 'selected' : '' }}>{{ $eventTypeLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="game_key">Jeu</label>
                                            <select class="tt-form-control" id="game_key" name="game_key" data-match-game-key data-lenis-prevent>
                                                @foreach($gameOptions as $gameKey => $gameLabel)
                                                    <option value="{{ $gameKey }}" {{ old('game_key', $formContext['game_key'] ?? '') === $gameKey ? 'selected' : '' }}>{{ $gameLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="market_preset">Preset de marches</label>
                                            <select class="tt-form-control" id="market_preset" name="market_preset" data-market-preset data-lenis-prevent>
                                                @foreach($marketPresetOptions as $presetKey => $presetLabel)
                                                    <option value="{{ $presetKey }}" {{ (string) $marketPreset === (string) $presetKey ? 'selected' : '' }}>{{ $presetLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="adm-form-grid-3">
                                        <div class="tt-form-group">
                                            <label for="starts_at">Debut</label>
                                            <input class="tt-form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', $match && $match->starts_at ? $match->starts_at->format('Y-m-d\\TH:i') : '') }}" required>
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="locked_at">Cloture des predictions</label>
                                            <input class="tt-form-control" id="locked_at" name="locked_at" type="datetime-local" value="{{ old('locked_at', $match && $match->locked_at ? $match->locked_at->format('Y-m-d\\TH:i') : '') }}">
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="ends_at">Fin / fenetre</label>
                                            <input class="tt-form-control" id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at', $match && $match->ends_at ? $match->ends_at->format('Y-m-d\\TH:i') : '') }}">
                                        </div>
                                    </div>

                                    <div class="adm-form-grid-3">
                                        <div class="tt-form-group">
                                            <label for="competition_name">Nom de la competition</label>
                                            <input class="tt-form-control" id="competition_name" name="competition_name" value="{{ old('competition_name', $formContext['competition_name'] ?? '') }}" placeholder="RLCS Open, VCT France...">
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="competition_stage">Phase du tournoi</label>
                                            <input class="tt-form-control" id="competition_stage" name="competition_stage" value="{{ old('competition_stage', $formContext['competition_stage'] ?? '') }}" placeholder="Open qualifier, Swiss, Playoffs...">
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="competition_split">Split / label</label>
                                            <input class="tt-form-control" id="competition_split" name="competition_split" value="{{ old('competition_split', $formContext['competition_split'] ?? '') }}" placeholder="Spring 2026, Regional #1...">
                                        </div>
                                    </div>

                                    <div class="adm-form-grid-3">
                                        <div class="tt-form-group" data-tournament-only>
                                            <label for="event_name">Nom du tournoi a predire</label>
                                            <input class="tt-form-control" id="event_name" name="event_name" value="{{ old('event_name', $formContext['event_name'] ?? '') }}" placeholder="RLCS Europe Open #1">
                                        </div>

                                        <div class="tt-form-group" data-head-to-head-only>
                                            <label for="team_a_name">Equipe A</label>
                                            <input class="tt-form-control" id="team_a_name" name="team_a_name" value="{{ old('team_a_name', $formContext['team_a_name'] ?? '') }}" placeholder="ERAH Rocket League">
                                        </div>

                                        <div class="tt-form-group" data-head-to-head-only>
                                            <label for="team_b_name">Equipe B / adversaire</label>
                                            <input class="tt-form-control" id="team_b_name" name="team_b_name" value="{{ old('team_b_name', $formContext['team_b_name'] ?? '') }}" placeholder="Nom adversaire">
                                        </div>
                                    </div>

                                    <div class="adm-form-grid-3">
                                        <div class="tt-form-group" data-head-to-head-only>
                                            <label for="best_of">Format BO</label>
                                            <select class="tt-form-control" id="best_of" name="best_of" data-match-best-of data-lenis-prevent>
                                                <option value="">Aucun</option>
                                                @foreach($bestOfOptions as $bestOfValue => $bestOfLabel)
                                                    <option value="{{ $bestOfValue }}" {{ (string) old('best_of', $formContext['best_of'] ?? '') === (string) $bestOfValue ? 'selected' : '' }}>{{ $bestOfLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="tt-form-group" data-head-to-head-only>
                                            <label for="parent_match_id">Tournoi parent (facultatif)</label>
                                            <select class="tt-form-control" id="parent_match_id" name="parent_match_id" data-lenis-prevent>
                                                <option value="">Aucun parent</option>
                                                @foreach($tournamentParentOptions as $parentOption)
                                                    <option value="{{ $parentOption->id }}" {{ (string) old('parent_match_id', $formContext['parent_match_id'] ?? '') === (string) $parentOption->id ? 'selected' : '' }}>
                                                        #{{ $parentOption->id }} - {{ $parentOption->event_name ?: $parentOption->competition_name ?: 'Tournoi RL' }}{{ $parentOption->child_matches_unlocked_at ? '' : ' (phase matchs fermee)' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="tt-form-group adm-form-cta">
                                                <label>Actions</label>
                                                <div class="adm-row-actions">
                                                    <button type="button" class="tt-btn tt-btn-secondary tt-magnetic-item" data-apply-preset>
                                                        <span data-hover="Charger le preset">Charger le preset</span>
                                                    </button>
                                                </div>
                                            <p class="adm-form-cta-copy">Utilisez le preset comme base, puis ajustez les libelles, les cles et les cotes dans l editeur de marches.</p>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="adm-surface">
                                <div class="tt-heading tt-heading-lg margin-bottom-30">
                                    <h2 class="tt-heading-title tt-text-reveal">Marches et choix</h2>
                                    <p class="max-width-700 tt-anim-fadeinup text-gray">Le preset genere une base editable. Les cles techniques restent importantes pour le reglement; adaptez ensuite les libelles et les cotes au contexte du match ou du tournoi.</p>
                                </div>

                                <div id="adm-market-editor" class="adm-market-stack">
                                    @foreach($marketRows as $marketIndex => $marketRow)
                                        <article class="adm-market-card" data-market-card>
                                            <div class="adm-market-head">
                                                <h3 class="adm-surface-title" style="font-size:28px">{{ $marketRow['title'] }}</h3>
                                                <span class="adm-pill">{{ $marketRow['key'] }}</span>
                                            </div>

                                            <div class="adm-form-grid-4">
                                                <div class="tt-form-group">
                                                    <label>Cle du marche</label>
                                                    <input class="tt-form-control" name="markets[{{ $marketIndex }}][key]" value="{{ $marketRow['key'] }}" required>
                                                </div>
                                                <div class="tt-form-group adm-col-span-2">
                                                    <label>Titre visible</label>
                                                    <input class="tt-form-control" name="markets[{{ $marketIndex }}][title]" value="{{ $marketRow['title'] }}" required>
                                                </div>
                                                <div class="tt-form-group">
                                                    <label>Actif</label>
                                                    <select class="tt-form-control" name="markets[{{ $marketIndex }}][is_active]" data-lenis-prevent>
                                                        <option value="1" {{ !empty($marketRow['is_active']) ? 'selected' : '' }}>Oui</option>
                                                        <option value="0" {{ empty($marketRow['is_active']) ? 'selected' : '' }}>Non</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="adm-selection-grid">
                                                @foreach($marketRow['selections'] as $selectionIndex => $selectionRow)
                                                    <div class="adm-selection-row">
                                                        <input class="adm-inline-input" name="markets[{{ $marketIndex }}][selections][{{ $selectionIndex }}][key]" value="{{ $selectionRow['key'] }}" placeholder="key" required>
                                                        <input class="adm-inline-input" name="markets[{{ $marketIndex }}][selections][{{ $selectionIndex }}][label]" value="{{ $selectionRow['label'] }}" placeholder="label" required>
                                                        <input class="adm-inline-input" name="markets[{{ $marketIndex }}][selections][{{ $selectionIndex }}][odds]" type="number" min="1" step="0.001" value="{{ $selectionRow['odds'] }}" required>
                                                        <input class="adm-inline-input" name="markets[{{ $marketIndex }}][selections][{{ $selectionIndex }}][sort_order]" type="number" min="0" step="1" value="{{ $selectionRow['sort_order'] ?? $selectionIndex }}">
                                                        <span class="adm-pill">Choix</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </section>

                            <section class="adm-surface">
                                <div class="adm-row-actions">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Enregistrer">Enregistrer</span>
                                    </button>
                                    <a href="{{ route('admin.matches.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                        <span data-hover="Retour au listing">Retour au listing</span>
                                    </a>
                                    @if($match)
                                        <a href="{{ route('admin.matches.manage', $match->id) }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                            <span data-hover="Gerer l evenement">Gerer l evenement</span>
                                        </a>
                                    @endif
                                </div>
                            </section>
                        </div>

                        <aside class="adm-side-note">
                            <section class="adm-side-card">
                                <h3>Pattern</h3>
                                <p class="adm-meta">Match direct : on connait deja l adversaire et les predictions portent sur le resultat du match. Parcours en tournoi : on predit d abord jusqu ou ERAH ira dans le tournoi.</p>
                            </section>

                            <section class="adm-side-card">
                                <h3>Rocket League</h3>
                                <p class="adm-meta">Avant le TOP 16, utilisez le preset parcours tournoi. Une fois la phase matchs ouverte, creez les vrais matchs lies au tournoi parent en BO5 ou BO7.</p>
                            </section>

                            <section class="adm-side-card">
                                <h3>Compatibilite</h3>
                                <p class="adm-meta">Le moteur reste unique : matchs, pronostics, marches, selections, wallet et reglement continuent de fonctionner ensemble sans doublon.</p>
                            </section>

                            @if($parentMatch)
                                <section class="adm-side-card">
                                    <h3>Parent preselectionne</h3>
                                    <p class="adm-meta">#{{ $parentMatch->id }} - {{ $parentMatch->event_name ?: $parentMatch->competition_name ?: 'Tournoi RL' }}</p>
                                    <p class="adm-meta">{{ $parentMatch->child_matches_unlocked_at ? 'La phase matchs est deja ouverte sur ce tournoi.' : 'La phase matchs n est pas encore ouverte sur ce tournoi parent.' }}</p>
                                </section>
                            @endif
                        </aside>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const eventTypeField = document.querySelector('[data-match-event-type]');
            const gameKeyField = document.querySelector('[data-match-game-key]');
            const bestOfField = document.querySelector('[data-match-best-of]');
            const presetField = document.querySelector('[data-market-preset]');
            const applyPresetButton = document.querySelector('[data-apply-preset]');
            const editor = document.getElementById('adm-market-editor');
            const presetDefinitions = @json($presetDefinitions);

            const toggleSections = () => {
                const eventType = eventTypeField ? eventTypeField.value : 'head_to_head';
                document.querySelectorAll('[data-head-to-head-only]').forEach((node) => {
                    node.classList.toggle('adm-section-hidden', eventType !== 'head_to_head');
                });
                document.querySelectorAll('[data-tournament-only]').forEach((node) => {
                    node.classList.toggle('adm-section-hidden', eventType !== 'tournament_run');
                });
            };

            const suggestPreset = () => {
                if (!presetField || !gameKeyField || !eventTypeField) {
                    return;
                }

                if (eventTypeField.value === 'tournament_run') {
                    presetField.value = 'rocket_league_tournament';
                    return;
                }

                if (gameKeyField.value === 'rocket_league' && bestOfField && bestOfField.value === '7') {
                    presetField.value = 'rocket_league_bo7';
                    return;
                }

                if (gameKeyField.value === 'rocket_league' && bestOfField && bestOfField.value === '5') {
                    presetField.value = 'rocket_league_bo5';
                    return;
                }

                presetField.value = 'classic_winner';
            };

            const renderMarkets = (markets) => {
                if (!editor || !Array.isArray(markets)) {
                    return;
                }

                editor.innerHTML = '';

                markets.forEach((market, marketIndex) => {
                    const card = document.createElement('article');
                    card.className = 'adm-market-card';

                    const selectionsHtml = (market.selections || []).map((selection, selectionIndex) => `
                        <div class="adm-selection-row">
                            <input class="adm-inline-input" name="markets[${marketIndex}][selections][${selectionIndex}][key]" value="${selection.key ?? ''}" placeholder="key" required>
                            <input class="adm-inline-input" name="markets[${marketIndex}][selections][${selectionIndex}][label]" value="${selection.label ?? ''}" placeholder="label" required>
                            <input class="adm-inline-input" name="markets[${marketIndex}][selections][${selectionIndex}][odds]" type="number" min="1" step="0.001" value="${selection.odds ?? 2}" required>
                            <input class="adm-inline-input" name="markets[${marketIndex}][selections][${selectionIndex}][sort_order]" type="number" min="0" step="1" value="${selection.sort_order ?? selectionIndex}">
                            <span class="adm-pill">Choix</span>
                        </div>
                    `).join('');

                    card.innerHTML = `
                        <div class="adm-market-head">
                            <h3 class="adm-surface-title" style="font-size:28px">${market.title ?? market.key}</h3>
                            <span class="adm-pill">${market.key ?? ''}</span>
                        </div>
                        <div class="adm-form-grid-4">
                            <div class="tt-form-group">
                                <label>Cle du marche</label>
                                <input class="tt-form-control" name="markets[${marketIndex}][key]" value="${market.key ?? ''}" required>
                            </div>
                            <div class="tt-form-group adm-col-span-2">
                                <label>Titre visible</label>
                                <input class="tt-form-control" name="markets[${marketIndex}][title]" value="${market.title ?? ''}" required>
                            </div>
                            <div class="tt-form-group">
                                <label>Actif</label>
                                <select class="tt-form-control" name="markets[${marketIndex}][is_active]">
                                    <option value="1" ${(market.is_active ?? true) ? 'selected' : ''}>Oui</option>
                                    <option value="0" ${!(market.is_active ?? true) ? 'selected' : ''}>Non</option>
                                </select>
                            </div>
                        </div>
                        <div class="adm-selection-grid">${selectionsHtml}</div>
                    `;

                    editor.appendChild(card);
                });
            };

            const applyPreset = () => {
                if (!presetField) {
                    return;
                }

                const markets = presetDefinitions[presetField.value] || [];
                renderMarkets(markets);
            };

            toggleSections();

            if (eventTypeField) {
                eventTypeField.addEventListener('change', () => {
                    toggleSections();
                    suggestPreset();
                });
            }

            if (gameKeyField) {
                gameKeyField.addEventListener('change', suggestPreset);
            }

            if (bestOfField) {
                bestOfField.addEventListener('change', suggestPreset);
            }

            if (applyPresetButton) {
                applyPresetButton.addEventListener('click', applyPreset);
            }
        });
    </script>
@endsection
