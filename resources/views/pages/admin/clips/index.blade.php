@extends('marketing.layouts.template')

@section('title', 'Admin Clips | ERAH Plateforme')
@section('meta_description', 'Gestion des clips: creation, publication et moderation.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <link rel="stylesheet" href="/template/assets/css/blog.css">
@endsection

@section('content')
    @php
        $status = $status ?? 'all';
        $clips = $clips ?? collect();
        $fallbackClipImage = '/template/assets/img/galerie/challengers_valorant.jpg';
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Admin Clips',
        'heroDescription' => 'Creation, publication et moderation des clips en une vue compacte.',
        'heroMaskDescription' => 'Workflow contenu clips.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Filtrer les clips</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Passez rapidement entre tous les clips, les clips publies et les brouillons.</p>
                        </div>

                        <div class="adm-filter-actions">
                            <a href="{{ route('admin.clips.create') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Nouveau clip">Nouveau clip</span>
                            </a>
                            <a href="{{ route('admin.clips.index', ['status' => 'all']) }}" class="tt-btn {{ $status === 'all' ? 'tt-btn-secondary' : 'tt-btn-outline' }} tt-magnetic-item">
                                <span data-hover="Tous">Tous</span>
                            </a>
                            <a href="{{ route('admin.clips.index', ['status' => 'published']) }}" class="tt-btn {{ $status === 'published' ? 'tt-btn-secondary' : 'tt-btn-outline' }} tt-magnetic-item">
                                <span data-hover="Publies">Publies</span>
                            </a>
                            <a href="{{ route('admin.clips.index', ['status' => 'draft']) }}" class="tt-btn {{ $status === 'draft' ? 'tt-btn-secondary' : 'tt-btn-outline' }} tt-magnetic-item">
                                <span data-hover="Brouillons">Brouillons</span>
                            </a>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Bibliotheque clips</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Apercus legers, metriques utiles et actions editoriales claires.</p>
                        </div>

                        @if($clips->count())
                            <div id="blog-list" class="bli-image-cropped adm-blog-list adm-clip-list">
                                @foreach($clips as $clip)
                                    @php
                                        $clipStatusLabel = $clip->is_published ? 'Publie' : 'Brouillon';
                                        $clipPublicUrl = $clip->slug ? route('clips.show', $clip->slug) : '#';
                                        $clipThumb = (string) ($clip->thumbnail_url ?: $fallbackClipImage);
                                        $clipDate = optional($clip->published_at ?: $clip->created_at)->format('d/m/Y H:i');
                                        $clipSummary = trim((string) ($clip->description ?? ''));
                                        if ($clipSummary === '') {
                                            $clipSummary = 'Aucune description renseignee pour ce clip.';
                                        }
                                        $clipSummary = \Illuminate\Support\Str::limit($clipSummary, 170);
                                    @endphp
                                    <article class="blog-list-item">
                                        <a href="{{ $clipPublicUrl }}" class="bli-image-wrap" data-cursor="Voir" @if(!$clip->slug) onclick="return false;" @endif>
                                            <figure class="bli-image tt-anim-zoomin">
                                                <img src="{{ $clipThumb }}" loading="lazy" alt="{{ $clip->title }}">
                                            </figure>
                                        </a>

                                        <div class="bli-info">
                                            <div class="bli-categories">
                                                <a href="{{ route('admin.clips.index', ['status' => $clip->is_published ? 'published' : 'draft']) }}">{{ $clipStatusLabel }}</a>
                                                <a href="#">Clip #{{ $clip->id }}</a>
                                            </div>

                                            <h2 class="bli-title"><a href="{{ route('admin.clips.edit', $clip->id) }}">{{ $clip->title }}</a></h2>

                                            <div class="bli-meta">
                                                <span class="published">{{ $clipDate }}</span>
                                                <span class="posted-by">- slug: {{ $clip->slug ?: 'auto' }}</span>
                                            </div>

                                            <div class="bli-desc">{{ $clipSummary }}</div>

                                            <div class="bli-desc">
                                                {{ (int) $clip->likes_count }} likes · {{ (int) $clip->comments_count }} commentaires · {{ (int) $clip->favorites_count }} favoris
                                            </div>

                                            <div class="adm-row-actions">
                                                <a href="{{ route('admin.clips.edit', $clip->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                    <span data-hover="Modifier">Modifier</span>
                                                </a>

                                                @if($clip->slug)
                                                    <a href="{{ $clipPublicUrl }}" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener">
                                                        <span data-hover="Voir le clip">Voir le clip</span>
                                                    </a>
                                                @endif

                                                @if($clip->is_published)
                                                    <form method="POST" action="{{ route('admin.clips.unpublish', $clip->id) }}">
                                                        @csrf
                                                        <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                            <span data-hover="Depublier">Depublier</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.clips.publish', $clip->id) }}">
                                                        @csrf
                                                        <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                            <span data-hover="Publier">Publier</span>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form method="POST" action="{{ route('admin.clips.destroy', $clip->id) }}" onsubmit="return confirm('Supprimer ce clip ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                        <span data-hover="Supprimer">Supprimer</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $clips->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun clip pour ce filtre.</div>
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

