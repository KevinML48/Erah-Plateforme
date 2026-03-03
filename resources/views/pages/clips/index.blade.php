@extends('layouts.app')

@section('title', 'Clips')

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.clips.index' : 'clips.index';
        $showRouteName = $isPublicApp ? 'app.clips.show' : 'clips.show';
        $favoritesUrl = $isPublicApp
            ? (auth()->check() ? route('app.clips.favorites') : route('login'))
            : route('clips.favorites');
    @endphp

    <section class="section">
        <h1>Clips</h1>
        <div class="actions">
            <x-ui.button :href="route($indexRouteName, ['sort' => 'recent'])" variant="secondary" magnetic>Recents</x-ui.button>
            <x-ui.button :href="route($indexRouteName, ['sort' => 'popular'])" variant="secondary" magnetic>Populaires</x-ui.button>
            <x-ui.button :href="$favoritesUrl" variant="secondary" magnetic>Mes favoris</x-ui.button>
        </div>
        @if($isPublicApp)
            <p class="meta">Mode lecture seule sur /app. Pour liker, commenter ou mettre en favori, connectez-vous et utilisez la console.</p>
        @endif
    </section>

    <section class="section">
        @if(($clips ?? null) && $clips->count())
            <x-ui.table>
                <thead>
                <tr>
                    <th>Titre</th>
                    <th>Stats</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($clips as $clip)
                    <tr>
                        <td>
                            <a href="{{ route($showRouteName, $clip->slug) }}">{{ $clip->title }}</a>
                            <p class="meta">{{ \Illuminate\Support\Str::limit((string) $clip->description, 100) }}</p>
                        </td>
                        <td>
                            likes: {{ $clip->likes_count }}<br>
                            comments: {{ $clip->comments_count }}<br>
                            favorites: {{ $clip->favorites_count }}
                        </td>
                        <td>
                            @if($isPublicApp)
                                @guest
                                    <x-ui.button :href="route('login')" variant="outline">Se connecter pour interagir</x-ui.button>
                                @else
                                    <x-ui.button :href="route('clips.show', $clip->slug)" variant="outline">Ouvrir la console clip</x-ui.button>
                                @endguest
                            @else
                                <div class="actions">
                                    @php($liked = in_array($clip->id, $likedIds ?? [], true))
                                    @php($favorited = in_array($clip->id, $favoriteIds ?? [], true))

                                    @if($liked)
                                        <form method="POST" action="{{ route('clips.unlike', $clip->id) }}">
                                            @csrf
                                            <x-ui.button type="submit" variant="outline">Unlike</x-ui.button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('clips.like', $clip->id) }}">
                                            @csrf
                                            <x-ui.button type="submit" variant="primary">Like</x-ui.button>
                                        </form>
                                    @endif

                                    @if($favorited)
                                        <form method="POST" action="{{ route('clips.unfavorite', $clip->id) }}">
                                            @csrf
                                            <x-ui.button type="submit" variant="outline">Unfavorite</x-ui.button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('clips.favorite', $clip->id) }}">
                                            @csrf
                                            <x-ui.button type="submit" variant="secondary">Favorite</x-ui.button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </x-ui.table>

            <div class="actions">{{ $clips->links() }}</div>
        @else
            <x-ui.empty-state title="Aucun clip disponible" message="Publiez des clips ou changez le filtre." />
        @endif
    </section>
@endsection
