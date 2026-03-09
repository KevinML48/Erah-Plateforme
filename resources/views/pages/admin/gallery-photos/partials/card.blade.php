@php
    $title = $photo->title ?: 'Media galerie #'.$photo->id;
    $isScheduled = $photo->published_at?->isFuture() ?? false;
    $description = filled($photo->description)
        ? \Illuminate\Support\Str::limit($photo->description, 150)
        : 'Aucune description. Utilisez l edition rapide pour ajouter un contexte utile.';
    $previewUrl = $photo->primary_media_url;
    $previewCaption = collect([
        $photo->category_label ?: null,
        $photo->filter_label ?: null,
        $photo->alt_text ?: null,
    ])->filter()->implode(' - ');
    $statusTone = $photo->is_active ? 'is-active' : 'is-inactive';
    $statusLabel = $photo->is_active ? 'Actif' : 'Inactif';
    $publishLabel = $photo->published_at?->format('d/m/Y H:i') ?: 'Immediat';
    $updatedLabel = $photo->updated_at?->format('d/m/Y H:i') ?: 'n/a';
    $sizeLabel = filled($photo->media_size) ? number_format($photo->media_size / 1048576, 1, ',', ' ') . ' Mo' : 'n/a';
    $mediaLabel = $photo->media_mime_type ?: ($photo->is_video ? 'video' : 'image');
@endphp

<article class="adm-gallery-card" data-gallery-card>
    <div class="adm-gallery-card-media">
        <div class="adm-gallery-card-badges">
            <span class="adm-gallery-state-pill {{ $statusTone }}">{{ $statusLabel }}</span>
            @if($isScheduled)
                <span class="adm-gallery-state-pill is-scheduled">Planifie</span>
            @endif
            <span class="adm-gallery-state-pill is-neutral">{{ $photo->is_video ? 'Video' : 'Image' }}</span>
        </div>

        <span class="adm-gallery-card-order">Ordre {{ (int) $photo->sort_order }}</span>

        @if($photo->is_video)
            <video muted loop playsinline preload="metadata" data-gallery-card-video>
                <source src="{{ $photo->video_url }}" type="{{ $photo->media_mime_type ?: 'video/mp4' }}">
            </video>
        @elseif($photo->image_url)
            <img src="{{ $photo->image_url }}" alt="{{ $photo->display_alt_text }}" loading="lazy">
        @else
            <div class="adm-gallery-card-media-empty">Media indisponible</div>
        @endif

        <div class="adm-gallery-card-media-overlay"></div>

        @if($previewUrl)
            <button
                type="button"
                class="adm-gallery-media-cta"
                data-gallery-preview-trigger
                data-preview-type="{{ $photo->is_video ? 'video' : 'image' }}"
                data-preview-src="{{ $previewUrl }}"
                data-preview-mime="{{ $photo->media_mime_type ?: ($photo->is_video ? 'video/mp4' : 'image/jpeg') }}"
                data-preview-title="{{ $title }}"
                data-preview-caption="{{ $previewCaption !== '' ? $previewCaption : $description }}"
            >
                Previsualiser
            </button>
        @endif
    </div>

    <div class="adm-gallery-card-body">
        <div class="adm-gallery-card-head">
            <div>
                <p class="adm-gallery-card-eyebrow">
                    Media #{{ $photo->id }}
                    @if($photo->created_at)
                        - ajoute le {{ $photo->created_at->format('d/m/Y') }}
                    @endif
                </p>
                <h3>{{ $title }}</h3>
            </div>

            <span class="adm-pill">{{ $photo->legacy_source ? 'Import legacy' : 'Upload admin' }}</span>
        </div>

        <p class="adm-gallery-card-summary">{{ $description }}</p>

        <div class="adm-gallery-chip-row">
            <span class="adm-pill">Filtre: {{ $photo->filter_label ?: 'Sans filtre' }}</span>
            <span class="adm-pill">Categorie: {{ $photo->category_label ?: 'Non classee' }}</span>
            <span class="adm-pill">Curseur: {{ $photo->cursor_label ?: 'Voir' }}</span>
        </div>

        <dl class="adm-gallery-meta-list">
            <div class="adm-gallery-meta-item">
                <dt>Publication</dt>
                <dd>{{ $publishLabel }}</dd>
            </div>
            <div class="adm-gallery-meta-item">
                <dt>Alt text</dt>
                <dd>{{ $photo->alt_text ?: 'Non renseigne' }}</dd>
            </div>
            <div class="adm-gallery-meta-item">
                <dt>Type</dt>
                <dd>{{ $mediaLabel }}</dd>
            </div>
            <div class="adm-gallery-meta-item">
                <dt>Taille</dt>
                <dd>{{ $sizeLabel }}</dd>
            </div>
            <div class="adm-gallery-meta-item">
                <dt>Derniere maj</dt>
                <dd>{{ $updatedLabel }}</dd>
            </div>
            <div class="adm-gallery-meta-item">
                <dt>Source</dt>
                <dd>{{ $photo->legacy_source ?: 'Upload admin' }}</dd>
            </div>
        </dl>

        <div class="adm-gallery-order-panel">
            <div>
                <span class="adm-gallery-form-label">Reordonner</span>
                <p class="adm-gallery-inline-help">Utilisez l'ordre direct ou les deplacements rapides.</p>
            </div>

            <div class="adm-gallery-order-controls">
                <form method="POST" action="{{ route('admin.gallery-photos.reorder', $photo->id) }}" class="adm-gallery-order-form">
                    @csrf
                    <input class="tt-form-control" name="sort_order" type="number" min="0" value="{{ (int) $photo->sort_order }}" aria-label="Ordre de {{ $title }}">
                    <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item adm-gallery-btn">
                        <span>Appliquer</span>
                    </button>
                </form>

                <div class="adm-gallery-order-steps">
                    <form method="POST" action="{{ route('admin.gallery-photos.reorder', $photo->id) }}">
                        @csrf
                        <input type="hidden" name="direction" value="up">
                        <button type="submit" class="adm-gallery-icon-btn" aria-label="Monter {{ $title }}">Monter</button>
                    </form>

                    <form method="POST" action="{{ route('admin.gallery-photos.reorder', $photo->id) }}">
                        @csrf
                        <input type="hidden" name="direction" value="down">
                        <button type="submit" class="adm-gallery-icon-btn" aria-label="Descendre {{ $title }}">Descendre</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="adm-gallery-action-row">
            @if($previewUrl)
                <button
                    type="button"
                    class="tt-btn tt-btn-secondary tt-magnetic-item adm-gallery-btn"
                    data-gallery-preview-trigger
                    data-preview-type="{{ $photo->is_video ? 'video' : 'image' }}"
                    data-preview-src="{{ $previewUrl }}"
                    data-preview-mime="{{ $photo->media_mime_type ?: ($photo->is_video ? 'video/mp4' : 'image/jpeg') }}"
                    data-preview-title="{{ $title }}"
                    data-preview-caption="{{ $previewCaption !== '' ? $previewCaption : $description }}"
                >
                    <span>Previsualiser</span>
                </button>
            @endif

            <form method="POST" action="{{ route('admin.gallery-photos.toggle', $photo->id) }}">
                @csrf
                <button type="submit" class="tt-btn {{ $photo->is_active ? 'tt-btn-outline' : 'tt-btn-secondary' }} tt-magnetic-item adm-gallery-btn">
                    <span>{{ $photo->is_active ? 'Desactiver' : 'Activer' }}</span>
                </button>
            </form>

            @if($previewUrl)
                <a href="{{ $previewUrl }}" class="tt-btn tt-btn-outline tt-magnetic-item adm-gallery-btn" target="_blank" rel="noopener">
                    <span>Ouvrir le fichier</span>
                </a>
            @endif

            <form method="POST" action="{{ route('admin.gallery-photos.destroy', $photo->id) }}" onsubmit="return confirm('Supprimer definitivement ce media galerie ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item adm-gallery-btn">
                    <span>Supprimer</span>
                </button>
            </form>
        </div>

        <details class="adm-gallery-edit">
            <summary>
                <span>Modifier rapidement</span>
                <small>Titre, publication, ordre, remplacement du media.</small>
            </summary>

            <form method="POST" action="{{ route('admin.gallery-photos.update', $photo->id) }}" enctype="multipart/form-data" class="adm-gallery-edit-form">
                @csrf
                @method('PUT')

                <div class="adm-gallery-field-grid adm-gallery-field-grid-2">
                    <div class="adm-gallery-field-card adm-span-2">
                        <label class="adm-gallery-form-label" for="photo_title_{{ $photo->id }}">Titre</label>
                        <input class="tt-form-control" id="photo_title_{{ $photo->id }}" name="title" type="text" value="{{ $photo->title }}" placeholder="Titre">
                    </div>

                    <div class="adm-gallery-field-card adm-span-2">
                        <label class="adm-gallery-form-label" for="photo_description_{{ $photo->id }}">Description</label>
                        <textarea class="tt-form-control" id="photo_description_{{ $photo->id }}" name="description" placeholder="Description">{{ $photo->description }}</textarea>
                    </div>

                    <div class="adm-gallery-field-card">
                        <label class="adm-gallery-form-label" for="photo_filter_key_{{ $photo->id }}">Filter key</label>
                        <input class="tt-form-control" id="photo_filter_key_{{ $photo->id }}" name="filter_key" type="text" value="{{ $photo->filter_key }}" placeholder="filter_key">
                    </div>

                    <div class="adm-gallery-field-card">
                        <label class="adm-gallery-form-label" for="photo_filter_label_{{ $photo->id }}">Filter label</label>
                        <input class="tt-form-control" id="photo_filter_label_{{ $photo->id }}" name="filter_label" type="text" value="{{ $photo->filter_label }}" placeholder="Filter label">
                    </div>

                    <div class="adm-gallery-field-card">
                        <label class="adm-gallery-form-label" for="photo_category_{{ $photo->id }}">Categorie</label>
                        <input class="tt-form-control" id="photo_category_{{ $photo->id }}" name="category_label" type="text" value="{{ $photo->category_label }}" placeholder="Categorie">
                    </div>

                    <div class="adm-gallery-field-card">
                        <label class="adm-gallery-form-label" for="photo_cursor_{{ $photo->id }}">Libelle de survol</label>
                        <input class="tt-form-control" id="photo_cursor_{{ $photo->id }}" name="cursor_label" type="text" value="{{ $photo->cursor_label }}" placeholder="Voir">
                    </div>

                    <div class="adm-gallery-field-card adm-span-2">
                        <label class="adm-gallery-form-label" for="photo_alt_{{ $photo->id }}">Alt text</label>
                        <input class="tt-form-control" id="photo_alt_{{ $photo->id }}" name="alt_text" type="text" value="{{ $photo->alt_text }}" placeholder="Alt text">
                    </div>

                    <div class="adm-gallery-field-card">
                        <label class="adm-gallery-form-label" for="photo_order_{{ $photo->id }}">Ordre</label>
                        <input class="tt-form-control" id="photo_order_{{ $photo->id }}" name="sort_order" type="number" min="0" value="{{ (int) $photo->sort_order }}">
                    </div>

                    <div class="adm-gallery-field-card">
                        <label class="adm-gallery-form-label" for="photo_published_{{ $photo->id }}">Publication</label>
                        <input class="tt-form-control" id="photo_published_{{ $photo->id }}" name="published_at" type="datetime-local" value="{{ optional($photo->published_at)->format('Y-m-d\\TH:i') }}">
                    </div>

                    <div class="adm-gallery-field-card adm-span-2">
                        <label class="adm-gallery-form-label" for="photo_media_{{ $photo->id }}">Remplacer le media</label>
                        <input class="tt-form-control" id="photo_media_{{ $photo->id }}" name="media_file" type="file" accept="image/*,video/mp4,video/webm">
                        <p class="adm-gallery-inline-help">Laissez vide pour conserver le fichier actuel.</p>
                    </div>
                </div>

                <div class="adm-gallery-edit-actions">
                    <label class="adm-gallery-switch" for="photo_active_{{ $photo->id }}">
                        <input id="photo_active_{{ $photo->id }}" name="is_active" type="checkbox" value="1" @checked($photo->is_active)>
                        <span>Actif</span>
                    </label>

                    <div class="adm-gallery-edit-action-buttons">
                        <button type="button" class="tt-btn tt-btn-outline tt-magnetic-item adm-gallery-btn" data-gallery-close-details>
                            <span>Fermer</span>
                        </button>

                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item adm-gallery-btn">
                            <span>Enregistrer</span>
                        </button>
                    </div>
                </div>
            </form>
        </details>
    </div>
</article>
