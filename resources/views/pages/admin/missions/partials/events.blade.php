<section class="adm-surface">
    <div class="tt-heading tt-heading-lg margin-bottom-20">
        <h2 class="tt-heading-title tt-text-reveal">Evenements dynamiques</h2>
        <p class="max-width-700 tt-anim-fadeinup text-gray">Double XP global, bonus duel ou bonus clips avec fenetre temporelle.</p>
    </div>

    <form method="POST" action="{{ route('admin.events.store') }}" class="tt-form tt-form-creative adm-form">
        @csrf
        <div class="adm-form-grid-3">
            <div class="tt-form-group">
                <label>Key</label>
                <input class="tt-form-control" name="key" required placeholder="double-xp-weekend">
            </div>
            <div class="tt-form-group">
                <label>Titre</label>
                <input class="tt-form-control" name="title" required>
            </div>
            <div class="tt-form-group">
                <label>Type</label>
                <select class="tt-form-control" name="type">
                    <option value="double_xp">double_xp</option>
                    <option value="bonus_duel">bonus_duel</option>
                    <option value="bonus_clips">bonus_clips</option>
                </select>
            </div>
            <div class="tt-form-group">
                <label>Statut</label>
                <select class="tt-form-control" name="status">
                    <option value="draft">draft</option>
                    <option value="published">published</option>
                    <option value="hidden">hidden</option>
                </select>
            </div>
            <div class="tt-form-group">
                <label>Debut</label>
                <input class="tt-form-control" type="datetime-local" name="starts_at">
            </div>
            <div class="tt-form-group">
                <label>Fin</label>
                <input class="tt-form-control" type="datetime-local" name="ends_at">
            </div>
            <div class="tt-form-group adm-col-span-3">
                <label>Description</label>
                <textarea class="tt-form-control" name="description" rows="2"></textarea>
            </div>
            <div class="tt-form-group adm-col-span-3">
                <label>Config JSON</label>
                <textarea class="tt-form-control" name="config" rows="4">{"xp_multiplier":2}</textarea>
            </div>
            <div class="tt-form-group">
                <div class="tt-form-check">
                    <input type="checkbox" id="event_is_active" name="is_active" value="1" checked>
                    <label for="event_is_active">Actif</label>
                </div>
            </div>
        </div>

        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
            <span data-hover="Creer l evenement">Creer l evenement</span>
        </button>
    </form>

    @if(($events ?? collect())->count())
        <div class="adm-mission-grid margin-top-30">
            @foreach($events as $event)
                <article class="adm-mission-card">
                    <div class="adm-mission-head">
                        <h3 class="adm-mission-title">{{ $event->title }}</h3>
                        <span class="adm-pill">{{ $event->status }}</span>
                    </div>
                    <p class="adm-meta">{{ $event->key }} - {{ $event->type }}</p>
                    <div class="adm-row-actions">
                        <span class="adm-pill">{{ $event->is_active ? 'Actif' : 'Inactif' }}</span>
                        <span class="adm-pill">{{ optional($event->starts_at)->format('d/m H:i') ?: 'Maintenant' }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" onsubmit="return confirm('Supprimer cet evenement ?');">
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
