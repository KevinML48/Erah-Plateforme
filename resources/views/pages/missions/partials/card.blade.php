@php
    $templateId = (int) ($mission['template_id'] ?? 0);
    $isFocused = in_array($templateId, $focusTemplateIds ?? [], true);
    $focusStoreRoute = $missionFocusStoreRoute ?? 'missions.focus.store';
    $focusDestroyRoute = $missionFocusDestroyRoute ?? 'missions.focus.destroy';
    $claimRoute = $missionClaimRoute ?? 'missions.claim';
    $focusFull = count($focusTemplateIds ?? []) >= (int) ($focusLimit ?? 3);
@endphp

<article class="mission-surface mission-card">
    <div class="mission-card-head">
        <div>
            <span class="mission-card-kicker">{{ $mission['category'] ?? 'general' }}</span>
            <h3 class="mission-card-title">{{ $mission['title'] ?? 'Mission' }}</h3>
        </div>
        <span class="mission-status {{ $mission['status_class'] ?? '' }}">{{ $mission['status_label'] ?? 'En cours' }}</span>
    </div>

    <p class="mission-card-description">{{ $mission['short_description'] ?? '' }}</p>

    <div class="mission-card-meta">
        <span class="mission-pill">{{ $mission['scope_label'] ?? 'Mission' }}</span>
        <span class="mission-pill">{{ $mission['event_label'] ?? 'Action libre' }}</span>
        @if(!empty($mission['difficulty']))
            <span class="mission-pill">{{ $mission['difficulty'] }}</span>
        @endif
        @if((int) ($mission['estimated_minutes'] ?? 0) > 0)
            <span class="mission-pill">{{ (int) $mission['estimated_minutes'] }} min</span>
        @endif
    </div>

    <div class="mission-progress">
        <div class="mission-progress-head">
            <span>Progression</span>
            <strong>{{ (int) ($mission['progress_count'] ?? 0) }} / {{ (int) ($mission['target_count'] ?? 0) }}</strong>
        </div>
        <div class="mission-progress-track">
            <span style="width: {{ (int) ($mission['progress_percent'] ?? 0) }}%"></span>
        </div>
    </div>

    <div class="mission-reward-row">
        @if((int) ($mission['rewards']['xp'] ?? 0) > 0)
            <span class="mission-reward-chip">+{{ (int) $mission['rewards']['xp'] }} XP</span>
        @endif
        @if((int) ($mission['rewards']['points'] ?? 0) > 0)
            <span class="mission-reward-chip">+{{ (int) $mission['rewards']['points'] }} points</span>
        @endif
    </div>

    <div class="mission-card-foot">
        <div class="mission-card-meta">
            <span class="mission-pill">
                {{ optional($mission['period_start'] ?? null)->format('d/m H:i') ?? '-' }}
                ->
                {{ optional($mission['period_end'] ?? null)->format('d/m H:i') ?? '-' }}
            </span>
        </div>

        @if($templateId > 0)
            <div class="mission-inline-actions">
                @if(!empty($mission['is_claimable']) && !empty($mission['id']))
                    <form method="POST" action="{{ route($claimRoute, (int) $mission['id']) }}" class="mission-focus-form">
                        @csrf
                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                            <span data-hover="Reclamer la recompense">Reclamer la recompense</span>
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route($isFocused ? $focusDestroyRoute : $focusStoreRoute, $templateId) }}" class="mission-focus-form">
                    @csrf
                    @if($isFocused)
                        @method('DELETE')
                    @endif
                    <button type="submit" class="tt-btn {{ $isFocused ? 'tt-btn-outline' : 'tt-btn-primary' }} tt-magnetic-item" {{ (!$isFocused && $focusFull) ? 'disabled' : '' }}>
                        <span data-hover="{{ $isFocused ? 'Retirer du focus' : 'Ajouter au focus' }}">
                            {{ $isFocused ? 'Retirer du focus' : 'Ajouter au focus' }}
                        </span>
                    </button>
                </form>
            </div>
        @endif
    </div>
</article>
