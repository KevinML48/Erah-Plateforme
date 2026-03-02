@extends('layouts.app')

@section('title', 'Clips')

@section('content')
    <section class="section">
        <h1>Clips</h1>
        <div class="actions">
            <a class="button-link" href="{{ route('clips.index', ['sort' => 'recent']) }}">Recents</a>
            <a class="button-link" href="{{ route('clips.index', ['sort' => 'popular']) }}">Populaires</a>
            <a class="button-link" href="{{ route('clips.favorites') }}">Mes favoris</a>
        </div>
    </section>

    <section class="section">
        @if(($clips ?? null) && $clips->count())
            <div class="table-wrap">
                <table>
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
                                <a href="{{ route('clips.show', $clip->slug) }}">{{ $clip->title }}</a>
                                <p class="meta">{{ \Illuminate\Support\Str::limit((string) $clip->description, 100) }}</p>
                            </td>
                            <td>
                                likes: {{ $clip->likes_count }}<br>
                                comments: {{ $clip->comments_count }}<br>
                                favorites: {{ $clip->favorites_count }}
                            </td>
                            <td>
                                <div class="actions">
                                    @php($liked = in_array($clip->id, $likedIds ?? [], true))
                                    @php($favorited = in_array($clip->id, $favoriteIds ?? [], true))

                                    @if($liked)
                                        <form method="POST" action="{{ route('clips.unlike', $clip->id) }}">
                                            @csrf
                                            <button type="submit">Unlike</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('clips.like', $clip->id) }}">
                                            @csrf
                                            <button type="submit">Like</button>
                                        </form>
                                    @endif

                                    @if($favorited)
                                        <form method="POST" action="{{ route('clips.unfavorite', $clip->id) }}">
                                            @csrf
                                            <button type="submit">Unfavorite</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('clips.favorite', $clip->id) }}">
                                            @csrf
                                            <button type="submit">Favorite</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="actions">{{ $clips->links() }}</div>
        @else
            <p class="meta">Aucun clip disponible.</p>
        @endif
    </section>
@endsection
