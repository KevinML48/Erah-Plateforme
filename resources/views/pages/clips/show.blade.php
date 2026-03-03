@extends('layouts.app')

@section('title', $clip->title ?? 'Clip')

@section('content')
    @php($isPublicApp = request()->routeIs('app.*'))

    <section class="section">
        <h1>{{ $clip->title }}</h1>
        <p class="meta">Slug: {{ $clip->slug }}</p>
        <p>{{ $clip->description }}</p>
        <p><a href="{{ $clip->video_url }}" target="_blank" rel="noopener">Ouvrir la video</a></p>

        <p>
            likes: {{ $clip->likes_count }} |
            comments: {{ $clip->comments_count }} |
            favorites: {{ $clip->favorites_count }}
        </p>

        @if($isPublicApp)
            <div class="actions">
                @guest
                    <x-ui.button :href="route('login')" variant="primary" magnetic>Se connecter pour commenter et liker</x-ui.button>
                @else
                    <x-ui.button :href="route('clips.show', $clip->slug)" variant="secondary" magnetic>Ouvrir la version interactive</x-ui.button>
                @endguest
            </div>
        @else
            <div class="actions">
                @if($isLiked)
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

                @if($isFavorited)
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

                <form method="POST" action="{{ route('clips.share', $clip->id) }}">
                    @csrf
                    <input type="hidden" name="channel" value="link">
                    <x-ui.button type="submit" variant="dark">Partager</x-ui.button>
                </form>
            </div>
        @endif
    </section>

    <section class="section">
        <h2>Commentaires</h2>

        @if(!$isPublicApp)
            <form method="POST" action="{{ route('clips.comment', $clip->id) }}" class="grid">
                @csrf
                <div>
                    <label for="body">Ajouter un commentaire</label>
                    <textarea id="body" name="body" required>{{ old('body') }}</textarea>
                </div>
                <div class="actions">
                    <x-ui.button type="submit" variant="primary" magnetic>Publier</x-ui.button>
                </div>
            </form>
        @else
            <p class="meta">Les commentaires sont visibles publiquement. Publication reservee aux utilisateurs connectes dans la console.</p>
        @endif

        <hr>

        @if(($comments ?? null) && $comments->count())
            <ul>
                @foreach($comments as $comment)
                    <li>
                        <strong>{{ $comment->user->name ?? 'User' }}</strong> - {{ $comment->body }}
                        <span class="meta">({{ optional($comment->created_at)->format('Y-m-d H:i') }})</span>

                        @if(!$isPublicApp && (auth()->id() === $comment->user_id || auth()->user()?->role === 'admin'))
                            <form method="POST" action="{{ route('clips.comment.delete', [$clip->id, $comment->id]) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="danger" size="sm">Supprimer</x-ui.button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="actions">{{ $comments->links() }}</div>
        @else
            <p class="meta">Aucun commentaire.</p>
        @endif
    </section>
@endsection
