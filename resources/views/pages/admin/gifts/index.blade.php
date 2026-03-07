@extends('marketing.layouts.template')

@section('title', 'Admin Cadeaux | ERAH Plateforme')
@section('meta_description', 'Gestion du catalogue cadeaux et moderation des redemptions.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $status = $status ?? 'all';
        $statuses = $statuses ?? [];
        $giftFallbackImage = '/template/assets/img/logo-fond.png';
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Admin Cadeaux',
        'heroDescription' => 'Catalogue cadeaux, images, stocks et moderation des demandes.',
        'heroMaskDescription' => 'Gestion gifts + redemptions.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Creer un cadeau</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Vous pouvez soit televerser une image, soit fournir une URL.</p>
                        </div>

                        <form method="POST" action="{{ route('admin.gifts.store') }}" enctype="multipart/form-data" class="tt-form tt-form-creative adm-form">
                            @csrf

                            <div class="adm-form-grid-4">
                                <div class="tt-form-group">
                                    <label for="gift_title">Titre</label>
                                    <input class="tt-form-control" id="gift_title" name="title" value="{{ old('title') }}" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="gift_cost_points">Cout (points)</label>
                                    <input class="tt-form-control" id="gift_cost_points" name="cost_points" type="number" min="1" value="{{ old('cost_points', 1000) }}" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="gift_stock">Stock</label>
                                    <input class="tt-form-control" id="gift_stock" name="stock" type="number" min="0" value="{{ old('stock', 10) }}" required>
                                </div>

                                <div class="tt-form-group" style="align-self:end;">
                                    <div class="tt-form-check">
                                        <input type="checkbox" id="gift_is_active" name="is_active" value="1" @checked(old('is_active', true))>
                                        <label for="gift_is_active">Actif</label>
                                    </div>
                                </div>

                                <div class="tt-form-group adm-col-span-2">
                                    <label for="gift_image_file">Image du cadeau</label>
                                    <input class="tt-form-control" id="gift_image_file" name="image_file" type="file" accept="image/*">
                                </div>

                                <div class="tt-form-group adm-col-span-2">
                                    <label for="gift_image_url">Ou URL image</label>
                                    <input class="tt-form-control" id="gift_image_url" name="image_url" type="url" value="{{ old('image_url') }}" placeholder="https://...">
                                </div>

                                <div class="tt-form-group adm-col-span-4">
                                    <label for="gift_description">Description</label>
                                    <textarea class="tt-form-control" id="gift_description" name="description" rows="3">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Creer le cadeau">Creer le cadeau</span>
                            </button>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Catalogue cadeaux</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Vue cartes plus lisible avec edition simplifiee par cadeau.</p>
                        </div>

                        @if($gifts->count())
                            <div class="adm-gift-grid">
                                @foreach($gifts as $gift)
                                    @php
                                        $giftImage = (string) ($gift->image_url ?: $giftFallbackImage);
                                    @endphp
                                    <article class="adm-gift-card">
                                        <div class="adm-gift-media">
                                            <img src="{{ $giftImage }}" loading="lazy" alt="{{ $gift->title }}">
                                        </div>

                                        <div class="adm-gift-copy">
                                            <h3 class="adm-gift-title">{{ $gift->title }}</h3>
                                            <p class="adm-meta">{{ \Illuminate\Support\Str::limit((string) ($gift->description ?? 'Aucune description.'), 120) }}</p>
                                        </div>

                                        <div class="adm-gift-meta">
                                            <span class="adm-pill">ID #{{ $gift->id }}</span>
                                            <span class="adm-pill">{{ (int) $gift->cost_points }} pts</span>
                                            <span class="adm-pill">Stock {{ (int) $gift->stock }}</span>
                                            <span class="adm-pill">{{ $gift->is_active ? 'Actif' : 'Inactif' }}</span>
                                        </div>

                                        <details class="adm-advanced">
                                            <summary>Modifier ce cadeau</summary>
                                            <div class="adm-advanced-body">
                                                <form method="POST" action="{{ route('admin.gifts.update', $gift->id) }}" enctype="multipart/form-data" class="tt-form tt-form-creative adm-form">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="adm-form-grid-3">
                                                        <div class="tt-form-group">
                                                            <label>Titre</label>
                                                            <input class="tt-form-control" name="title" value="{{ $gift->title }}" required>
                                                        </div>

                                                        <div class="tt-form-group">
                                                            <label>Cout points</label>
                                                            <input class="tt-form-control" name="cost_points" type="number" min="1" value="{{ (int) $gift->cost_points }}" required>
                                                        </div>

                                                        <div class="tt-form-group">
                                                            <label>Stock</label>
                                                            <input class="tt-form-control" name="stock" type="number" min="0" value="{{ (int) $gift->stock }}" required>
                                                        </div>

                                                        <div class="tt-form-group adm-col-span-3">
                                                            <label>Description</label>
                                                            <textarea class="tt-form-control" name="description" rows="2">{{ $gift->description }}</textarea>
                                                        </div>

                                                        <div class="tt-form-group">
                                                            <label>Nouvelle image (fichier)</label>
                                                            <input class="tt-form-control" name="image_file" type="file" accept="image/*">
                                                        </div>

                                                        <div class="tt-form-group adm-col-span-2">
                                                            <label>URL image</label>
                                                            <input class="tt-form-control" name="image_url" value="{{ $gift->image_url }}" placeholder="https://...">
                                                        </div>

                                                        <div class="tt-form-group" style="align-self:end;">
                                                            <div class="tt-form-check">
                                                                <input type="checkbox" id="gift_active_{{ $gift->id }}" name="is_active" value="1" {{ $gift->is_active ? 'checked' : '' }}>
                                                                <label for="gift_active_{{ $gift->id }}">Actif</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="adm-row-actions">
                                                        <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                            <span data-hover="Enregistrer">Enregistrer</span>
                                                        </button>
                                                    </div>
                                                </form>

                                                <form method="POST" action="{{ route('admin.gifts.destroy', $gift->id) }}" onsubmit="return confirm('Supprimer ce cadeau ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                        <span data-hover="Supprimer">Supprimer</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </details>
                                    </article>
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $gifts->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun cadeau dans le catalogue.</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Redemptions</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Traitez les demandes en attente puis suivez l'expedition et la livraison.</p>
                        </div>

                        <div class="adm-filter-actions margin-bottom-20">
                            <a href="{{ route('admin.gifts.index', ['status' => 'all']) }}" class="tt-btn {{ $status === 'all' ? 'tt-btn-secondary' : 'tt-btn-outline' }} tt-magnetic-item">
                                <span data-hover="Tous">Tous</span>
                            </a>
                            @foreach($statuses as $item)
                                <a href="{{ route('admin.gifts.index', ['status' => $item]) }}" class="tt-btn {{ $status === $item ? 'tt-btn-secondary' : 'tt-btn-outline' }} tt-magnetic-item">
                                    <span data-hover="{{ ucfirst($item) }}">{{ ucfirst($item) }}</span>
                                </a>
                            @endforeach
                        </div>

                        @if($redemptions->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Utilisateur</th>
                                            <th>Cadeau</th>
                                            <th>Statut</th>
                                            <th>Cout</th>
                                            <th>Demande</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($redemptions as $redemption)
                                            <tr>
                                                <td>#{{ $redemption->id }}</td>
                                                <td>#{{ $redemption->user_id }} {{ $redemption->user->name ?? '' }}</td>
                                                <td>#{{ $redemption->gift_id }} {{ $redemption->gift->title ?? '' }}</td>
                                                <td><span class="adm-pill">{{ $redemption->status }}</span></td>
                                                <td>{{ (int) $redemption->cost_points_snapshot }}</td>
                                                <td>{{ optional($redemption->requested_at)->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="adm-row-actions">
                                                        <form method="POST" action="{{ route('admin.redemptions.approve', $redemption->id) }}">
                                                            @csrf
                                                            <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                                <span data-hover="Approuver">Approuver</span>
                                                            </button>
                                                        </form>

                                                        <form method="POST" action="{{ route('admin.redemptions.reject', $redemption->id) }}" class="adm-inline-form">
                                                            @csrf
                                                            <input class="adm-inline-input" name="reason" placeholder="Motif refus">
                                                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                                <span data-hover="Refuser">Refuser</span>
                                                            </button>
                                                        </form>

                                                        <form method="POST" action="{{ route('admin.redemptions.ship', $redemption->id) }}" class="adm-inline-form">
                                                            @csrf
                                                            <input class="adm-inline-input" name="tracking_code" placeholder="Code tracking">
                                                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                                <span data-hover="Expedier">Expedier</span>
                                                            </button>
                                                        </form>

                                                        <form method="POST" action="{{ route('admin.redemptions.deliver', $redemption->id) }}">
                                                            @csrf
                                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                                <span data-hover="Livrer">Livrer</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="adm-pagin">{{ $redemptions->links() }}</div>
                        @else
                            <div class="adm-empty">Aucune redemption pour ce filtre.</div>
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

