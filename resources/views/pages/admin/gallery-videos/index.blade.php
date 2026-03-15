@extends('marketing.layouts.template')

@section('title', 'Admin Galerie Videos | ERAH Plateforme')
@section('meta_description', 'Gestion admin de la galerie video publique.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-video-shell {
            display: grid;
            gap: 24px;
        }

        .adm-video-grid,
        .adm-video-stats,
        .adm-video-toolbar,
        .adm-video-form-grid,
        .adm-video-form-shell,
        .adm-video-card-grid,
        .adm-video-card-actions,
        .adm-video-order-tools,
        .adm-video-flashes {
            display: grid;
            gap: 16px;
        }

        .adm-video-toolbar {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .adm-video-stats {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        .adm-video-stat,
        .adm-video-card,
        .adm-video-flash,
        .adm-video-empty,
        .adm-video-form-wrap {
            border: 1px solid var(--adm-border);
            border-radius: 24px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .035), rgba(255, 255, 255, .015)),
                var(--adm-surface-bg);
            padding: 22px;
        }

        .adm-video-stat strong {
            display: block;
            margin-bottom: 8px;
            color: var(--adm-text);
            font-size: 36px;
            line-height: .95;
        }

        .adm-video-stat span {
            display: block;
            margin-bottom: 8px;
            font-size: 11px;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--adm-text-soft);
        }

        .adm-video-stat p,
        .adm-video-card p,
        .adm-video-flash p,
        .adm-video-empty p {
            margin: 0;
            color: var(--adm-text-soft);
            line-height: 1.6;
        }

        .adm-video-topbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-start;
        }

        .adm-video-topbar h2,
        .adm-video-card h3,
        .adm-video-form-wrap h3 {
            margin: 0;
            color: var(--adm-text);
            line-height: 1;
        }

        .adm-video-topbar h2 {
            font-size: clamp(28px, 3.8vw, 46px);
        }

        .adm-video-topbar p {
            margin: 10px 0 0;
            max-width: 760px;
            color: var(--adm-text-soft);
            line-height: 1.65;
        }

        .adm-video-flash.is-success {
            border-color: rgba(34, 197, 94, .28);
            background: rgba(34, 197, 94, .1);
        }

        .adm-video-flash.is-error {
            border-color: rgba(239, 68, 68, .28);
            background: rgba(239, 68, 68, .1);
        }

        .adm-video-flash.is-info {
            border-color: rgba(59, 130, 246, .28);
            background: rgba(59, 130, 246, .1);
        }

        .adm-video-flash.is-neutral {
            border-color: rgba(148, 163, 184, .24);
            background: rgba(148, 163, 184, .08);
        }

        .adm-video-form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .adm-video-form-shell {
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr);
            align-items: start;
        }

        .adm-video-form-grid .adm-span-2 {
            grid-column: 1 / -1;
        }

        .adm-video-card-grid {
            grid-template-columns: minmax(300px, .9fr) minmax(0, 1.1fr);
            align-items: start;
        }

        .adm-video-card-media {
            overflow: hidden;
            border: 1px solid var(--adm-border-soft);
            border-radius: 20px;
            background: rgba(0, 0, 0, .35);
            aspect-ratio: 16 / 10;
        }

        .adm-video-card-media img,
        .adm-video-card-media video,
        .adm-video-card-media iframe {
            width: 100%;
            height: 100%;
            border: 0;
            object-fit: cover;
            display: block;
        }

        .adm-video-preview {
            display: grid;
            gap: 18px;
            padding: 20px;
            border: 1px solid var(--adm-border-soft);
            border-radius: 20px;
            background: rgba(255, 255, 255, .03);
            position: sticky;
            top: 24px;
        }

        .adm-video-preview-copy {
            display: grid;
            gap: 12px;
        }

        .adm-video-preview-copy h4 {
            margin: 0;
            color: var(--adm-text);
            font-size: 28px;
            line-height: 1;
        }

        .adm-video-preview-copy p {
            margin: 0;
            color: var(--adm-text-soft);
            line-height: 1.6;
        }

        .adm-video-preview-actions {
            display: grid;
            gap: 10px;
        }

        .adm-video-preview-actions .tt-btn {
            width: 100%;
        }

        .adm-video-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 12px 0 16px;
        }

        .adm-video-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--adm-border-soft);
            color: var(--adm-text);
            background: rgba(255, 255, 255, .04);
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .adm-video-card summary {
            cursor: pointer;
            list-style: none;
        }

        .adm-video-card summary::-webkit-details-marker {
            display: none;
        }

        .adm-video-card-actions {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .adm-video-order-tools {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .adm-video-empty {
            text-align: center;
            padding: 48px 32px;
        }

        .adm-video-empty h3 {
            margin-bottom: 12px;
        }

        .adm-video-errors {
            margin: 0;
            padding-left: 18px;
            color: #fecaca;
        }

        body.tt-lightmode-on .adm-video-errors {
            color: #b91c1c;
        }

        @media (max-width: 1200px) {
            .adm-video-stats,
            .adm-video-toolbar,
            .adm-video-card-actions,
            .adm-video-order-tools {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .adm-video-form-shell,
            .adm-video-card-grid {
                grid-template-columns: 1fr;
            }

            .adm-video-preview {
                position: static;
            }
        }

        @media (max-width: 767px) {
            .adm-video-stats,
            .adm-video-toolbar,
            .adm-video-form-grid,
            .adm-video-card-actions,
            .adm-video-order-tools {
                grid-template-columns: 1fr;
            }

            .adm-video-form-wrap,
            .adm-video-card {
                padding: 18px;
                border-radius: 20px;
            }

            .adm-video-form-shell {
                gap: 14px;
            }

            .adm-video-preview {
                order: -1;
                gap: 14px;
                padding: 14px;
                border-radius: 18px;
            }

            .adm-video-card-media {
                aspect-ratio: 16 / 9;
                border-radius: 16px;
            }

            .adm-video-preview-copy {
                gap: 10px;
            }

            .adm-video-preview-copy h4 {
                font-size: 22px;
            }

            .adm-video-card-meta {
                gap: 8px;
                margin: 0;
            }

            .adm-video-pill {
                padding: 7px 10px;
                font-size: 11px;
            }

            .adm-video-preview-actions,
            .adm-filter-actions {
                width: 100%;
            }

            .adm-video-preview-actions .tt-btn,
            .adm-filter-actions .tt-btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
@php
    use App\Models\GalleryVideo;
    use Illuminate\Support\Str;

    $videos = $videos ?? collect();
    $stats = $stats ?? [];
    $filters = $filters ?? [];
    $sortOptions = $sortOptions ?? [];
    $categories = $categories ?? collect();
    $defaultPoster = '/template/assets/img/logo-fond.png';
@endphp

@include('pages.admin.partials.hero', [
    'heroSubtitle' => 'Pilotage contenu',
    'heroTitle' => 'Galerie Videos',
    'heroDescription' => 'Restaurez le contenu historique, pilotez les mises en avant et gardez la page publique galerie-video alimentee sans dependance statique.',
    'heroMaskDescription' => 'Creation, publication, archivage et ordre de la galerie video publique.',
    'heroVideoPoster' => '/template/assets/img/logo-fond.png',
])

<div id="tt-page-content">
    <div class="tt-section padding-bottom-xlg-80">
        <div class="tt-section-inner tt-wrap max-width-1800">
            <div class="adm-shell adm-video-shell">
                @include('pages.admin.partials.nav')

                <section class="adm-surface">
                    <div class="adm-video-topbar">
                        <div>
                            <h2>Console galerie video</h2>
                            <p>Cette interface alimente la page publique `/galerie-video`. Les videos du template historique sont reimportees automatiquement si la galerie est vide, puis vous gardez la main sur les titres, categories, previews, ordre, statut et mise en avant.</p>
                        </div>

                        <div class="adm-filter-actions">
                            <form method="POST" action="{{ route('admin.gallery-videos.import-legacy') }}">
                                @csrf
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Relancer l'import legacy">Relancer l'import legacy</span>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.gallery-videos.import-legacy-if-empty') }}">
                                @csrf
                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Importer si vide">Importer si vide</span>
                                </button>
                            </form>
                            <a href="{{ route('marketing.gallery-video') }}" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener">
                                <span data-hover="Voir la page publique">Voir la page publique</span>
                            </a>
                            <a href="{{ route('admin.gallery-videos.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Rafraichir la console">Rafraichir la console</span>
                            </a>
                        </div>
                    </div>
                </section>

                <section class="adm-video-flashes">
                    @if(session('success'))
                        <article class="adm-video-flash is-success"><p>{{ session('success') }}</p></article>
                    @endif

                    @if(session('info'))
                        <article class="adm-video-flash is-neutral"><p>{{ session('info') }}</p></article>
                    @endif

                    @if(($autoImportedCount ?? 0) > 0)
                        <article class="adm-video-flash is-info"><p>{{ $autoImportedCount }} video(s) historiques importee(s) depuis le template original.</p></article>
                    @endif

                    @if($errors->any())
                        <article class="adm-video-flash is-error">
                            <p>La derniere operation n'a pas abouti.</p>
                            <ul class="adm-video-errors">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endif
                </section>

                <section class="adm-video-stats">
                    <article class="adm-video-stat"><span>Total</span><strong>{{ (int) ($stats['total'] ?? 0) }}</strong><p>Toutes les videos de la galerie admin.</p></article>
                    <article class="adm-video-stat"><span>Publiees</span><strong>{{ (int) ($stats['published'] ?? 0) }}</strong><p>Visibles cote public selon date de publication.</p></article>
                    <article class="adm-video-stat"><span>Brouillons</span><strong>{{ (int) ($stats['drafts'] ?? 0) }}</strong><p>En attente de validation ou de diffusion.</p></article>
                    <article class="adm-video-stat"><span>Archivees</span><strong>{{ (int) ($stats['archived'] ?? 0) }}</strong><p>Retirees du front sans etre supprimees.</p></article>
                    <article class="adm-video-stat"><span>A la une</span><strong>{{ (int) ($stats['featured'] ?? 0) }}</strong><p>Mises en avant en tete de la galerie.</p></article>
                    <article class="adm-video-stat"><span>Legacy</span><strong>{{ (int) ($stats['legacy'] ?? 0) }}</strong><p>Issues du template historique importe.</p></article>
                </section>

                <section class="adm-video-form-wrap">
                    <div class="adm-video-topbar">
                        <div>
                            <h3>Filtrer la bibliothèque</h3>
                            <p>Recherchez rapidement une video, une categorie ou un statut avant edition.</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.gallery-videos.index') }}" class="adm-form tt-form tt-form-creative tt-form-lg">
                        <div class="adm-video-toolbar">
                            <div class="tt-form-group">
                                <label for="filter-q">Recherche</label>
                                <input id="filter-q" type="text" name="q" class="tt-form-control" value="{{ $filters['q'] ?? '' }}" placeholder="Titre, categorie, URL, description">
                            </div>

                            <div class="tt-form-group">
                                <label for="filter-status">Statut</label>
                                <select id="filter-status" name="status" class="tt-form-control">
                                    <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Tous</option>
                                    <option value="published" @selected(($filters['status'] ?? '') === 'published')>Publiees</option>
                                    <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Brouillons</option>
                                    <option value="archived" @selected(($filters['status'] ?? '') === 'archived')>Archivees</option>
                                </select>
                            </div>

                            <div class="tt-form-group">
                                <label for="filter-category">Categorie</label>
                                <select id="filter-category" name="category" class="tt-form-control">
                                    <option value="all" @selected(($filters['category'] ?? 'all') === 'all')>Toutes</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_key }}" @selected(($filters['category'] ?? '') === $category->category_key)>
                                            {{ $category->category_label ?: Str::headline($category->category_key) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="tt-form-group">
                                <label for="filter-source">Source</label>
                                <select id="filter-source" name="source" class="tt-form-control">
                                    <option value="all" @selected(($filters['source'] ?? 'all') === 'all')>Toutes</option>
                                    <option value="legacy" @selected(($filters['source'] ?? '') === 'legacy')>Legacy</option>
                                    <option value="manual" @selected(($filters['source'] ?? '') === 'manual')>Ajout manuel</option>
                                </select>
                            </div>

                            <div class="tt-form-group">
                                <label for="filter-sort">Tri</label>
                                <select id="filter-sort" name="sort" class="tt-form-control">
                                    @foreach($sortOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(($filters['sort'] ?? 'manual') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="adm-filter-actions" style="margin-top: 16px;">
                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Appliquer">Appliquer</span></button>
                            <a href="{{ route('admin.gallery-videos.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Reinitialiser">Reinitialiser</span></a>
                        </div>
                    </form>
                </section>

                <section class="adm-video-form-wrap">
                    <div class="adm-video-topbar">
                        <div>
                            <h3>Ajouter une vidéo</h3>
                            <p>Utilisez l'URL publique comme source, ajoutez une preview vidéo si besoin, et sinon la galerie affichera simplement le logo par défaut quand aucune image n'est fournie.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.gallery-videos.store') }}" class="adm-form tt-form tt-form-creative tt-form-lg" enctype="multipart/form-data" data-video-preview-root>
                        @csrf

                        <div class="adm-video-form-shell">
                            <div class="adm-video-form-grid">
                                <div class="tt-form-group"><label for="create-title">Titre</label><input id="create-title" type="text" name="title" class="tt-form-control" value="{{ old('title') }}" required data-preview-field="title"></div>
                                <div class="tt-form-group"><label for="create-slug">Slug</label><input id="create-slug" type="text" name="slug" class="tt-form-control" value="{{ old('slug') }}" placeholder="Genere automatiquement si vide"></div>
                                <div class="tt-form-group adm-span-2"><label for="create-excerpt">Accroche courte</label><textarea id="create-excerpt" name="excerpt" class="tt-form-control" placeholder="Texte court affiche dans les cartes" data-preview-field="excerpt">{{ old('excerpt') }}</textarea></div>
                                <div class="tt-form-group adm-span-2"><label for="create-description">Description detaillee</label><textarea id="create-description" name="description" class="tt-form-control" placeholder="Contexte, evenement, contenu de la video" data-preview-field="description">{{ old('description') }}</textarea></div>
                                <div class="tt-form-group"><label for="create-platform">Plateforme</label><select id="create-platform" name="platform" class="tt-form-control" data-preview-field="platform"><option value="youtube" @selected(old('platform', 'youtube') === 'youtube')>YouTube</option><option value="twitch" @selected(old('platform') === 'twitch')>Twitch</option><option value="other" @selected(old('platform') === 'other')>Autre</option></select></div>
                                <div class="tt-form-group"><label for="create-video-url">URL video</label><input id="create-video-url" type="url" name="video_url" class="tt-form-control" value="{{ old('video_url') }}" required data-preview-field="video_url"></div>
                                <div class="tt-form-group"><label for="create-embed-url">URL embed</label><input id="create-embed-url" type="url" name="embed_url" class="tt-form-control" value="{{ old('embed_url') }}" placeholder="Optionnel si vous voulez forcer l'embed" data-preview-field="embed_url"></div>
                                <div class="tt-form-group"><label for="create-thumbnail-url">URL miniature</label><input id="create-thumbnail-url" type="url" name="thumbnail_url" class="tt-form-control" value="{{ old('thumbnail_url') }}" placeholder="Optionnel, sinon logo par defaut" data-preview-field="thumbnail_url"></div>
                                <div class="tt-form-group"><label for="create-thumbnail-image">Miniature uploadee</label><input id="create-thumbnail-image" type="file" name="thumbnail_image" class="tt-form-control" accept="image/png,image/jpeg,image/webp,image/avif" data-preview-field="thumbnail_image"></div>
                                <div class="tt-form-group"><label for="create-preview-video-url">Preview MP4</label><input id="create-preview-video-url" type="url" name="preview_video_url" class="tt-form-control" value="{{ old('preview_video_url') }}" placeholder="Utilisee dans le slider plein ecran" data-preview-field="preview_video_url"></div>
                                <div class="tt-form-group"><label for="create-preview-video-webm-url">Preview WebM</label><input id="create-preview-video-webm-url" type="url" name="preview_video_webm_url" class="tt-form-control" value="{{ old('preview_video_webm_url') }}"></div>
                                <div class="tt-form-group"><label for="create-category-key">Cle categorie</label><input id="create-category-key" type="text" name="category_key" class="tt-form-control" value="{{ old('category_key') }}" placeholder="valorant, lan, club"></div>
                                <div class="tt-form-group"><label for="create-category-label">Libelle categorie</label><input id="create-category-label" type="text" name="category_label" class="tt-form-control" value="{{ old('category_label') }}" placeholder="Valorant, LAN, Club" data-preview-field="category_label"></div>
                                <div class="tt-form-group"><label for="create-status">Statut</label><select id="create-status" name="status" class="tt-form-control" data-preview-field="status"><option value="draft" @selected(old('status', 'draft') === 'draft')>Brouillon</option><option value="published" @selected(old('status') === 'published')>Publiee</option><option value="archived" @selected(old('status') === 'archived')>Archivee</option></select></div>
                                <div class="tt-form-group"><label for="create-sort-order">Ordre</label><input id="create-sort-order" type="number" name="sort_order" class="tt-form-control" value="{{ old('sort_order', 0) }}" min="0"></div>
                                <div class="tt-form-group"><label for="create-published-at">Date publication</label><input id="create-published-at" type="datetime-local" name="published_at" class="tt-form-control" value="{{ old('published_at') }}"></div>
                                <div class="tt-form-group"><label class="tt-form-check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured')) data-preview-field="is_featured"><span>Mise en avant</span></label></div>
                            </div>

                            <aside class="adm-video-preview" aria-live="polite">
                                <div class="adm-video-card-media" data-preview-media>
                                    <img src="{{ old('thumbnail_url') ?: $defaultPoster }}" alt="Apercu miniature" data-preview-image>
                                    <video muted playsinline loop preload="metadata" style="display: none;" data-preview-video></video>
                                </div>

                                <div class="adm-video-preview-copy">
                                    <div class="adm-video-card-meta">
                                        <span class="adm-video-pill" data-preview-status>{{ old('status', 'draft') === 'published' ? 'Publiee' : (old('status') === 'archived' ? 'Archivee' : 'Brouillon') }}</span>
                                        <span class="adm-video-pill" data-preview-category>{{ old('category_label') ?: 'Categorie' }}</span>
                                        <span class="adm-video-pill" data-preview-platform>{{ Str::headline(old('platform', 'youtube')) }}</span>
                                        <span class="adm-video-pill" data-preview-featured @if(! old('is_featured')) style="display: none;" @endif>A la une</span>
                                    </div>
                                    <h4 data-preview-title>{{ old('title') ?: 'Titre de la video' }}</h4>
                                    <p data-preview-summary>{{ old('excerpt') ?: (old('description') ?: 'L apercu se met a jour pendant la saisie pour verifier le rendu editorial et visuel.') }}</p>
                                    <div class="adm-video-preview-actions">
                                        <a href="{{ old('video_url') ?: '#' }}" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener" data-preview-link>
                                            <span data-hover="Ouvrir la video">Ouvrir la video</span>
                                        </a>
                                    </div>
                                </div>
                            </aside>
                        </div>

                        <div class="adm-filter-actions" style="margin-top: 18px;">
                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Creer la video">Creer la video</span></button>
                        </div>
                    </form>
                </section>

                @if($videos->count())
                    @foreach($videos as $video)
                        @php
                            $embedUrl = $video->resolved_embed_url;

                            if ($embedUrl && Str::contains($embedUrl, ['player.twitch.tv', 'clips.twitch.tv/embed'])) {
                                $embedUrl .= (Str::contains($embedUrl, '?') ? '&' : '?').'parent='.request()->getHost();
                            }
                        @endphp

                        <details class="adm-video-card" @if(request()->integer('open') === $video->id) open @endif>
                            <summary>
                                <div class="adm-video-topbar">
                                    <div>
                                        <h3>{{ $video->title }}</h3>
                                        <div class="adm-video-card-meta">
                                            <span class="adm-video-pill">{{ $video->status_label }}</span>
                                            <span class="adm-video-pill">{{ $video->display_category_label }}</span>
                                            <span class="adm-video-pill">{{ Str::headline($video->platform) }}</span>
                                            <span class="adm-video-pill">Ordre {{ $video->sort_order }}</span>
                                            @if($video->is_featured)
                                                <span class="adm-video-pill">A la une</span>
                                            @endif
                                            @if($video->legacy_source)
                                                <span class="adm-video-pill">Template historique</span>
                                            @endif
                                        </div>
                                        <p>{{ $video->excerpt ?: Str::limit($video->description ?: 'Aucune description renseignee.', 190) }}</p>
                                    </div>

                                    <div class="adm-filter-actions">
                                        <a href="{{ $video->video_url }}" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener"><span data-hover="Ouvrir la video">Ouvrir la video</span></a>
                                    </div>
                                </div>
                            </summary>

                            <div class="adm-video-card-grid" style="margin-top: 22px;">
                                <div class="adm-video-card-media">
                                    @if($embedUrl)
                                        <iframe src="{{ $embedUrl }}" title="{{ $video->title }}" loading="lazy" allowfullscreen></iframe>
                                    @elseif($video->resolved_thumbnail_url)
                                        <img src="{{ $video->resolved_thumbnail_url }}" alt="{{ $video->title }}" loading="lazy">
                                    @else
                                        <img src="/template/assets/img/logo-fond.png" alt="{{ $video->title }}" loading="lazy">
                                    @endif
                                </div>

                                <div class="adm-video-grid">
                                    <form method="POST" action="{{ route('admin.gallery-videos.update', $video->id) }}" class="adm-form tt-form tt-form-creative tt-form-lg" enctype="multipart/form-data" data-video-preview-root>
                                        @csrf
                                        @method('PUT')

                                        <div class="adm-video-form-shell">
                                            <div class="adm-video-form-grid">
                                                <div class="tt-form-group"><label for="title-{{ $video->id }}">Titre</label><input id="title-{{ $video->id }}" type="text" name="title" class="tt-form-control" value="{{ $video->title }}" required data-preview-field="title"></div>
                                                <div class="tt-form-group"><label for="slug-{{ $video->id }}">Slug</label><input id="slug-{{ $video->id }}" type="text" name="slug" class="tt-form-control" value="{{ $video->slug }}"></div>
                                                <div class="tt-form-group adm-span-2"><label for="excerpt-{{ $video->id }}">Accroche courte</label><textarea id="excerpt-{{ $video->id }}" name="excerpt" class="tt-form-control" data-preview-field="excerpt">{{ $video->excerpt }}</textarea></div>
                                                <div class="tt-form-group adm-span-2"><label for="description-{{ $video->id }}">Description detaillee</label><textarea id="description-{{ $video->id }}" name="description" class="tt-form-control" data-preview-field="description">{{ $video->description }}</textarea></div>
                                                <div class="tt-form-group"><label for="platform-{{ $video->id }}">Plateforme</label><select id="platform-{{ $video->id }}" name="platform" class="tt-form-control" data-preview-field="platform"><option value="youtube" @selected($video->platform === 'youtube')>YouTube</option><option value="twitch" @selected($video->platform === 'twitch')>Twitch</option><option value="other" @selected($video->platform === 'other')>Autre</option></select></div>
                                                <div class="tt-form-group"><label for="video-url-{{ $video->id }}">URL video</label><input id="video-url-{{ $video->id }}" type="url" name="video_url" class="tt-form-control" value="{{ $video->video_url }}" required data-preview-field="video_url"></div>
                                                <div class="tt-form-group"><label for="embed-url-{{ $video->id }}">URL embed</label><input id="embed-url-{{ $video->id }}" type="url" name="embed_url" class="tt-form-control" value="{{ $video->embed_url }}" data-preview-field="embed_url"></div>
                                                <div class="tt-form-group"><label for="thumbnail-url-{{ $video->id }}">URL miniature</label><input id="thumbnail-url-{{ $video->id }}" type="url" name="thumbnail_url" class="tt-form-control" value="{{ $video->thumbnail_url }}" data-preview-field="thumbnail_url"></div>
                                                <div class="tt-form-group"><label for="thumbnail-image-{{ $video->id }}">Miniature uploadee</label><input id="thumbnail-image-{{ $video->id }}" type="file" name="thumbnail_image" class="tt-form-control" accept="image/png,image/jpeg,image/webp,image/avif" data-preview-field="thumbnail_image"></div>
                                                <div class="tt-form-group"><label for="preview-url-{{ $video->id }}">Preview MP4</label><input id="preview-url-{{ $video->id }}" type="url" name="preview_video_url" class="tt-form-control" value="{{ $video->preview_video_url }}" data-preview-field="preview_video_url"></div>
                                                <div class="tt-form-group"><label for="preview-webm-url-{{ $video->id }}">Preview WebM</label><input id="preview-webm-url-{{ $video->id }}" type="url" name="preview_video_webm_url" class="tt-form-control" value="{{ $video->preview_video_webm_url }}"></div>
                                                <div class="tt-form-group"><label for="category-key-{{ $video->id }}">Cle categorie</label><input id="category-key-{{ $video->id }}" type="text" name="category_key" class="tt-form-control" value="{{ $video->category_key }}"></div>
                                                <div class="tt-form-group"><label for="category-label-{{ $video->id }}">Libelle categorie</label><input id="category-label-{{ $video->id }}" type="text" name="category_label" class="tt-form-control" value="{{ $video->category_label }}" data-preview-field="category_label"></div>
                                                <div class="tt-form-group"><label for="status-{{ $video->id }}">Statut</label><select id="status-{{ $video->id }}" name="status" class="tt-form-control" data-preview-field="status"><option value="draft" @selected($video->status === GalleryVideo::STATUS_DRAFT)>Brouillon</option><option value="published" @selected($video->status === GalleryVideo::STATUS_PUBLISHED)>Publiee</option><option value="archived" @selected($video->status === GalleryVideo::STATUS_ARCHIVED)>Archivee</option></select></div>
                                                <div class="tt-form-group"><label for="sort-order-{{ $video->id }}">Ordre</label><input id="sort-order-{{ $video->id }}" type="number" name="sort_order" class="tt-form-control" value="{{ $video->sort_order }}" min="0"></div>
                                                <div class="tt-form-group"><label for="published-at-{{ $video->id }}">Date publication</label><input id="published-at-{{ $video->id }}" type="datetime-local" name="published_at" class="tt-form-control" value="{{ $video->published_at?->format('Y-m-d\TH:i') }}"></div>
                                                <div class="tt-form-group"><label class="tt-form-check"><input type="checkbox" name="is_featured" value="1" @checked($video->is_featured) data-preview-field="is_featured"><span>Mise en avant</span></label></div>
                                            </div>

                                            <aside class="adm-video-preview" aria-live="polite">
                                                <div class="adm-video-card-media" data-preview-media>
                                                    <img src="{{ $video->resolved_thumbnail_url ?: $defaultPoster }}" alt="Apercu miniature" data-preview-image>
                                                    <video muted playsinline loop preload="metadata" style="display: none;" data-preview-video></video>
                                                </div>

                                                <div class="adm-video-preview-copy">
                                                    <div class="adm-video-card-meta">
                                                        <span class="adm-video-pill" data-preview-status>{{ $video->status_label }}</span>
                                                        <span class="adm-video-pill" data-preview-category>{{ $video->display_category_label }}</span>
                                                        <span class="adm-video-pill" data-preview-platform>{{ Str::headline($video->platform) }}</span>
                                                        <span class="adm-video-pill" data-preview-featured @if(! $video->is_featured) style="display: none;" @endif>A la une</span>
                                                        @if($video->legacy_source)
                                                            <span class="adm-video-pill">Source {{ $video->legacy_source }}</span>
                                                        @endif
                                                    </div>
                                                    <h4 data-preview-title>{{ $video->title }}</h4>
                                                    <p data-preview-summary>{{ $video->excerpt ?: ($video->description ?: 'L apercu se met a jour pendant la saisie.') }}</p>
                                                    <div class="adm-video-preview-actions">
                                                        <a href="{{ $video->video_url }}" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener" data-preview-link>
                                                            <span data-hover="Ouvrir la video">Ouvrir la video</span>
                                                        </a>
                                                        @if($video->thumbnail_url)
                                                            <form method="POST" action="{{ route('admin.gallery-videos.remove-thumbnail', $video->id) }}" onsubmit="return confirm('Retirer la miniature de cette video ?');">
                                                                @csrf
                                                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Retirer la miniature">Retirer la miniature</span></button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </aside>
                                        </div>

                                        <div class="adm-filter-actions" style="margin-top: 18px;">
                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Enregistrer">Enregistrer</span></button>
                                        </div>
                                    </form>

                                    <div class="adm-video-grid">
                                        <div class="adm-video-card-actions">
                                            <form method="POST" action="{{ route('admin.gallery-videos.publish', $video->id) }}">
                                                @csrf
                                                <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item" style="width: 100%;"><span data-hover="Publier">Publier</span></button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.gallery-videos.unpublish', $video->id) }}">
                                                @csrf
                                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item" style="width: 100%;"><span data-hover="Remettre en brouillon">Brouillon</span></button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.gallery-videos.archive', $video->id) }}">
                                                @csrf
                                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item" style="width: 100%;"><span data-hover="Archiver">Archiver</span></button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.gallery-videos.destroy', $video->id) }}" onsubmit="return confirm('Supprimer cette video de la galerie ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item" style="width: 100%;"><span data-hover="Supprimer">Supprimer</span></button>
                                            </form>
                                        </div>

                                        <div class="adm-video-order-tools">
                                            <form method="POST" action="{{ route('admin.gallery-videos.reorder', $video->id) }}">@csrf<input type="hidden" name="direction" value="top"><button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item" style="width: 100%;"><span data-hover="Tout en haut">Top</span></button></form>
                                            <form method="POST" action="{{ route('admin.gallery-videos.reorder', $video->id) }}">@csrf<input type="hidden" name="direction" value="up"><button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item" style="width: 100%;"><span data-hover="Monter">Monter</span></button></form>
                                            <form method="POST" action="{{ route('admin.gallery-videos.reorder', $video->id) }}">@csrf<input type="hidden" name="direction" value="down"><button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item" style="width: 100%;"><span data-hover="Descendre">Descendre</span></button></form>
                                            <form method="POST" action="{{ route('admin.gallery-videos.reorder', $video->id) }}">@csrf<input type="hidden" name="direction" value="bottom"><button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item" style="width: 100%;"><span data-hover="Tout en bas">Bottom</span></button></form>
                                            <form method="POST" action="{{ route('admin.gallery-videos.reorder', $video->id) }}">@csrf<input type="number" name="sort_order" class="tt-form-control" value="{{ $video->sort_order }}" min="0"><button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item" style="width: 100%; margin-top: 10px;"><span data-hover="Fixer l'ordre">Fixer l'ordre</span></button></form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </details>
                    @endforeach

                    <div class="adm-pagin">{{ $videos->links('vendor.pagination.admin') }}</div>
                @else
                    <div class="adm-video-empty">
                        <h3>Aucune vidéo trouvée</h3>
                        <p>Ajustez vos filtres ou ajoutez une première vidéo pour alimenter la galerie publique.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var previewRoots = Array.prototype.slice.call(document.querySelectorAll('[data-video-preview-root]'));

            previewRoots.forEach(function (root) {
                var title = root.querySelector('[data-preview-title]');
                var summary = root.querySelector('[data-preview-summary]');
                var category = root.querySelector('[data-preview-category]');
                var platform = root.querySelector('[data-preview-platform]');
                var status = root.querySelector('[data-preview-status]');
                var featured = root.querySelector('[data-preview-featured]');
                var image = root.querySelector('[data-preview-image]');
                var video = root.querySelector('[data-preview-video]');
                var link = root.querySelector('[data-preview-link]');
                var fields = Array.prototype.slice.call(root.querySelectorAll('[data-preview-field]'));

                var syncPreview = function () {
                    var values = {};

                    fields.forEach(function (field) {
                        var key = field.getAttribute('data-preview-field');

                        if (field.type === 'checkbox') {
                            values[key] = field.checked;
                            return;
                        }

                        if (field.type === 'file') {
                            values[key] = field.files && field.files[0] ? field.files[0] : null;
                            return;
                        }

                        values[key] = field.value || '';
                    });

                    title.textContent = values.title || 'Titre de la video';
                    summary.textContent = values.excerpt || values.description || 'L apercu se met a jour pendant la saisie pour verifier le rendu editorial et visuel.';
                    category.textContent = values.category_label || 'Categorie';
                    platform.textContent = values.platform ? values.platform.charAt(0).toUpperCase() + values.platform.slice(1) : 'Plateforme';

                    if (values.status === 'published') {
                        status.textContent = 'Publiee';
                    } else if (values.status === 'archived') {
                        status.textContent = 'Archivee';
                    } else {
                        status.textContent = 'Brouillon';
                    }

                    featured.style.display = values.is_featured ? 'inline-flex' : 'none';

                    link.setAttribute('href', values.video_url || '#');

                    if (values.thumbnail_image instanceof File) {
                        var reader = new FileReader();
                        reader.onload = function (event) {
                            image.src = event.target && event.target.result ? event.target.result : image.src;
                            image.style.display = 'block';
                            video.pause();
                            video.removeAttribute('src');
                            video.load();
                            video.style.display = 'none';
                        };
                        reader.readAsDataURL(values.thumbnail_image);

                        return;
                    }

                    if (values.preview_video_url) {
                        video.src = values.preview_video_url;
                        video.style.display = 'block';
                        image.style.display = 'none';
                        video.load();
                        return;
                    }

                    video.pause();
                    video.removeAttribute('src');
                    video.load();
                    video.style.display = 'none';
                    image.style.display = 'block';
                    image.src = values.thumbnail_url || image.getAttribute('src') || '{{ $defaultPoster }}';
                };

                fields.forEach(function (field) {
                    field.addEventListener('input', syncPreview);
                    field.addEventListener('change', syncPreview);
                });

                syncPreview();
            });
        });
    </script>
@endsection