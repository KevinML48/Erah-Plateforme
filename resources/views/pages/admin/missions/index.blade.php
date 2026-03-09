@extends('marketing.layouts.template')

@section('title', 'Admin Missions | ERAH Plateforme')
@section('meta_description', 'Gestion simplifiee des templates missions et des generations.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $scopes = $scopes ?? [];
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Admin Missions',
        'heroDescription' => 'Creation et gestion simplifiees des templates missions.',
        'heroMaskDescription' => 'Vue operationnelle missions quotidiennes et hebdomadaires.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Actions rapides</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Regeneration manuelle des missions en cours pour toute la base utilisateur.</p>
                        </div>

                        <div class="adm-row-actions">
                            <form method="POST" action="{{ route('missions.generate.daily') }}">
                                @csrf
                                <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                    <span data-hover="Generer daily">Generer daily</span>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('missions.generate.weekly') }}">
                                @csrf
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Generer weekly">Generer weekly</span>
                                </button>
                            </form>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Nouveau template mission</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Version simplifiee: seuls les champs essentiels sont visibles, les options avancees sont repliables.</p>
                        </div>

                        <form method="POST" action="{{ route('admin.missions.store') }}" class="tt-form tt-form-creative adm-form">
                            @csrf

                            <div class="adm-form-grid-3">
                                <div class="tt-form-group">
                                    <label for="mission_title">Titre</label>
                                    <input class="tt-form-control" id="mission_title" name="title" value="{{ old('title') }}" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="mission_key">Key</label>
                                    <input class="tt-form-control" id="mission_key" name="key" value="{{ old('key') }}" placeholder="mission.daily.win" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="mission_event_type">Type evenement</label>
                                    <input class="tt-form-control" id="mission_event_type" name="event_type" value="{{ old('event_type', 'clip.like') }}" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="mission_target_count">Objectif</label>
                                    <input class="tt-form-control" id="mission_target_count" type="number" name="target_count" min="1" value="{{ old('target_count', 1) }}" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="mission_scope">Scope</label>
                                    <select class="tt-form-control" id="mission_scope" name="scope" required data-lenis-prevent>
                                        @foreach($scopes as $scope)
                                            <option value="{{ $scope }}" {{ old('scope') === $scope ? 'selected' : '' }}>{{ $scope }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="tt-form-group" style="align-self:end;">
                                    <div class="tt-form-check">
                                        <input type="checkbox" id="mission_is_active" name="is_active" value="1" @checked(old('is_active', true))>
                                        <label for="mission_is_active">Actif</label>
                                    </div>
                                </div>
                            </div>

                            <details class="adm-advanced">
                                <summary>Options avancees (facultatif)</summary>
                                <div class="adm-advanced-body">
                                    <div class="adm-form-grid-3">
                                        <div class="tt-form-group adm-col-span-3">
                                            <label for="mission_description">Description</label>
                                            <textarea class="tt-form-control" id="mission_description" name="description" rows="3">{{ old('description') }}</textarea>
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="mission_start_at">Debut</label>
                                            <input class="tt-form-control" id="mission_start_at" type="datetime-local" name="start_at" value="{{ old('start_at') }}">
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="mission_end_at">Fin</label>
                                            <input class="tt-form-control" id="mission_end_at" type="datetime-local" name="end_at" value="{{ old('end_at') }}">
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="mission_rewards_xp">XP</label>
                                            <input class="tt-form-control" id="mission_rewards_xp" type="number" name="rewards_xp" min="0" value="{{ old('rewards_xp', 0) }}">
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="mission_rewards_rank_points">Rank points</label>
                                            <input class="tt-form-control" id="mission_rewards_rank_points" type="number" name="rewards_rank_points" min="0" value="{{ old('rewards_rank_points', 0) }}">
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="mission_rewards_reward_points">Reward points</label>
                                            <input class="tt-form-control" id="mission_rewards_reward_points" type="number" name="rewards_reward_points" min="0" value="{{ old('rewards_reward_points', 0) }}">
                                        </div>

                                        <div class="tt-form-group">
                                            <label for="mission_rewards_bet_points">Bet points</label>
                                            <input class="tt-form-control" id="mission_rewards_bet_points" type="number" name="rewards_bet_points" min="0" value="{{ old('rewards_bet_points', 0) }}">
                                        </div>

                                        <div class="tt-form-group adm-col-span-3">
                                            <label for="mission_constraints_json">Constraints JSON</label>
                                            <textarea class="tt-form-control" id="mission_constraints_json" name="constraints_json" rows="2" placeholder='{"min_stake":100}'>{{ old('constraints_json') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </details>

                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Creer le template">Creer le template</span>
                            </button>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Templates existants</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Gestion rapide sur carte: modification essentielle, puis options avancees au besoin.</p>
                        </div>

                        @if($templates->count())
                            <div class="adm-mission-grid">
                                @foreach($templates as $template)
                                    @php
                                        $constraintsJson = $template->constraints ? json_encode($template->constraints, JSON_UNESCAPED_SLASHES) : '';
                                        $startAtValue = optional($template->start_at)->format('Y-m-d\\TH:i');
                                        $endAtValue = optional($template->end_at)->format('Y-m-d\\TH:i');
                                        $rewardXp = (int) ($template->rewards['xp'] ?? 0);
                                        $rewardRank = (int) ($template->rewards['rank_points'] ?? 0);
                                        $rewardReward = (int) ($template->rewards['reward_points'] ?? 0);
                                        $rewardBet = (int) ($template->rewards['bet_points'] ?? 0);
                                    @endphp

                                    <article class="adm-mission-card">
                                        <div class="adm-mission-head">
                                            <h3 class="adm-mission-title">{{ $template->title }}</h3>
                                            <span class="adm-pill">{{ $template->is_active ? 'Actif' : 'Inactif' }}</span>
                                        </div>

                                        <p class="adm-meta">{{ $template->key }} - {{ $template->event_type }}</p>

                                        <div class="adm-row-actions">
                                            <span class="adm-pill">Scope {{ $template->scope }}</span>
                                            <span class="adm-pill">Objectif {{ (int) $template->target_count }}</span>
                                            <span class="adm-pill">XP {{ $rewardXp }}</span>
                                        </div>

                                        <form method="POST" action="{{ route('admin.missions.update', $template->id) }}" class="tt-form tt-form-creative adm-form">
                                            @csrf
                                            @method('PUT')

                                            <div class="adm-form-grid">
                                                <div class="tt-form-group">
                                                    <label>Titre</label>
                                                    <input class="tt-form-control" name="title" value="{{ $template->title }}" required>
                                                </div>

                                                <div class="tt-form-group">
                                                    <label>Key</label>
                                                    <input class="tt-form-control" name="key" value="{{ $template->key }}" required>
                                                </div>

                                                <div class="tt-form-group">
                                                    <label>Type evenement</label>
                                                    <input class="tt-form-control" name="event_type" value="{{ $template->event_type }}" required>
                                                </div>

                                                <div class="tt-form-group">
                                                    <label>Objectif</label>
                                                    <input class="tt-form-control" type="number" name="target_count" min="1" value="{{ (int) $template->target_count }}" required>
                                                </div>

                                                <div class="tt-form-group">
                                                    <label>Scope</label>
                                                    <select class="tt-form-control" name="scope" required data-lenis-prevent>
                                                        @foreach($scopes as $scope)
                                                            <option value="{{ $scope }}" {{ $template->scope === $scope ? 'selected' : '' }}>{{ $scope }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="tt-form-group" style="align-self:end;">
                                                    <div class="tt-form-check">
                                                        <input type="checkbox" id="mission_active_{{ $template->id }}" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}>
                                                        <label for="mission_active_{{ $template->id }}">Actif</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="description" value="{{ $template->description }}">
                                            <input type="hidden" name="start_at" value="{{ $startAtValue }}">
                                            <input type="hidden" name="end_at" value="{{ $endAtValue }}">
                                            <input type="hidden" name="rewards_xp" value="{{ $rewardXp }}">
                                            <input type="hidden" name="rewards_rank_points" value="{{ $rewardRank }}">
                                            <input type="hidden" name="rewards_reward_points" value="{{ $rewardReward }}">
                                            <input type="hidden" name="rewards_bet_points" value="{{ $rewardBet }}">
                                            <input type="hidden" name="constraints_json" value="{{ $constraintsJson }}">

                                            <div class="adm-row-actions">
                                                <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                    <span data-hover="Mettre a jour">Mettre a jour</span>
                                                </button>
                                            </div>
                                        </form>

                                        <form method="POST" action="{{ route('admin.missions.destroy', $template->id) }}" onsubmit="return confirm('Supprimer ce template mission ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Supprimer">Supprimer</span>
                                            </button>
                                        </form>

                                        <details class="adm-advanced">
                                            <summary>Modifier les options avancees</summary>
                                            <div class="adm-advanced-body">
                                                <form method="POST" action="{{ route('admin.missions.update', $template->id) }}" class="tt-form tt-form-creative adm-form">
                                                    @csrf
                                                    @method('PUT')

                                                    <input type="hidden" name="key" value="{{ $template->key }}">
                                                    <input type="hidden" name="title" value="{{ $template->title }}">
                                                    <input type="hidden" name="event_type" value="{{ $template->event_type }}">
                                                    <input type="hidden" name="target_count" value="{{ (int) $template->target_count }}">
                                                    <input type="hidden" name="scope" value="{{ $template->scope }}">

                                                    <div class="tt-form-group">
                                                        <label>Description</label>
                                                        <textarea class="tt-form-control" name="description" rows="3">{{ $template->description }}</textarea>
                                                    </div>

                                                    <div class="adm-form-grid-3">
                                                        <div class="tt-form-group">
                                                            <label>Debut</label>
                                                            <input class="tt-form-control" type="datetime-local" name="start_at" value="{{ $startAtValue }}">
                                                        </div>

                                                        <div class="tt-form-group">
                                                            <label>Fin</label>
                                                            <input class="tt-form-control" type="datetime-local" name="end_at" value="{{ $endAtValue }}">
                                                        </div>

                                                        <div class="tt-form-group" style="align-self:end;">
                                                            <div class="tt-form-check">
                                                                <input type="checkbox" id="mission_adv_active_{{ $template->id }}" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}>
                                                                <label for="mission_adv_active_{{ $template->id }}">Actif</label>
                                                            </div>
                                                        </div>

                                                        <div class="tt-form-group">
                                                            <label>XP</label>
                                                            <input class="tt-form-control" type="number" name="rewards_xp" min="0" value="{{ $rewardXp }}">
                                                        </div>

                                                        <div class="tt-form-group">
                                                            <label>Rank points</label>
                                                            <input class="tt-form-control" type="number" name="rewards_rank_points" min="0" value="{{ $rewardRank }}">
                                                        </div>

                                                        <div class="tt-form-group">
                                                            <label>Reward points</label>
                                                            <input class="tt-form-control" type="number" name="rewards_reward_points" min="0" value="{{ $rewardReward }}">
                                                        </div>

                                                        <div class="tt-form-group">
                                                            <label>Bet points</label>
                                                            <input class="tt-form-control" type="number" name="rewards_bet_points" min="0" value="{{ $rewardBet }}">
                                                        </div>

                                                        <div class="tt-form-group adm-col-span-3">
                                                            <label>Constraints JSON</label>
                                                            <textarea class="tt-form-control" name="constraints_json" rows="2">{{ $constraintsJson }}</textarea>
                                                        </div>
                                                    </div>

                                                    <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                        <span data-hover="Sauvegarder options avancees">Sauvegarder options avancees</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </details>
                                    </article>
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $templates->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun template mission.</div>
                        @endif
                    </section>

                    @include('pages.admin.missions.partials.quizzes')
                    @include('pages.admin.missions.partials.live-codes')
                    @include('pages.admin.missions.partials.events')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection

