@extends('layouts.app')

@section('title', 'Classements')

@section('content')
    <section class="section">
        <h1>Classements</h1>
        <div class="actions">
            <a class="button-link" href="{{ route('leaderboards.me') }}">Ma ligue</a>
        </div>

        @if(($leagues ?? null) && $leagues->count())
            <ul>
                @foreach($leagues as $league)
                    <li>
                        <a href="{{ route('leaderboards.show', $league->key) }}">{{ $league->name }}</a>
                        <span class="meta">(min points: {{ $league->min_rank_points }})</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="meta">Aucune ligue active.</p>
        @endif
    </section>
@endsection
