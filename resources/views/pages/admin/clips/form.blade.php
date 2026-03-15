@extends('marketing.layouts.template')

@section('title', ($clip ? 'Edit Clip | Admin ERAH' : 'Create Clip | Admin ERAH'))
@section('meta_description', 'Edition et création de clips dans la console admin.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $clip = $clip ?? null;
        $action = $action ?? route('admin.clips.store');
        $method = $method ?? 'POST';
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => $clip ? 'Modifier Clip' : 'Nouveau Clip',
        'heroDescription' => $clip ? 'Edition des metadonnees et des URLs media du clip.' : 'Creation d'un nouveau clip pour la plateforme.',
        'heroMaskDescription' => 'Formulaire dynamique inspire du template forms.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1400">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">{{ $clip ? 'Clip #'.$clip->id : 'Creation clip' }}</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Conservez des URLs valides pour la video et la miniature, puis publiez depuis la liste clips.</p>
                        </div>

                        <form method="POST" action="{{ $action }}" class="tt-form tt-form-creative adm-form">
                            @csrf
                            @if($method !== 'POST')
                                @method($method)
                            @endif

                            <div class="adm-form-grid">
                                <div class="tt-form-group">
                                    <label for="title">Titre</label>
                                    <input class="tt-form-control" id="title" name="title" value="{{ old('title', $clip->title ?? '') }}" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="slug">Slug (optionnel)</label>
                                    <input class="tt-form-control" id="slug" name="slug" value="{{ old('slug', $clip->slug ?? '') }}">
                                </div>
                            </div>

                            <div class="tt-form-group">
                                <label for="description">Description</label>
                                <textarea class="tt-form-control" id="description" name="description" rows="5">{{ old('description', $clip->description ?? '') }}</textarea>
                            </div>

                            <div class="adm-form-grid">
                                <div class="tt-form-group">
                                    <label for="video_url">Video URL</label>
                                    <input class="tt-form-control" id="video_url" name="video_url" type="url" value="{{ old('video_url', $clip->video_url ?? '') }}" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="thumbnail_url">Thumbnail URL</label>
                                    <input class="tt-form-control" id="thumbnail_url" name="thumbnail_url" type="url" value="{{ old('thumbnail_url', $clip->thumbnail_url ?? '') }}">
                                </div>
                            </div>

                            <div class="adm-row-actions">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Enregistrer">Enregistrer</span>
                                </button>

                                <a href="{{ route('admin.clips.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Retour liste">Retour liste</span>
                                </a>

                                @if($clip && $clip->slug)
                                    <a href="{{ route('clips.show', $clip->slug) }}" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener">
                                        <span data-hover="Voir public">Voir public</span>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
