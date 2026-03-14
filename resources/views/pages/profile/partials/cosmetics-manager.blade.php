@php
    $profileCosmetics = $profileCosmetics ?? ['owned_by_slot' => [], 'slot_labels' => []];
    $ownedBySlot = $profileCosmetics['owned_by_slot'] ?? [];
    $slotLabels = $profileCosmetics['slot_labels'] ?? [];
    $equipRouteName = $equipRouteName ?? 'profile.cosmetics.equip';
    $hasAnyCosmetics = collect($ownedBySlot)->flatten(1)->isNotEmpty();
@endphp

<div class="profile-side-card margin-top-30">
    <h5 class="margin-bottom-10">Objets de profil</h5>
    <p class="profile-security-note">
        Les recompenses numeriques achetees dans la boutique apparaissent ici. Vous pouvez equiper un badge, un contour, une banniere, un titre ou un theme quand ils sont disponibles.
    </p>

    @if($hasAnyCosmetics)
        <div class="profile-cosmetic-slot-stack">
            @foreach($ownedBySlot as $slot => $items)
                @continue(empty($items))

                <section class="profile-cosmetic-slot">
                    <div class="profile-cosmetic-slot-head">
                        <strong>{{ $slotLabels[$slot] ?? ucfirst(str_replace('_', ' ', $slot)) }}</strong>
                        <span>{{ count($items) }} debloque(s)</span>
                    </div>

                    <div class="profile-cosmetic-grid">
                        @foreach($items as $item)
                            @php($preview = $item['preview'] ?? [])
                            <article class="profile-cosmetic-card {{ !empty($item['is_equipped']) ? 'is-equipped' : '' }} {{ !empty($item['is_expired']) ? 'is-expired' : '' }}">
                                <div class="profile-cosmetic-copy">
                                    <div class="profile-cosmetic-topline">
                                        <span
                                            class="profile-cosmetic-preview"
                                            @if(!empty($preview['pill_background']) || !empty($preview['pill_color']))
                                                style="
                                                    {{ !empty($preview['pill_background']) ? 'background: '.$preview['pill_background'].';' : '' }}
                                                    {{ !empty($preview['pill_color']) ? 'color: '.$preview['pill_color'].';' : '' }}
                                                "
                                            @endif
                                        >
                                            {{ $slotLabels[$slot] ?? 'Profil' }}
                                        </span>
                                        @if(!empty($item['is_equipped']))
                                            <span class="profile-cosmetic-state">Equipe</span>
                                        @elseif(!empty($item['is_expired']))
                                            <span class="profile-cosmetic-state is-expired">Expire</span>
                                        @elseif($slot === 'profile_featured')
                                            <span class="profile-cosmetic-state is-active">Actif</span>
                                        @endif
                                    </div>

                                    <strong>{{ $item['label'] }}</strong>
                                    <p>{{ $item['description'] ?: 'Objet de profil debloque dans la boutique.' }}</p>

                                    @if(!empty($item['expires_label']))
                                        <div class="profile-cosmetic-meta">Actif jusqu au {{ $item['expires_label'] }}</div>
                                    @elseif($slot === 'profile_featured')
                                        <div class="profile-cosmetic-meta">Activation immediate sur votre profil.</div>
                                    @endif
                                </div>

                                @if(empty($item['is_expired']) && empty($item['is_equipped']) && $slot !== 'profile_featured')
                                    <form method="POST" action="{{ route($equipRouteName, $item['id']) }}">
                                        @csrf
                                        <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                            <span data-hover="Equiper">Equiper</span>
                                        </button>
                                    </form>
                                @elseif(!empty($item['is_equipped']))
                                    <span class="profile-cosmetic-helper">Actif sur votre profil.</span>
                                @elseif(!empty($item['is_expired']))
                                    <span class="profile-cosmetic-helper">Renouvelez cet objet depuis la boutique si vous souhaitez le reactiver.</span>
                                @else
                                    <span class="profile-cosmetic-helper">Effet temporaire applique automatiquement au profil.</span>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @else
        <div class="profile-mission-empty">
            Aucun objet de profil debloque pour le moment. Les cadeaux numeriques achetes apparaitront ici automatiquement.
        </div>
    @endif
</div>
