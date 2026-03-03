@extends('layouts.app')

@section('title', 'Clips favoris')

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.clips.index' : 'clips.index';
        $showRouteName = $isPublicApp ? 'app.clips.show' : 'clips.show';
    @endphp

    <section class="section">
        <h1>Mes clips favoris</h1>
        <div class="actions">
            <x-ui.button :href="route($indexRouteName)" variant="secondary" magnetic>Retour aux clips</x-ui.button>
        </div>

        @if(($clips ?? null) && $clips->count())
            <div class="grid grid-3">
                @foreach($clips as $clip)
                    <x-ui.card :title="$clip->title" subtitle="Favori">
                        <p class="meta">Publication: {{ optional($clip->published_at)->format('Y-m-d') ?: 'N/A' }}</p>
                        <div class="actions">
                            <x-ui.button :href="route($showRouteName, $clip->slug)" variant="outline" size="sm">Voir clip</x-ui.button>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>

            <div class="actions">{{ $clips->links() }}</div>
        @else
            <x-ui.empty-state title="Aucun favori" message="Ajoutez des clips en favoris depuis la liste clips." />
        @endif
    </section>
@endsection
