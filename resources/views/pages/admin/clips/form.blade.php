@extends('layouts.app')

@section('title', ($clip ? 'Edit clip' : 'Create clip'))

@section('content')
    <section class="section">
        <h1>{{ $clip ? 'Modifier clip #'.$clip->id : 'Nouveau clip' }}</h1>

        <form method="POST" action="{{ $action }}" class="grid">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            <div>
                <label for="title">Titre</label>
                <input id="title" name="title" value="{{ old('title', $clip->title ?? '') }}" required>
            </div>

            <div>
                <label for="slug">Slug (optionnel)</label>
                <input id="slug" name="slug" value="{{ old('slug', $clip->slug ?? '') }}">
            </div>

            <div>
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $clip->description ?? '') }}</textarea>
            </div>

            <div>
                <label for="video_url">Video URL</label>
                <input id="video_url" name="video_url" type="url" value="{{ old('video_url', $clip->video_url ?? '') }}" required>
            </div>

            <div>
                <label for="thumbnail_url">Thumbnail URL</label>
                <input id="thumbnail_url" name="thumbnail_url" type="url" value="{{ old('thumbnail_url', $clip->thumbnail_url ?? '') }}">
            </div>

            <div class="actions">
                <button type="submit">Enregistrer</button>
                <a class="button-link" href="{{ route('admin.clips.index') }}">Retour liste</a>
                @if($clip)
                    <a class="button-link" href="{{ route('clips.show', $clip->slug) }}">Voir public</a>
                @endif
            </div>
        </form>
    </section>
@endsection
