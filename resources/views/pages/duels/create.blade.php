@extends('layouts.app')

@section('title', 'Creer duel')

@section('content')
    <section class="section">
        <h1>Creer un duel</h1>

        <form method="GET" action="{{ route('duels.create') }}" class="grid">
            <div>
                <label for="q">Recherche utilisateur</label>
                <input id="q" name="q" value="{{ $search ?? '' }}" placeholder="nom ou email">
            </div>
            <div class="actions">
                <button type="submit">Rechercher</button>
            </div>
        </form>
    </section>

    <section class="section">
        <h2>Selectionner un joueur</h2>
        @if(($users ?? null) && $users->count())
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $target)
                        <tr>
                            <td>{{ $target->name }}</td>
                            <td>{{ $target->email }}</td>
                            <td>
                                <form method="POST" action="{{ route('duels.store') }}" class="grid">
                                    @csrf
                                    <input type="hidden" name="challenged_user_id" value="{{ $target->id }}">
                                    <input type="hidden" name="idempotency_key" value="duel-{{ auth()->id() }}-{{ $target->id }}-{{ now()->timestamp }}">

                                    <label>Message (optionnel)
                                        <input name="message" placeholder="Message rapide">
                                    </label>

                                    <label>Duree expiration (minutes)
                                        <input type="number" name="expires_in_minutes" min="1" max="10080" value="60">
                                    </label>

                                    <button type="submit">Envoyer duel</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="meta">Aucun utilisateur trouve.</p>
        @endif
    </section>
@endsection
