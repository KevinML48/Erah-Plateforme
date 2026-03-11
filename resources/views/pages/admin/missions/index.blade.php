@extends('marketing.layouts.template')
@section('title', 'Admin Missions | ERAH Plateforme')
@section('meta_description', 'Pilotage des templates missions et des generations.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')
@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $scopes = $scopes ?? [];
        $overview = $overview ?? [];
        $filters = $filters ?? ['scope' => 'all', 'status' => 'all', 'category' => '', 'difficulty' => ''];
        $categories = collect($categories ?? []);
        $difficultyOptions = ['simple' => 'Simple', 'medium' => 'Moyenne', 'special' => 'Speciale', 'hard' => 'Difficile'];
    @endphp

    @include('pages.admin.partials.hero', ['heroSubtitle' => 'Administration ERAH', 'heroTitle' => 'Pilotage missions', 'heroDescription' => 'Templates, generation et maintenance du moteur missions.', 'heroMaskDescription' => 'Fondation XP + points prete pour accueillir le futur catalogue.'])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="adm-kpi-grid">
                            <article class="adm-kpi-card"><strong>{{ (int) ($overview['active_templates'] ?? 0) }}</strong><span>Templates actifs</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) ($overview['active_daily_templates'] ?? 0) }}</strong><span>Daily actives</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) ($overview['missions_completed_today'] ?? 0) }}</strong><span>Completees aujourd hui</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) (($overview['active_live_codes'] ?? 0) + ($overview['active_events'] ?? 0)) }}</strong><span>Live codes + events</span></article>
                        </div>
                        <div class="adm-row-actions" style="margin-top:14px;">
                            <form method="POST" action="{{ route('missions.generate.daily') }}">@csrf<button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Quotidiennes">Regenerer les quotidiennes</span></button></form>
                            <form method="POST" action="{{ route('missions.generate.weekly') }}">@csrf<button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Hebdomadaires">Regenerer les hebdomadaires</span></button></form>
                            <form method="POST" action="{{ route('missions.generate.event-window') }}">@csrf<button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Fenetre">Regenerer la fenetre evenement</span></button></form>
                            <form method="POST" action="{{ route('missions.repair') }}">@csrf<button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Reparer">Reparer et resynchroniser</span></button></form>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <form method="GET" action="{{ route('admin.missions.index') }}" class="tt-form tt-form-creative adm-form">
                            <div class="adm-form-grid-4">
                                <div class="tt-form-group"><label>Scope</label><select class="tt-form-control" name="scope" data-lenis-prevent><option value="all">Tous</option>@foreach($scopes as $scope)<option value="{{ $scope }}" {{ ($filters['scope'] ?? 'all') === $scope ? 'selected' : '' }}>{{ $scope }}</option>@endforeach</select></div>
                                <div class="tt-form-group"><label>Statut</label><select class="tt-form-control" name="status" data-lenis-prevent><option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>Tous</option><option value="active" {{ ($filters['status'] ?? 'all') === 'active' ? 'selected' : '' }}>Actif</option><option value="inactive" {{ ($filters['status'] ?? 'all') === 'inactive' ? 'selected' : '' }}>Inactif</option></select></div>
                                <div class="tt-form-group"><label>Categorie</label><select class="tt-form-control" name="category" data-lenis-prevent><option value="">Toutes</option>@foreach($categories as $category)<option value="{{ $category }}" {{ ($filters['category'] ?? '') === $category ? 'selected' : '' }}>{{ $category }}</option>@endforeach</select></div>
                                <div class="tt-form-group"><label>Difficulte</label><select class="tt-form-control" name="difficulty" data-lenis-prevent><option value="">Toutes</option>@foreach($difficultyOptions as $difficultyKey => $difficultyLabel)<option value="{{ $difficultyKey }}" {{ ($filters['difficulty'] ?? '') === $difficultyKey ? 'selected' : '' }}>{{ $difficultyLabel }}</option>@endforeach</select></div>
                            </div>
                            <div class="adm-row-actions"><button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Filtrer">Filtrer</span></button><a href="{{ route('admin.missions.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Reinitialiser">Reinitialiser</span></a></div>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20"><h2 class="tt-heading-title tt-text-reveal">Creer un template mission</h2><p class="max-width-700 tt-anim-fadeinup text-gray">Format final strict: rewards = xp + points.</p></div>
                        <form method="POST" action="{{ route('admin.missions.store') }}" class="tt-form tt-form-creative adm-form">
                            @csrf
                            <div class="adm-form-grid-4">
                                <div class="tt-form-group"><label>Titre</label><input class="tt-form-control" name="title" value="{{ old('title') }}" required></div>
                                <div class="tt-form-group"><label>Key</label><input class="tt-form-control" name="key" value="{{ old('key') }}" required></div>
                                <div class="tt-form-group"><label>Categorie</label><input class="tt-form-control" name="category" value="{{ old('category', 'community') }}"></div>
                                <div class="tt-form-group"><label>Type</label><input class="tt-form-control" name="type" value="{{ old('type', 'repeatable') }}"></div>
                                <div class="tt-form-group"><label>Event type</label><input class="tt-form-control" name="event_type" value="{{ old('event_type', 'clip.comment') }}" required></div>
                                <div class="tt-form-group"><label>Scope</label><select class="tt-form-control" name="scope" required data-lenis-prevent>@foreach($scopes as $scope)<option value="{{ $scope }}" {{ old('scope') === $scope ? 'selected' : '' }}>{{ $scope }}</option>@endforeach</select></div>
                                <div class="tt-form-group"><label>Objectif</label><input class="tt-form-control" type="number" name="target_count" min="1" value="{{ old('target_count', 1) }}" required></div>
                                <div class="tt-form-group"><label>Ordre</label><input class="tt-form-control" type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"></div>
                                <div class="tt-form-group"><label>XP</label><input class="tt-form-control" type="number" name="rewards_xp" min="0" value="{{ old('rewards_xp', 0) }}"></div>
                                <div class="tt-form-group"><label>Points</label><input class="tt-form-control" type="number" name="rewards_points" min="0" value="{{ old('rewards_points', 0) }}"></div>
                                <div class="tt-form-group"><label>Difficulte</label><select class="tt-form-control" name="difficulty" data-lenis-prevent><option value="">Aucune</option>@foreach($difficultyOptions as $difficultyKey => $difficultyLabel)<option value="{{ $difficultyKey }}" {{ old('difficulty') === $difficultyKey ? 'selected' : '' }}>{{ $difficultyLabel }}</option>@endforeach</select></div>
                                <div class="tt-form-group"><label>Duree estimee</label><input class="tt-form-control" type="number" name="estimated_minutes" min="1" value="{{ old('estimated_minutes') }}"></div>
                                <div class="tt-form-group"><label>Icone</label><input class="tt-form-control" name="icon" value="{{ old('icon') }}"></div>
                                <div class="tt-form-group"><label>Badge</label><input class="tt-form-control" name="badge_label" value="{{ old('badge_label') }}"></div>
                                <div class="tt-form-group adm-col-span-2"><label>Description courte</label><textarea class="tt-form-control" name="short_description" rows="2">{{ old('short_description') }}</textarea></div>
                                <div class="tt-form-group adm-col-span-2"><label>Description detaillee</label><textarea class="tt-form-control" name="description" rows="2">{{ old('description') }}</textarea></div>
                                <div class="tt-form-group adm-col-span-4"><label>Description longue</label><textarea class="tt-form-control" name="long_description" rows="3">{{ old('long_description') }}</textarea></div>
                                <div class="tt-form-group adm-col-span-2"><label>Contraintes JSON</label><textarea class="tt-form-control" name="constraints_json" rows="2">{{ old('constraints_json') }}</textarea></div>
                                <div class="tt-form-group adm-col-span-2"><label>Prerequis JSON</label><textarea class="tt-form-control" name="prerequisites_json" rows="2">{{ old('prerequisites_json') }}</textarea></div>
                                <div class="tt-form-group adm-col-span-4"><label>UI meta JSON</label><textarea class="tt-form-control" name="ui_meta_json" rows="2">{{ old('ui_meta_json') }}</textarea></div>
                                <div class="tt-form-group"><label>Debut</label><input class="tt-form-control" type="datetime-local" name="start_at" value="{{ old('start_at') }}"></div>
                                <div class="tt-form-group"><label>Fin</label><input class="tt-form-control" type="datetime-local" name="end_at" value="{{ old('end_at') }}"></div>
                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_is_discovery" name="is_discovery" value="1" @checked(old('is_discovery'))><label for="mission_is_discovery">Mise en avant decouverte</label></div></div>
                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_is_featured" name="is_featured" value="1" @checked(old('is_featured'))><label for="mission_is_featured">Mise en avant</label></div></div>
                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_is_repeatable" name="is_repeatable" value="1" @checked(old('is_repeatable'))><label for="mission_is_repeatable">Rejouable</label></div></div>
                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_requires_claim" name="requires_claim" value="1" @checked(old('requires_claim'))><label for="mission_requires_claim">Reclamation manuelle</label></div></div>
                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_is_active" name="is_active" value="1" @checked(old('is_active', true))><label for="mission_is_active">Actif</label></div></div>
                            </div>
                            <div class="adm-row-actions"><button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Creer">Creer le template</span></button></div>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20"><h2 class="tt-heading-title tt-text-reveal">Templates existants</h2><p class="max-width-700 tt-anim-fadeinup text-gray">Edition rapide et preview du catalogue actuel.</p></div>
                        @if($templates->count())
                            <div class="adm-mission-grid">
                                @foreach($templates as $template)
                                    @php $rewards = $template->normalizedRewards(); $constraintsJson = $template->constraints ? json_encode($template->constraints, JSON_UNESCAPED_SLASHES) : ''; $prerequisitesJson = $template->prerequisites ? json_encode($template->prerequisites, JSON_UNESCAPED_SLASHES) : ''; $uiMetaJson = $template->ui_meta ? json_encode($template->ui_meta, JSON_UNESCAPED_SLASHES) : ''; @endphp
                                    <article class="adm-mission-card">
                                        <div class="adm-mission-head"><div><h3 class="adm-mission-title">{{ $template->title }}</h3><p class="adm-meta">{{ $template->key }} - {{ $template->event_type }}</p></div><span class="adm-pill">{{ $template->is_active ? 'Actif' : 'Inactif' }}</span></div>
                                        <div class="adm-row-actions"><span class="adm-pill">{{ $template->scope }}</span><span class="adm-pill">{{ $template->category ?: 'general' }}</span><span class="adm-pill">XP {{ (int) $rewards['xp'] }}</span><span class="adm-pill">Points {{ (int) $rewards['points'] }}</span><span class="adm-pill">Objectif {{ (int) $template->target_count }}</span>@if($template->requires_claim)<span class="adm-pill">Reclamation manuelle</span>@endif</div>
                                        <form method="POST" action="{{ route('admin.missions.update', $template->id) }}" class="tt-form tt-form-creative adm-form">
                                            @csrf @method('PUT')
                                            <div class="adm-form-grid-4">
                                                <div class="tt-form-group"><label>Titre</label><input class="tt-form-control" name="title" value="{{ $template->title }}" required></div>
                                                <div class="tt-form-group"><label>Key</label><input class="tt-form-control" name="key" value="{{ $template->key }}" required></div>
                                                <div class="tt-form-group"><label>Categorie</label><input class="tt-form-control" name="category" value="{{ $template->category }}"></div>
                                                <div class="tt-form-group"><label>Type</label><input class="tt-form-control" name="type" value="{{ $template->type }}"></div>
                                                <div class="tt-form-group"><label>Event type</label><input class="tt-form-control" name="event_type" value="{{ $template->event_type }}" required></div>
                                                <div class="tt-form-group"><label>Scope</label><select class="tt-form-control" name="scope" required data-lenis-prevent>@foreach($scopes as $scope)<option value="{{ $scope }}" {{ $template->scope === $scope ? 'selected' : '' }}>{{ $scope }}</option>@endforeach</select></div>
                                                <div class="tt-form-group"><label>Objectif</label><input class="tt-form-control" type="number" name="target_count" min="1" value="{{ (int) $template->target_count }}" required></div>
                                                <div class="tt-form-group"><label>Ordre</label><input class="tt-form-control" type="number" name="sort_order" min="0" value="{{ (int) $template->sort_order }}"></div>
                                                <div class="tt-form-group"><label>XP</label><input class="tt-form-control" type="number" name="rewards_xp" min="0" value="{{ (int) $rewards['xp'] }}"></div>
                                                <div class="tt-form-group"><label>Points</label><input class="tt-form-control" type="number" name="rewards_points" min="0" value="{{ (int) $rewards['points'] }}"></div>
                                                <div class="tt-form-group"><label>Difficulte</label><select class="tt-form-control" name="difficulty" data-lenis-prevent><option value="">Aucune</option>@foreach($difficultyOptions as $difficultyKey => $difficultyLabel)<option value="{{ $difficultyKey }}" {{ $template->difficulty === $difficultyKey ? 'selected' : '' }}>{{ $difficultyLabel }}</option>@endforeach</select></div>
                                                <div class="tt-form-group"><label>Duree estimee</label><input class="tt-form-control" type="number" name="estimated_minutes" min="1" value="{{ $template->estimated_minutes }}"></div>
                                                <div class="tt-form-group"><label>Icone</label><input class="tt-form-control" name="icon" value="{{ $template->icon }}"></div>
                                                <div class="tt-form-group"><label>Badge</label><input class="tt-form-control" name="badge_label" value="{{ $template->badge_label }}"></div>
                                                <div class="tt-form-group adm-col-span-2"><label>Description courte</label><textarea class="tt-form-control" name="short_description" rows="2">{{ $template->short_description }}</textarea></div>
                                                <div class="tt-form-group adm-col-span-2"><label>Description</label><textarea class="tt-form-control" name="description" rows="2">{{ $template->description }}</textarea></div>
                                                <div class="tt-form-group adm-col-span-4"><label>Description longue</label><textarea class="tt-form-control" name="long_description" rows="3">{{ $template->long_description }}</textarea></div>
                                                <div class="tt-form-group adm-col-span-2"><label>Contraintes JSON</label><textarea class="tt-form-control" name="constraints_json" rows="2">{{ $constraintsJson }}</textarea></div>
                                                <div class="tt-form-group adm-col-span-2"><label>Prerequis JSON</label><textarea class="tt-form-control" name="prerequisites_json" rows="2">{{ $prerequisitesJson }}</textarea></div>
                                                <div class="tt-form-group adm-col-span-4"><label>UI meta JSON</label><textarea class="tt-form-control" name="ui_meta_json" rows="2">{{ $uiMetaJson }}</textarea></div>
                                                <div class="tt-form-group"><label>Debut</label><input class="tt-form-control" type="datetime-local" name="start_at" value="{{ optional($template->start_at)->format('Y-m-d\\TH:i') }}"></div>
                                                <div class="tt-form-group"><label>Fin</label><input class="tt-form-control" type="datetime-local" name="end_at" value="{{ optional($template->end_at)->format('Y-m-d\\TH:i') }}"></div>
                                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_active_{{ $template->id }}" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}><label for="mission_active_{{ $template->id }}">Actif</label></div></div>
                                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_discovery_{{ $template->id }}" name="is_discovery" value="1" {{ $template->is_discovery ? 'checked' : '' }}><label for="mission_discovery_{{ $template->id }}">Mise en avant decouverte</label></div></div>
                                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_featured_{{ $template->id }}" name="is_featured" value="1" {{ $template->is_featured ? 'checked' : '' }}><label for="mission_featured_{{ $template->id }}">Mise en avant</label></div></div>
                                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_repeatable_{{ $template->id }}" name="is_repeatable" value="1" {{ $template->is_repeatable ? 'checked' : '' }}><label for="mission_repeatable_{{ $template->id }}">Rejouable</label></div></div>
                                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="mission_claim_{{ $template->id }}" name="requires_claim" value="1" {{ $template->requires_claim ? 'checked' : '' }}><label for="mission_claim_{{ $template->id }}">Reclamation manuelle</label></div></div>
                                            </div>
                                            <div class="adm-row-actions"><button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Mettre a jour">Mettre a jour</span></button></div>
                                        </form>
                                        <form method="POST" action="{{ route('admin.missions.destroy', $template->id) }}" onsubmit="return confirm('Supprimer ce template mission ?');">@csrf @method('DELETE')<button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Supprimer">Supprimer</span></button></form>
                                    </article>
                                @endforeach
                            </div>
                            <div class="adm-pagin">{{ $templates->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun template mission ne correspond au filtre courant.</div>
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
