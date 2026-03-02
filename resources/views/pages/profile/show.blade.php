@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    <section class="section">
        <h1>Mon profil</h1>
        <p><strong>{{ $user->name }}</strong> - {{ $user->email }}</p>
        <p class="meta">Role: {{ $user->role }}</p>

        @if($user->avatar_path)
            <p>Avatar:</p>
            <img src="{{ \Illuminate\Support\Str::startsWith($user->avatar_path, ['http://', 'https://']) ? $user->avatar_path : asset('storage/'.$user->avatar_path) }}" alt="Avatar" style="max-width:120px; height:auto;">
        @endif
    </section>

    <section class="section">
        <h2>Progression</h2>
        <ul>
            <li>Ligue: {{ $progress->league->name ?? 'N/A' }}</li>
            <li>Rank points: {{ $progress->total_rank_points ?? 0 }}</li>
            <li>XP total: {{ $progress->xp_total ?? 0 }}</li>
            <li>Likes: {{ $stats['likes'] ?? 0 }}</li>
            <li>Commentaires: {{ $stats['comments'] ?? 0 }}</li>
            <li>Duels: {{ $stats['duels'] ?? 0 }}</li>
            <li>Bets: {{ $stats['bets'] ?? 0 }}</li>
        </ul>
    </section>

    <section class="section">
        <h2>Modifier profil</h2>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="grid">
            @csrf
            @method('PUT')

            <div>
                <label for="name">Nom</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div>
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <div>
                <label for="avatar">Photo de profil</label>
                <input id="avatar" name="avatar" type="file" accept="image/*">
            </div>

            <div class="grid grid-2">
                <div>
                    <label for="twitter_url">Twitter/X</label>
                    <input id="twitter_url" name="twitter_url" type="url" value="{{ old('twitter_url', $user->twitter_url) }}">
                </div>
                <div>
                    <label for="instagram_url">Instagram</label>
                    <input id="instagram_url" name="instagram_url" type="url" value="{{ old('instagram_url', $user->instagram_url) }}">
                </div>
                <div>
                    <label for="tiktok_url">TikTok</label>
                    <input id="tiktok_url" name="tiktok_url" type="url" value="{{ old('tiktok_url', $user->tiktok_url) }}">
                </div>
                <div>
                    <label for="discord_url">Discord</label>
                    <input id="discord_url" name="discord_url" type="url" value="{{ old('discord_url', $user->discord_url) }}">
                </div>
            </div>

            <div class="actions">
                <button type="submit">Mettre a jour</button>
                <a class="button-link" href="{{ route('users.public', $user->id) }}">Voir profil public</a>
            </div>
        </form>
    </section>

    <section class="section">
        <h2>Historique points (30 derniers)</h2>
        @if(($transactions ?? null) && $transactions->count())
            <ul>
                @foreach($transactions as $tx)
                    <li>
                        {{ optional($tx->created_at)->format('Y-m-d H:i') }} - {{ $tx->kind }}: {{ $tx->amount }}
                        <span class="meta">({{ $tx->reason ?? $tx->source_type }})</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="meta">Aucune transaction de points.</p>
        @endif
    </section>
@endsection
