<section class="adm-surface">
    <div class="tt-heading tt-heading-lg margin-bottom-20">
        <h2 class="tt-heading-title tt-text-reveal">Codes live</h2>
        <p class="max-width-700 tt-anim-fadeinup text-gray">Generation manuelle, expiration, usages et rewards.</p>
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
                <label>Statut</label>
                <select class="tt-form-control" name="status">
                    <option value="draft">draft</option>
                    <option value="published">published</option>
                    <option value="hidden">hidden</option>
                </select>
            </div>
            <div class="tt-form-group">
                <label>XP reward</label>
                <input class="tt-form-control" type="number" name="xp_reward" min="0" value="40">
            </div>
            <div class="tt-form-group">
                <label>Points plateforme</label>
                <input class="tt-form-control" type="number" name="reward_points" min="0" value="60">
            </div>
            <div class="tt-form-group">
                <label>Points paris legacy</label>
                <input class="tt-form-control" type="number" name="bet_points" min="0" value="0">
            </div>
            <div class="tt-form-group">
                <label>Usage limit</label>
                <input class="tt-form-control" type="number" name="usage_limit" min="1">
            </div>
            <div class="tt-form-group">
                <label>Per user limit</label>
                <input class="tt-form-control" type="number" name="per_user_limit" min="1" value="1">
            </div>
            <div class="tt-form-group">
                <label>Expire le</label>
                <input class="tt-form-control" type="datetime-local" name="expires_at">
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
                    <div class="adm-row-actions">
                        <span class="adm-pill">+{{ (int) $code->xp_reward }} XP</span>
                        <span class="adm-pill">+{{ (int) $code->reward_points }} pts</span>
                        <span class="adm-pill">{{ (int) $code->redemptions_count }} redemption(s)</span>
                    </div>
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
