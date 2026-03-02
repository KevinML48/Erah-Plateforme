@extends('layouts.app')

@section('title', 'Clips favoris')

@section('content')
    <section class="section">
        <h1>Mes clips favoris</h1>
        <p><a href="{{ route('clips.index') }}">Retour aux clips</a></p>

        @if(($clips ?? null) && $clips->count())
            <ul>
                @foreach($clips as $clip)
                    <li>
                        <a href="{{ route('clips.show', $clip->slug) }}">{{ $clip->title }}</a>
                        <span class="meta">{{ optional($clip->published_at)->format('Y-m-d') }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="actions">{{ $clips->links() }}</div>
        @else
            <p class="meta">Aucun favori.</p>
        @endif
    </section>
@endsection
