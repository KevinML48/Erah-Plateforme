@extends('layouts.app')

@section('title', 'Preferences notifications')

@section('content')
    <section class="section">
        <h1>Preferences notifications</h1>
        <p class="meta">In-app est toujours actif.</p>

        <form method="POST" action="{{ route('notifications.preferences.update') }}" class="grid">
            @csrf

            @php($channelsData = $channels ?? null)
            @php($prefs = $preferences ?? collect())

            <label>
                <input type="checkbox" name="email_opt_in" value="1" {{ old('email_opt_in', $channelsData?->email_opt_in ?? false) ? 'checked' : '' }}>
                Email global
            </label>

            <label>
                <input type="checkbox" name="push_opt_in" value="1" {{ old('push_opt_in', $channelsData?->push_opt_in ?? false) ? 'checked' : '' }}>
                Push global
            </label>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Categorie</th>
                        <th>Email</th>
                        <th>Push</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(['duel', 'clips', 'system', 'match', 'bet'] as $cat)
                        @php($p = $prefs->get($cat))
                        <tr>
                            <td>{{ $cat }}</td>
                            <td><input type="checkbox" name="{{ $cat }}_email" value="1" {{ old($cat.'_email', $p?->email_enabled ?? false) ? 'checked' : '' }}></td>
                            <td><input type="checkbox" name="{{ $cat }}_push" value="1" {{ old($cat.'_push', $p?->push_enabled ?? false) ? 'checked' : '' }}></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="actions">
                <button type="submit">Enregistrer</button>
            </div>
        </form>

        @if(!($hasActiveDevice ?? false))
            <p class="meta">Push non disponible tant qu'aucun device actif n'est enregistre.</p>
        @endif
    </section>
@endsection
