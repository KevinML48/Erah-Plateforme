@extends('marketing.layouts.template')

@section('title', 'Admin cadeaux | ERAH')
@section('meta_description', 'Console fulfilment cadeaux: catalogue, commandes et stock.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-tight-list {
            display: grid;
            gap: 10px;
        }

        .adm-decision-grid {
            display: grid;
            gap: 14px;
        }

        .adm-decision-columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .adm-decision-col {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .adm-decision-col > .adm-meta {
            margin: 0 0 2px;
        }

        .adm-decision-grid > .adm-meta {
            margin: 0;
        }

        @media (max-width: 1199.98px) {
            .adm-decision-columns {
                grid-template-columns: 1fr;
                gap: 14px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $status = $status ?? 'all';
        $statuses = $statuses ?? [];
        $statusLabels = $statusLabels ?? \App\Models\GiftRedemption::statusLabels();
        $giftFallbackImage = '/template/assets/img/logo-fond.png';
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Administration ERAH',
        'heroTitle' => 'Console fulfilment cadeaux',
        'heroDescription' => 'Pilotez le catalogue, le stock et les commandes cadeaux depuis un seul ecran.',
        'heroMaskDescription' => 'Catalogue, fulfilment et suivi commande.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface" id="gift-redemptions-center">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Vue operationnelle cadeaux</h2>
                        </div>
                        <div class="adm-kpi-grid">
                            <article class="adm-kpi-card"><strong>{{ (int) ($kpis['gifts_total'] ?? 0) }}</strong><span>Cadeaux totaux</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) ($kpis['gifts_active'] ?? 0) }}</strong><span>Cadeaux actifs</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) ($kpis['pending_redemptions'] ?? 0) }}</strong><span>Commandes pending</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) ($kpis['approved_redemptions'] ?? 0) }}</strong><span>Commandes approuvees</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) ($kpis['shipped_redemptions'] ?? 0) }}</strong><span>Commandes expediees</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) ($kpis['delivered_redemptions'] ?? 0) }}</strong><span>Commandes livrees</span></article>
                            <article class="adm-kpi-card"><strong>{{ (int) ($kpis['low_stock_gifts'] ?? 0) }}</strong><span>Stocks faibles</span></article>
                        </div>
                        <div class="adm-sub-grid margin-top-20">
                            <div class="adm-sub-stack" id="gift-stock-alerts">
                                <h3 class="adm-surface-title">Alertes stock</h3>
                                @if(($stockAlerts ?? collect())->isNotEmpty())
                                    <div class="adm-tight-list">
                                        @foreach(($stockAlerts ?? collect()) as $giftAlert)
                                            <div class="adm-user-item"><strong>#{{ $giftAlert->id }} {{ $giftAlert->title }}</strong><small>Stock {{ (int) $giftAlert->stock }}</small></div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="adm-empty">Aucune alerte stock actuellement.</div>
                                @endif
                            </div>
                            <div class="adm-sub-stack">
                                <h3 class="adm-surface-title">Aide a la decision</h3>
                                <div class="adm-decision-grid">
                                    <div class="adm-decision-columns">
                                        <div class="adm-decision-col">
                                            <p class="adm-meta">Top favoris</p>
                                            <div class="adm-tight-list">
                                                @foreach(($mostFavorited ?? collect()) as $giftMetric)
                                                    <div class="adm-user-item"><strong>{{ $giftMetric->title }}</strong><small>{{ (int) ($giftMetric->favorites_count ?? 0) }} favoris</small></div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="adm-decision-col">
                                            <p class="adm-meta">Top ajouts panier</p>
                                            <div class="adm-tight-list">
                                                @foreach(($mostAddedToCart ?? collect()) as $giftMetric)
                                                    <div class="adm-user-item"><strong>{{ $giftMetric->title }}</strong><small>{{ (int) ($giftMetric->cart_quantity_total ?? 0) }} en panier actif</small></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <p class="adm-meta">Top demandes</p>
                                    <div class="adm-tight-list">
                                        @foreach(($mostRequested ?? collect()) as $giftMetric)
                                            <div class="adm-user-item"><strong>{{ $giftMetric->title }}</strong><small>{{ (int) ($giftMetric->redemptions_count ?? 0) }} demandes</small></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20"><h2 class="tt-heading-title tt-text-reveal">Creer un cadeau</h2></div>
                        <form method="POST" action="{{ route('admin.gifts.store') }}" enctype="multipart/form-data" class="tt-form tt-form-creative adm-form">
                            @csrf
                            <div class="adm-form-grid-4">
                                <div class="tt-form-group"><label>Titre</label><input class="tt-form-control" name="title" value="{{ old('title') }}" required></div>
                                <div class="tt-form-group"><label>Cout points</label><input class="tt-form-control" name="cost_points" type="number" min="1" value="{{ old('cost_points', 1000) }}" required></div>
                                <div class="tt-form-group"><label>Stock</label><input class="tt-form-control" name="stock" type="number" min="0" value="{{ old('stock', 10) }}" required></div>
                                <div class="tt-form-group"><label>Ordre affichage</label><input class="tt-form-control" name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}"></div>
                                <div class="tt-form-group adm-col-span-2"><label>Image fichier</label><input class="tt-form-control" name="image_file" type="file" accept="image/*"></div>
                                <div class="tt-form-group adm-col-span-2"><label>Ou URL image</label><input class="tt-form-control" name="image_url" type="url" value="{{ old('image_url') }}" placeholder="https://..."></div>
                                <div class="tt-form-group adm-col-span-4"><label>Description</label><textarea class="tt-form-control" name="description" rows="3">{{ old('description') }}</textarea></div>
                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="gift_is_active" name="is_active" value="1" @checked(old('is_active', true))><label for="gift_is_active">Actif</label></div></div>
                                <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="gift_is_featured" name="is_featured" value="1" @checked(old('is_featured', false))><label for="gift_is_featured">Mise en avant</label></div></div>
                            </div>
                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Creer le cadeau">Creer le cadeau</span></button>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20"><h2 class="tt-heading-title tt-text-reveal">Catalogue cadeaux</h2></div>
                        @if($gifts->count())
                            <div class="adm-gift-grid">
                                @foreach($gifts as $gift)
                                    <article class="adm-gift-card">
                                        <div class="adm-gift-media"><img src="{{ $gift->image_url ?: $giftFallbackImage }}" loading="lazy" alt="{{ $gift->title }}"></div>
                                        <div class="adm-gift-copy"><h3 class="adm-gift-title">{{ $gift->title }}</h3><p class="adm-meta">{{ \Illuminate\Support\Str::limit((string) ($gift->description ?? 'Aucune description'), 120) }}</p></div>
                                        <div class="adm-gift-meta">
                                            <span class="adm-pill">ID #{{ $gift->id }}</span>
                                            <span class="adm-pill">{{ (int) $gift->cost_points }} pts</span>
                                            <span class="adm-pill">Stock {{ (int) $gift->stock }}</span>
                                            <span class="adm-pill">{{ $gift->is_active ? 'Actif' : 'Inactif' }}</span>
                                            <span class="adm-pill">{{ $gift->is_featured ? 'Mis en avant' : 'Standard' }}</span>
                                            <span class="adm-pill">Ordre {{ (int) ($gift->sort_order ?? 0) }}</span>
                                            @if($gift->launchCatalogCategoryLabel())
                                                <span class="adm-pill">{{ $gift->launchCatalogCategoryLabel() }}</span>
                                            @endif
                                            @if($gift->launchCatalogDeliveryTypeLabel())
                                                <span class="adm-pill">{{ $gift->launchCatalogDeliveryTypeLabel() }}</span>
                                            @endif
                                            @if($gift->launchCatalogRequiresAdminValidation())
                                                <span class="adm-pill">Validation admin</span>
                                            @endif
                                        </div>
                                        <détails class="adm-advanced">
                                            <summary>Modifier ce cadeau</summary>
                                            <div class="adm-advanced-body">
                                                <form method="POST" action="{{ route('admin.gifts.update', $gift->id) }}" enctype="multipart/form-data" class="tt-form tt-form-creative adm-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="adm-form-grid-4">
                                                        <div class="tt-form-group"><label>Titre</label><input class="tt-form-control" name="title" value="{{ $gift->title }}" required></div>
                                                        <div class="tt-form-group"><label>Cout</label><input class="tt-form-control" name="cost_points" type="number" min="1" value="{{ (int) $gift->cost_points }}" required></div>
                                                        <div class="tt-form-group"><label>Stock</label><input class="tt-form-control" name="stock" type="number" min="0" value="{{ (int) $gift->stock }}" required></div>
                                                        <div class="tt-form-group"><label>Ordre</label><input class="tt-form-control" name="sort_order" type="number" min="0" value="{{ (int) ($gift->sort_order ?? 0) }}"></div>
                                                        <div class="tt-form-group adm-col-span-4"><label>Description</label><textarea class="tt-form-control" name="description" rows="2">{{ $gift->description }}</textarea></div>
                                                        <div class="tt-form-group adm-col-span-2"><label>Image fichier</label><input class="tt-form-control" name="image_file" type="file" accept="image/*"></div>
                                                        <div class="tt-form-group adm-col-span-2"><label>URL image</label><input class="tt-form-control" name="image_url" value="{{ $gift->image_url }}"></div>
                                                        <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="gift_active_{{ $gift->id }}" name="is_active" value="1" {{ $gift->is_active ? 'checked' : '' }}><label for="gift_active_{{ $gift->id }}">Actif</label></div></div>
                                                        <div class="tt-form-group"><div class="tt-form-check"><input type="checkbox" id="gift_featured_{{ $gift->id }}" name="is_featured" value="1" {{ $gift->is_featured ? 'checked' : '' }}><label for="gift_featured_{{ $gift->id }}">Mise en avant</label></div></div>
                                                    </div>
                                                    <div class="adm-row-actions">
                                                        <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Enregistrer">Enregistrer</span></button>
                                                    </div>
                                                </form>
                                                <form method="POST" action="{{ route('admin.gifts.destroy', $gift->id) }}" onsubmit="return confirm('Supprimer ce cadeau ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Supprimer">Supprimer</span></button>
                                                </form>
                                            </div>
                                        </détails>
                                    </article>
                                @endforeach
                            </div>
                            <div class="adm-pagin">{{ $gifts->onEachSide(1)->links('vendor.pagination.admin') }}</div>
                        @else
                            <div class="adm-empty">Aucun cadeau dans le catalogue.</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20"><h2 class="tt-heading-title tt-text-reveal">Centre de traitement commandes cadeaux</h2></div>
                        <div class="adm-sub-stack margin-bottom-20">
                            <h3 class="adm-surface-title">Suivi par utilisateur (commandes en cours)</h3>
                            <p class="adm-meta margin-bottom-10">Vision immediate de qui demande quoi, volume en cours et historique.</p>
                            @if(($inProgressByUser ?? collect())->count())
                                <div class="adm-table-wrap">
                                    <table class="adm-table">
                                        <thead>
                                            <tr>
                                                <th>Utilisateur</th>
                                                <th>Commandes en cours</th>
                                                <th>Cadeaux demandes</th>
                                                <th>Historique</th>
                                                <th>Derniere demande</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(($inProgressByUser ?? collect()) as $userQueue)
                                                <tr>
                                                    <td>
                                                        @if($userQueue['user'])
                                                            <strong>{{ $userQueue['user']->name }}</strong><br>
                                                            <small>#{{ $userQueue['user_id'] }} - {{ $userQueue['user']->email }}</small>
                                                        @else
                                                            <strong>Utilisateur supprime</strong><br>
                                                            <small>#{{ $userQueue['user_id'] }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ (int) $userQueue['active_orders'] }}</strong> en cours<br>
                                                        <small>
                                                            {{ (int) $userQueue['pending_orders'] }} pending,
                                                            {{ (int) $userQueue['approved_orders'] }} approuvees,
                                                            {{ (int) $userQueue['shipped_orders'] }} expediees
                                                        </small><br>
                                                        <small>{{ (int) $userQueue['total_points_in_progress'] }} pts engages</small>
                                                    </td>
                                                    <td>
                                                        @if(count($userQueue['gift_titles']))
                                                            <strong>{{ implode(', ', array_slice($userQueue['gift_titles'], 0, 3)) }}</strong>
                                                            @if(count($userQueue['gift_titles']) > 3)
                                                                <br><small>+ {{ count($userQueue['gift_titles']) - 3 }} autres</small>
                                                            @endif
                                                        @else
                                                            <small>-</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ (int) $userQueue['historical_orders'] }} commandes au total</td>
                                                    <td>{{ optional($userQueue['latest_requested_at'])->format('d/m/Y H:i') ?: '-' }}</td>
                                                    <td>
                                                        <div class="adm-row-actions">
                                                            <form method="GET" action="{{ route('admin.gifts.index') }}#gift-redemptions-center">
                                                                <input type="hidden" name="user_id" value="{{ $userQueue['user_id'] }}">
                                                                @if(($status ?? 'all') !== 'all')
                                                                    <input type="hidden" name="status" value="{{ $status }}">
                                                                @endif
                                                                @if(($sort ?? 'requested_desc') !== 'requested_desc')
                                                                    <input type="hidden" name="sort" value="{{ $sort }}">
                                                                @endif
                                                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                                    <span data-hover="Filtrer ses commandes">Filtrer ses commandes</span>
                                                                </button>
                                                            </form>
                                                            @if($userQueue['user'])
                                                                <a href="{{ route('admin.users.show', $userQueue['user_id']) }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                                    <span data-hover="Fiche utilisateur">Fiche utilisateur</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="adm-empty">Aucune commande en cours pour le moment.</div>
                            @endif
                        </div>

                        <div class="adm-sub-stack margin-bottom-20">
                            <h3 class="adm-surface-title">Commandes en cours a traiter</h3>
                            <p class="adm-meta margin-bottom-10">Pending, approuvees et expediees avec acces détail + fiche utilisateur.</p>
                            @if(($inProgressRedemptions ?? collect())->count())
                                <div class="adm-table-wrap">
                                    <table class="adm-table">
                                        <thead>
                                            <tr>
                                                <th>Commande</th>
                                                <th>Demandeur</th>
                                                <th>Cadeau</th>
                                                <th>Statut</th>
                                                <th>Date demande</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(($inProgressRedemptions ?? collect()) as $activeRedemption)
                                                <tr id="redemption-{{ $activeRedemption->id }}">
                                                    <td><strong>CMD-{{ str_pad((string) $activeRedemption->id, 6, '0', STR_PAD_LEFT) }}</strong><br><small>#{{ $activeRedemption->id }}</small></td>
                                                    <td>
                                                        @if($activeRedemption->user)
                                                            <strong>{{ $activeRedemption->user->name }}</strong><br>
                                                            <small>{{ $activeRedemption->user->email }}</small>
                                                        @else
                                                            <small>Utilisateur supprime</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $activeRedemption->gift->title ?? 'Cadeau supprime' }}</td>
                                                    <td><span class="adm-pill">{{ $statusLabels[$activeRedemption->status] ?? ucfirst((string) $activeRedemption->status) }}</span></td>
                                                    <td>{{ optional($activeRedemption->requested_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                                    <td>
                                                        <div class="adm-row-actions">
                                                            <a href="{{ route('admin.redemptions.show', $activeRedemption->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Commande">Commande</span></a>
                                                            @if($activeRedemption->user)
                                                                <a href="{{ route('admin.users.show', $activeRedemption->user_id) }}" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Utilisateur">Utilisateur</span></a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="adm-empty">Aucune commande en cours a traiter.</div>
                            @endif
                        </div>

                        @if(($userIdFilter ?? '') !== '' || ($giftIdFilter ?? '') !== '' || ($search ?? '') !== '' || ($status ?? 'all') !== 'all')
                            <div class="adm-row-actions margin-bottom-15">
                                <span class="adm-pill">
                                    Filtres actifs
                                    @if(($userIdFilter ?? '') !== '')
                                        | utilisateur #{{ $userIdFilter }}
                                    @endif
                                    @if(($giftIdFilter ?? '') !== '')
                                        | cadeau #{{ $giftIdFilter }}
                                    @endif
                                    @if(($search ?? '') !== '')
                                        | recherche "{{ $search }}"
                                    @endif
                                    @if(($status ?? 'all') !== 'all')
                                        | statut {{ $statusLabels[$status] ?? $status }}
                                    @endif
                                </span>
                                <a href="{{ route('admin.gifts.index') }}#gift-redemptions-center" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Reinitialiser">Reinitialiser</span>
                                </a>
                            </div>
                        @endif

                        <form method="GET" action="{{ route('admin.gifts.index') }}#gift-redemptions-center" class="tt-form tt-form-creative adm-form margin-bottom-20">
                            <div class="adm-form-grid-4">
                                <div class="tt-form-group"><label>Recherche</label><input class="tt-form-control" type="text" name="search" value="{{ $search ?? '' }}" placeholder="ID, tracking, utilisateur, email, cadeau"></div>
                                <div class="tt-form-group"><label>Statut</label><select class="tt-form-control" name="status"><option value="all" @selected($status === 'all')>Tous les statuts</option>@foreach($statuses as $statusValue)<option value="{{ $statusValue }}" @selected($status === $statusValue)>{{ $statusLabels[$statusValue] ?? ucfirst($statusValue) }}</option>@endforeach</select></div>
                                <div class="tt-form-group"><label>Tri</label><select class="tt-form-control" name="sort"><option value="requested_desc" @selected(($sort ?? '') === 'requested_desc')>Demande plus recente</option><option value="requested_asc" @selected(($sort ?? '') === 'requested_asc')>Demande plus ancienne</option><option value="updated_desc" @selected(($sort ?? '') === 'updated_desc')>Maj recente</option><option value="status" @selected(($sort ?? '') === 'status')>Statut puis date</option></select></div>
                                <div class="tt-form-group"><label>ID utilisateur</label><input class="tt-form-control" type="number" min="1" name="user_id" value="{{ $userIdFilter ?? '' }}"></div>
                                <div class="tt-form-group"><label>ID cadeau</label><input class="tt-form-control" type="number" min="1" name="gift_id" value="{{ $giftIdFilter ?? '' }}"></div>
                                <div class="tt-form-group adm-form-cta"><button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Filtrer">Filtrer la file</span></button></div>
                            </div>
                        </form>

                        @if($redemptions->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead><tr><th>Commande</th><th>Utilisateur</th><th>Cadeau</th><th>Statut</th><th>Suivi</th><th>Date demande</th><th>Actions</th></tr></thead>
                                    <tbody>
                                        @foreach($redemptions as $redemption)
                                            <tr>
                                                <td><strong>CMD-{{ str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT) }}</strong><br><small>#{{ $redemption->id }} - {{ (int) $redemption->cost_points_snapshot }} pts</small></td>
                                                <td>
                                                    #{{ $redemption->user_id }}<br>
                                                    @if($redemption->user)
                                                        <a href="{{ route('admin.users.show', $redemption->user_id) }}"><strong>{{ $redemption->user->name }}</strong></a><br>
                                                        <small>{{ $redemption->user->email }}</small>
                                                    @else
                                                        <strong>Utilisateur supprime</strong><br>
                                                        <small>-</small>
                                                    @endif
                                                </td>
                                                <td>#{{ $redemption->gift_id }}<br><strong>{{ $redemption->gift->title ?? 'Cadeau supprime' }}</strong></td>
                                                <td><span class="adm-pill">{{ $statusLabels[$redemption->status] ?? ucfirst($redemption->status) }}</span></td>
                                                <td>@if($redemption->tracking_code)<strong>{{ $redemption->tracking_code }}</strong><br><small>{{ $redemption->tracking_carrier ?: 'Transporteur non renseigne' }}</small>@else<small>Aucun tracking</small>@endif</td>
                                                <td>{{ optional($redemption->requested_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                                <td>
                                                    <div class="adm-row-actions">
                                                        <a href="{{ route('admin.redemptions.show', $redemption->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Detail">Detail</span></a>
                                                        @if($redemption->user)
                                                            <a href="{{ route('admin.users.show', $redemption->user_id) }}" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Utilisateur">Utilisateur</span></a>
                                                        @endif
                                                        @if($redemption->status === \App\Models\GiftRedemption::STATUS_PENDING)
                                                            <form method="POST" action="{{ route('admin.redemptions.approve', $redemption->id) }}">@csrf<button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Approuver">Approuver</span></button></form>
                                                        @endif
                                                        @if(in_array($redemption->status, [\App\Models\GiftRedemption::STATUS_PENDING, \App\Models\GiftRedemption::STATUS_APPROVED], true))
                                                            <form method="POST" action="{{ route('admin.redemptions.reject', $redemption->id) }}" class="adm-inline-form">@csrf<input class="adm-inline-input" name="reason" required minlength="3" placeholder="Motif rejet"><button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Rejeter">Rejeter</span></button></form>
                                                        @endif
                                                        @if(in_array($redemption->status, [\App\Models\GiftRedemption::STATUS_APPROVED, \App\Models\GiftRedemption::STATUS_PENDING], true))
                                                            <form method="POST" action="{{ route('admin.redemptions.ship', $redemption->id) }}" class="adm-inline-form">@csrf<input class="adm-inline-input" name="tracking_code" required placeholder="Tracking"><input class="adm-inline-input" name="tracking_carrier" placeholder="Transporteur"><button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Expedier">Expedier</span></button></form>
                                                        @endif
                                                        @if(in_array($redemption->status, [\App\Models\GiftRedemption::STATUS_SHIPPED, \App\Models\GiftRedemption::STATUS_APPROVED], true))
                                                            <form method="POST" action="{{ route('admin.redemptions.deliver', $redemption->id) }}">@csrf<button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Livrer">Livrer</span></button></form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="adm-pagin">{{ $redemptions->onEachSide(1)->links('vendor.pagination.admin') }}</div>
                        @else
                            <div class="adm-empty">Aucune commande cadeau ne correspond a ce filtre.</div>
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
