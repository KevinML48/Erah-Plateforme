@extends('layouts.app')

@section('title', 'Classements')

@section('content')
    @php($isPublicApp = request()->routeIs('app.*'))

    <section class="section">
        <h1>Classements</h1>
        <div class="actions">
            @if($isPublicApp)
                @auth
                    <x-ui.button :href="route('app.leaderboards.me')" variant="secondary" magnetic>Ma ligue</x-ui.button>
                @else
                    <x-ui.button :href="route('login')" variant="primary" magnetic>Connexion pour ma ligue</x-ui.button>
                @endauth
            @else
                <x-ui.button :href="route('leaderboards.me')" variant="secondary" magnetic>Ma ligue</x-ui.button>
            @endif
        </div>

        @if(($leagues ?? null) && $leagues->count())
            <div class="grid grid-3">
                @foreach($leagues as $league)
                    <x-ui.card :title="$league->name" subtitle="Ligue active">
                        <p class="meta">Min points: {{ $league->min_rank_points }}</p>
                        <div class="actions">
                            <x-ui.button :href="route($isPublicApp ? 'app.leaderboards.show' : 'leaderboards.show', $league->key)" variant="outline" size="sm">
                                Ouvrir classement
                            </x-ui.button>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <p class="meta">Aucune ligue active.</p>
        @endif
    </section>
@endsection
