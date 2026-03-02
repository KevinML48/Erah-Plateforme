<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ERAH Test Console')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header>
    <div class="container header-row">
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="brand">ERAH Test Console</a>

        @auth
            <nav class="nav-links" aria-label="Navigation principale">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('users.index') }}">Users</a>
                <a href="{{ route('ranking.index') }}">Ranking</a>
                <a href="{{ route('clips.index') }}">Clips</a>
                <a href="{{ route('matches.index') }}">Matches</a>
                <a href="{{ route('bets.index') }}">Paris</a>
                <a href="{{ route('wallets.index') }}">Wallets</a>
                <a href="{{ route('leaderboards.index') }}">Classements</a>
                <a href="{{ route('missions.index') }}">Missions</a>
                <a href="{{ route('gifts.index') }}">Gifts</a>
                <a href="{{ route('notifications.index') }}">Notifications</a>
                <a href="{{ route('duels.index') }}">Duels</a>
                <a href="{{ route('profile.show') }}">Profil</a>
                <a href="{{ route('settings.index') }}">Settings</a>

                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('admin.matches.index') }}">Admin Matches</a>
                    <a href="{{ route('admin.clips.index') }}">Admin Clips</a>
                    <a href="{{ route('admin.gifts.index') }}">Admin Gifts</a>
                    <a href="{{ route('admin.missions.index') }}">Admin Missions</a>
                    <a href="{{ route('admin.wallets.grant.create') }}">Admin Wallets</a>
                @endif
            </nav>

            <form method="POST" action="{{ route('auth.logout') }}" class="logout-form">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @else
            <nav class="nav-links" aria-label="Navigation publique">
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                <a href="{{ url('/auth/google/redirect') }}">Google</a>
                <a href="{{ url('/auth/discord/redirect') }}">Discord</a>
            </nav>
        @endauth
    </div>
</header>

<main class="container main-content">
    @if (session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="flash flash-error">
            {{ $errors->first() }}
        </div>
    @endif

    @yield('content')
</main>

<footer>
    <div class="container">
        <small>ERAH - Console front baseline (Blade)</small>
    </div>
</footer>
</body>
</html>
