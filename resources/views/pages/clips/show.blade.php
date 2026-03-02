@extends('layouts.app')

@section('title', $clip->title ?? 'Clip')

@section('content')
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

        <div class="actions">
            @if($isLiked)
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

            @if($isFavorited)
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

            <form method="POST" action="{{ route('clips.share', $clip->id) }}">
                @csrf
                <input type="hidden" name="channel" value="link">
                <button type="submit">Partager</button>
            </form>
        </div>
    </section>

    <section class="section">
        <h2>Commentaires</h2>

        <form method="POST" action="{{ route('clips.comment', $clip->id) }}" class="grid">
            @csrf
            <div>
                <label for="body">Ajouter un commentaire</label>
                <textarea id="body" name="body" required>{{ old('body') }}</textarea>
            </div>
            <div class="actions">
                <button type="submit">Publier</button>
            </div>
        </form>

        <hr>

        @if(($comments ?? null) && $comments->count())
            <ul>
                @foreach($comments as $comment)
                    <li>
                        <strong>{{ $comment->user->name ?? 'User' }}</strong> - {{ $comment->body }}
                        <span class="meta">({{ optional($comment->created_at)->format('Y-m-d H:i') }})</span>

                        @if(auth()->id() === $comment->user_id || auth()->user()?->role === 'admin')
                            <form method="POST" action="{{ route('clips.comment.delete', [$clip->id, $comment->id]) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Supprimer</button>
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
