@php
    $pending = $pending ?? [];
    $counts = $pending['counts'] ?? [];
    $redemptionsPending = collect($pending['redemptions_pending'] ?? []);
    $redemptionsApproved = collect($pending['redemptions_approved'] ?? []);
    $redemptionsShipped = collect($pending['redemptions_shipped'] ?? []);
    $matchesToSettle = collect($pending['matches_to_settle'] ?? []);
    $reviewsPending = collect($pending['reviews_pending'] ?? []);
    $lowStockGifts = collect($pending['low_stock_gifts'] ?? []);
    $lowStockShopItems = collect($pending['low_stock_shop_items'] ?? []);
@endphp

<div class="adm-ops-summary">
    <span class="adm-pill">Cadeaux pending {{ (int) ($counts['redemptions_pending'] ?? 0) }}</span>
    <span class="adm-pill">Cadeaux a expedier {{ (int) ($counts['redemptions_approved'] ?? 0) }}</span>
    <span class="adm-pill">Cadeaux a livrer {{ (int) ($counts['redemptions_shipped'] ?? 0) }}</span>
    <span class="adm-pill">Matchs a traiter {{ (int) ($counts['matches_to_settle'] ?? 0) }}</span>
    <span class="adm-pill">Paris a settle {{ (int) ($counts['bets_to_settle'] ?? 0) }}</span>
    <span class="adm-pill">Avis pending {{ (int) ($counts['reviews_pending'] ?? 0) }}</span>
</div>

<div class="adm-ops-grid">
    <article class="adm-ops-card">
        <h3>Demandes cadeaux en attente</h3>
        @if($redemptionsPending->isEmpty())
            <div class="adm-empty">Aucune demande pending.</div>
        @else
            <div class="adm-mini-list">
                @foreach($redemptionsPending as $item)
                    <div class="adm-mini-item">
                        <div>
                            <strong>#{{ $item->id }} - {{ $item->gift->title ?? 'Cadeau' }}</strong>
                            <p>{{ $item->user->name ?? 'User' }} · {{ $item->user->email ?? 'n/a' }} · {{ optional($item->requested_at)->format('d/m H:i') }}</p>
                        </div>
                        <div class="adm-row-actions">
                            <a href="{{ route('users.index', ['user_id' => $item->user_id]) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="User">User</span></a>
                            <form method="POST" action="{{ route('admin.redemptions.approve', $item->id) }}">
                                @csrf
                                <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Approuver">Approuver</span></button>
                            </form>
                            <form method="POST" action="{{ route('admin.redemptions.reject', $item->id) }}" class="adm-inline-form">
                                @csrf
                                <input class="adm-inline-input" name="reason" placeholder="Motif">
                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Rejeter">Rejeter</span></button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </article>

    <article class="adm-ops-card">
        <h3>Cadeaux approuves a expedier</h3>
        @if($redemptionsApproved->isEmpty())
            <div class="adm-empty">Aucune demande approuvee.</div>
        @else
            <div class="adm-mini-list">
                @foreach($redemptionsApproved as $item)
                    <div class="adm-mini-item">
                        <div>
                            <strong>#{{ $item->id }} - {{ $item->gift->title ?? 'Cadeau' }}</strong>
                            <p>{{ $item->user->name ?? 'User' }} · {{ optional($item->approved_at)->format('d/m H:i') }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.redemptions.ship', $item->id) }}" class="adm-inline-form adm-inline-form-stacked">
                            @csrf
                            <input class="adm-inline-input" name="tracking_code" placeholder="Tracking">
                            <input class="adm-inline-input" name="tracking_carrier" placeholder="Transporteur">
                            <input class="adm-inline-input" name="shipping_note" placeholder="Note">
                            <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Expedier">Expedier</span></button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </article>

    <article class="adm-ops-card">
        <h3>Cadeaux expedies a livrer</h3>
        @if($redemptionsShipped->isEmpty())
            <div class="adm-empty">Aucune demande expediee.</div>
        @else
            <div class="adm-mini-list">
                @foreach($redemptionsShipped as $item)
                    <div class="adm-mini-item">
                        <div>
                            <strong>#{{ $item->id }} - {{ $item->gift->title ?? 'Cadeau' }}</strong>
                            <p>{{ $item->tracking_carrier ?: 'Transporteur n/a' }} · {{ $item->tracking_code ?: 'Tracking n/a' }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.redemptions.deliver', $item->id) }}">
                            @csrf
                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Livrer">Livrer</span></button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </article>

    <article class="adm-ops-card">
        <h3>Matchs a traiter</h3>
        @if($matchesToSettle->isEmpty())
            <div class="adm-empty">Aucun match en attente.</div>
        @else
            <div class="adm-mini-list">
                @foreach($matchesToSettle as $match)
                    <div class="adm-mini-item">
                        <div>
                            <strong>#{{ $match->id }} - {{ $match->displayTitle() }}</strong>
                            <p>{{ $match->status }} · {{ optional($match->starts_at)->format('d/m H:i') }}</p>
                        </div>
                        <a href="{{ route('admin.matches.manage', $match->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Ouvrir">Ouvrir</span></a>
                    </div>
                @endforeach
            </div>
        @endif
    </article>

    <article class="adm-ops-card">
        <h3>Stock cadeaux faible</h3>
        @if($lowStockGifts->isEmpty())
            <div class="adm-empty">Stock cadeaux OK.</div>
        @else
            <div class="adm-mini-list">
                @foreach($lowStockGifts as $gift)
                    <div class="adm-mini-item">
                        <div>
                            <strong>#{{ $gift->id }} - {{ $gift->title }}</strong>
                            <p>Stock {{ (int) $gift->stock }} · {{ $gift->is_active ? 'Actif' : 'Inactif' }}</p>
                        </div>
                        <div class="adm-row-actions">
                            <a href="{{ route('admin.gifts.index') }}#gift-{{ $gift->id }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Fiche">Fiche</span></a>
                            <form method="POST" action="{{ route('admin.operations.gifts.stock', $gift->id) }}" class="adm-inline-form">
                                @csrf
                                <input class="adm-inline-input" type="number" min="0" name="stock" value="{{ (int) $gift->stock }}">
                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Stock">Stock</span></button>
                            </form>
                            <form method="POST" action="{{ route('admin.operations.gifts.status', $gift->id) }}">
                                @csrf
                                <input type="hidden" name="is_active" value="{{ $gift->is_active ? 0 : 1 }}">
                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="{{ $gift->is_active ? 'Desactiver' : 'Activer' }}">{{ $gift->is_active ? 'Desactiver' : 'Activer' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </article>

    <article class="adm-ops-card">
        <h3>Stock shop faible</h3>
        @if($lowStockShopItems->isEmpty())
            <div class="adm-empty">Stock shop OK.</div>
        @else
            <div class="adm-mini-list">
                @foreach($lowStockShopItems as $item)
                    <div class="adm-mini-item">
                        <div>
                            <strong>#{{ $item->id }} - {{ $item->name }}</strong>
                            <p>{{ $item->key }} · Stock {{ $item->stock ?? 'infini' }} · {{ $item->is_active ? 'Actif' : 'Inactif' }}</p>
                        </div>
                        <div class="adm-row-actions">
                            <a href="{{ route('admin.dashboard', ['q' => $item->id]) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Fiche">Fiche</span></a>
                            <form method="POST" action="{{ route('admin.operations.shop-items.stock', $item->id) }}" class="adm-inline-form">
                                @csrf
                                <input class="adm-inline-input" type="number" min="0" name="stock" value="{{ (int) ($item->stock ?? 0) }}">
                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Stock">Stock</span></button>
                            </form>
                            <form method="POST" action="{{ route('admin.operations.shop-items.status', $item->id) }}">
                                @csrf
                                <input type="hidden" name="is_active" value="{{ $item->is_active ? 0 : 1 }}">
                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="{{ $item->is_active ? 'Desactiver' : 'Activer' }}">{{ $item->is_active ? 'Desactiver' : 'Activer' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </article>

    <article class="adm-ops-card">
        <h3>Moderation rapide</h3>
        @if($reviewsPending->isEmpty())
            <div class="adm-empty">Aucun avis en attente.</div>
        @else
            <div class="adm-mini-list">
                @foreach($reviewsPending as $review)
                    <div class="adm-mini-item">
                        <div>
                            <strong>Avis #{{ $review->id }}</strong>
                            <p>{{ $review->user->email ?? 'Utilisateur inconnu' }} · {{ optional($review->created_at)->format('d/m H:i') }}</p>
                        </div>
                        <a href="{{ route('admin.reviews.index', ['q' => $review->id]) }}#review-{{ $review->id }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Moderer">Moderer</span></a>
                    </div>
                @endforeach
            </div>
        @endif
    </article>
</div>
