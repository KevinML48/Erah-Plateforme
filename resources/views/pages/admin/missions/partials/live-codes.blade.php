<section class="adm-surface">
    @php($liveCodeMissionTemplates = collect($liveCodeMissionTemplates ?? []))
    <div class="tt-heading tt-heading-lg margin-bottom-20">
        <h2 class="tt-heading-title tt-text-reveal">Codes live</h2>
        <p class="max-width-700 tt-anim-fadeinup text-gray">Generation manuelle, expiration, usages, recompenses et liaison directe avec une mission a valider pendant un live.</p>
    </div>

    <form method="POST" action="{{ route('admin.live-codes.store') }}" class="tt-form tt-form-creative adm-form">
        @csrf
        <div class="adm-form-grid-3">
            <div class="tt-form-group">
                <label>Code</label>
                <input class="tt-form-control" name="code" placeholder="ERAH2026">
            </div>
            <div class="tt-form-group">
                <label>Label</label>
                <input class="tt-form-control" name="label" required>
            </div>
            <div class="tt-form-group">
                <label>Mission associee</label>
                <select class="tt-form-control" name="mission_template_id" data-lenis-prevent>
                    <option value="">Aucune mission liee</option>
                    @foreach($liveCodeMissionTemplates as $template)
                        <option value="{{ $template->id }}">
                            {{ $template->title }} · {{ $template->scope }} · {{ $template->event_type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="tt-form-group">
                <label>Statut</label>
                <select class="tt-form-control" name="status">
                    <option value="draft">Brouillon</option>
                    <option value="published">Publie</option>
                    <option value="hidden">Masque</option>
                </select>
            </div>
            <div class="tt-form-group">
                <label>XP accordee</label>
                <input class="tt-form-control" type="number" name="xp_reward" min="0" value="40">
            </div>
            <div class="tt-form-group">
                <label>Points plateforme</label>
                <input class="tt-form-control" type="number" name="reward_points" min="0" value="60">
            </div>
            <div class="tt-form-group">
                <label>Points paris (compatibilite)</label>
                <input class="tt-form-control" type="number" name="bet_points" min="0" value="0">
            </div>
            <div class="tt-form-group">
                <label>Limite d usage</label>
                <input class="tt-form-control" type="number" name="usage_limit" min="1">
            </div>
            <div class="tt-form-group">
                <label>Limite par membre</label>
                <input class="tt-form-control" type="number" name="per_user_limit" min="1" value="1">
            </div>
            <div class="tt-form-group">
                <label>Expire le</label>
                <input class="tt-form-control" type="datetime-local" name="expires_at">
            </div>
            <div class="tt-form-group adm-col-span-3">
                <label>Description</label>
                <textarea class="tt-form-control" name="description" rows="2" placeholder="Visible sur la page codes live et utile pour preciser le contexte du direct."></textarea>
            </div>
        </div>

        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
            <span data-hover="Creer le code">Creer le code</span>
        </button>
    </form>

    @if(($liveCodes ?? collect())->count())
        <div class="adm-mission-grid margin-top-30">
            @foreach($liveCodes as $code)
                <article class="adm-mission-card">
                    <div class="adm-mission-head">
                        <h3 class="adm-mission-title">{{ $code->code }}</h3>
                        <span class="adm-pill">{{ $code->status }}</span>
                    </div>
                    <p class="adm-meta">{{ $code->label }}</p>
                    @if($code->missionTemplate)
                        <p class="adm-meta">Mission liee : <strong>{{ $code->missionTemplate->title }}</strong> · {{ $code->missionTemplate->event_type }}</p>
                    @endif
                    <div class="adm-row-actions">
                        <span class="adm-pill">+{{ (int) $code->xp_reward }} XP</span>
                        <span class="adm-pill">+{{ (int) $code->reward_points }} pts</span>
                        <span class="adm-pill">{{ (int) $code->redemptions_count }} utilisation(s)</span>
                    </div>
                    <details class="margin-top-20">
                        <summary class="tt-btn tt-btn-secondary tt-magnetic-item" style="display:inline-flex;">
                            <span data-hover="Modifier">Modifier le code</span>
                        </summary>
                        <form method="POST" action="{{ route('admin.live-codes.update', $code->id) }}" class="tt-form tt-form-creative adm-form margin-top-20">
                            @csrf
                            @method('PUT')
                            <div class="adm-form-grid-3">
                                <div class="tt-form-group">
                                    <label>Code</label>
                                    <input class="tt-form-control" name="code" value="{{ $code->code }}">
                                </div>
                                <div class="tt-form-group">
                                    <label>Label</label>
                                    <input class="tt-form-control" name="label" value="{{ $code->label }}" required>
                                </div>
                                <div class="tt-form-group">
                                    <label>Mission associee</label>
                                    <select class="tt-form-control" name="mission_template_id" data-lenis-prevent>
                                        <option value="">Aucune mission liee</option>
                                        @foreach($liveCodeMissionTemplates as $template)
                                            <option value="{{ $template->id }}" @selected((int) $code->mission_template_id === (int) $template->id)>
                                                {{ $template->title }} · {{ $template->scope }} · {{ $template->event_type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tt-form-group">
                                    <label>Statut</label>
                                    <select class="tt-form-control" name="status" data-lenis-prevent>
                                        <option value="draft" @selected($code->status === 'draft')>Brouillon</option>
                                        <option value="published" @selected($code->status === 'published')>Publie</option>
                                        <option value="hidden" @selected($code->status === 'hidden')>Masque</option>
                                    </select>
                                </div>
                                <div class="tt-form-group">
                                    <label>XP accordee</label>
                                    <input class="tt-form-control" type="number" name="xp_reward" min="0" value="{{ (int) $code->xp_reward }}">
                                </div>
                                <div class="tt-form-group">
                                    <label>Points plateforme</label>
                                    <input class="tt-form-control" type="number" name="reward_points" min="0" value="{{ (int) $code->reward_points }}">
                                </div>
                                <div class="tt-form-group">
                                    <label>Points paris</label>
                                    <input class="tt-form-control" type="number" name="bet_points" min="0" value="{{ (int) $code->bet_points }}">
                                </div>
                                <div class="tt-form-group">
                                    <label>Limite d usage</label>
                                    <input class="tt-form-control" type="number" name="usage_limit" min="1" value="{{ $code->usage_limit }}">
                                </div>
                                <div class="tt-form-group">
                                    <label>Limite par membre</label>
                                    <input class="tt-form-control" type="number" name="per_user_limit" min="1" value="{{ (int) $code->per_user_limit }}">
                                </div>
                                <div class="tt-form-group adm-col-span-3">
                                    <label>Description</label>
                                    <textarea class="tt-form-control" name="description" rows="2">{{ $code->description }}</textarea>
                                </div>
                                <div class="tt-form-group">
                                    <label>Expire le</label>
                                    <input class="tt-form-control" type="datetime-local" name="expires_at" value="{{ optional($code->expires_at)->format('Y-m-d\\TH:i') }}">
                                </div>
                            </div>
                            <div class="adm-row-actions">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Mettre a jour">Mettre a jour</span>
                                </button>
                            </div>
                        </form>
                    </details>
                    <form method="POST" action="{{ route('admin.live-codes.destroy', $code->id) }}" onsubmit="return confirm('Supprimer ce code ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                            <span data-hover="Supprimer">Supprimer</span>
                        </button>
                    </form>
                </article>
            @endforeach
        </div>
    @endif
</section>
