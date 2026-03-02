<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', 'ERAH Plateforme')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Plateforme ERAH">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header>
    <div class="container">
        <div class="header-row">
            <a class="brand" href="{{ auth()->check() ? route('dashboard') : route('marketing.index') }}">
                Plateforme ERAH
            </a>

            <nav class="nav-links">
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('matches.index') }}">Matches</a>
                    <a href="{{ route('clips.index') }}">Clips</a>
                    <a href="{{ route('leaderboards.index') }}">Classements</a>
                    <a href="{{ route('missions.index') }}">Missions</a>
                    <a href="{{ route('gifts.index') }}">Gifts</a>
                    <a href="{{ route('notifications.index') }}">Notifications</a>
                    <a href="{{ route('profile.show') }}">Profil</a>
                    <a href="{{ route('app.leaderboards.index') }}">Vue publique /app</a>

                    <form method="POST" action="{{ route('auth.logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="btn btn-danger">Deconnexion</button>
                    </form>
                @else
                    <a href="{{ route('marketing.index') }}">Accueil site</a>
                    <a href="{{ route('app.leaderboards.index') }}">Plateforme /app</a>
                    <a href="{{ route('login') }}">Connexion</a>
                    <a href="{{ route('register') }}">Inscription</a>
                @endauth
            </nav>
        </div>
    </div>
</header>

<main class="main-content">
    <div class="container">
        @if (session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="flash flash-error">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<footer>
    <div class="container">
        ERAH Plateforme - Base restauree.
    </div>
</footer>
</body>
</html>
