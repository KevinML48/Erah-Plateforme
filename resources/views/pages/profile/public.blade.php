@extends('layouts.app')

@section('title', 'Profil public')

@section('content')
    <section class="section">
        <h1>Profil public</h1>
        <p><strong>{{ $userProfile->name }}</strong></p>

        @if($userProfile->avatar_path)
            <img src="{{ \Illuminate\Support\Str::startsWith($userProfile->avatar_path, ['http://', 'https://']) ? $userProfile->avatar_path : asset('storage/'.$userProfile->avatar_path) }}" alt="Avatar" style="max-width:120px; height:auto;">
        @endif

        <p>{{ $userProfile->bio ?: 'Aucune bio' }}</p>

        <ul>
            <li>Ligue: {{ $progress->league->name ?? 'N/A' }}</li>
            <li>Rank points: {{ $progress->total_rank_points ?? 0 }}</li>
            <li>XP total: {{ $progress->xp_total ?? 0 }}</li>
            <li>Likes: {{ $stats['likes'] ?? 0 }}</li>
            <li>Commentaires: {{ $stats['comments'] ?? 0 }}</li>
            <li>Duels: {{ $stats['duels'] ?? 0 }}</li>
            <li>Bets: {{ $stats['bets'] ?? 0 }}</li>
        </ul>

        <p>
            @if($userProfile->twitter_url)<a href="{{ $userProfile->twitter_url }}" target="_blank" rel="noopener">Twitter/X</a>@endif
            @if($userProfile->instagram_url) | <a href="{{ $userProfile->instagram_url }}" target="_blank" rel="noopener">Instagram</a>@endif
            @if($userProfile->tiktok_url) | <a href="{{ $userProfile->tiktok_url }}" target="_blank" rel="noopener">TikTok</a>@endif
            @if($userProfile->discord_url) | <a href="{{ $userProfile->discord_url }}" target="_blank" rel="noopener">Discord</a>@endif
        </p>

        @if(($viewer?->id ?? null) === $userProfile->id)
            <p><a href="{{ route('profile.show') }}">Retour a mon profil editable</a></p>
        @endif
    </section>

    <section class="section">
        <h2>Transactions recentes</h2>
        @if(($recentTransactions ?? null) && $recentTransactions->count())
            <ul>
                @foreach($recentTransactions as $tx)
                    <li>{{ optional($tx->created_at)->format('Y-m-d H:i') }} - {{ $tx->kind }}: {{ $tx->amount }}</li>
                @endforeach
            </ul>
        @else
            <p class="meta">Aucune transaction recente.</p>
        @endif
    </section>
@endsection
