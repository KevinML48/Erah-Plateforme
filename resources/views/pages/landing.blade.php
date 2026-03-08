@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    <section class="section">
        <h1>Plateforme ERAH</h1>
        <p>Base front neutre. Cette page sert de point d'entree fonctionnel.</p>
        <div class="actions">
            @auth
                <a class="button-link" href="{{ route('dashboard') }}">Aller au dashboard</a>
            @else
                <a class="button-link" href="{{ route('login') }}">Se connecter</a>
                <a class="button-link" href="{{ route('register') }}">Creer un compte</a>
                <a class="button-link" href="{{ url('/auth/google/redirect') }}">Google login</a>
                <a class="button-link" href="{{ url('/auth/discord/redirect') }}">Discord login</a>
            @endauth
        </div>
    </section>

    <section class="section">
        <h2>Clips recents</h2>
        @php($clips = $recentClips ?? collect())
        @if($clips->isEmpty())
            <p class="meta">Aucun clip public pour le moment.</p>
        @else
            <ul>
                @foreach($clips as $clip)
                    <li>
                        <a href="{{ route('clips.show', $clip->slug) }}">{{ $clip->title }}</a>
                        <span class="meta">({{ $clip->likes_count }} likes)</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="section">
        <h2>Top ligue</h2>
        @if(empty($leaderboard) || empty($leaderboard['entries']))
            <p class="meta">Leaderboard indisponible.</p>
        @else
            <div class="table-wrap" data-responsive="cards">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Joueur</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($leaderboard['entries'] as $entry)
                        <tr>
                            <td data-label="#">{{ $entry['position'] }}</td>
                            <td data-label="Joueur">{{ $entry['name'] }}</td>
                            <td data-label="Points">{{ $entry['total_rank_points'] ?? $entry['rank_points'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
