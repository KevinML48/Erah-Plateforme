<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', 'ERAH Plateforme')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Plateforme ERAH">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .app-toast-stack { position: fixed; top: 16px; right: 16px; width: min(420px, calc(100vw - 24px)); display: grid; gap: 10px; z-index: 2000; pointer-events: none; }
        .app-toast { margin: 0; padding: 14px 18px; border-radius: 10px; border: 1px solid rgba(0, 0, 0, .15); box-shadow: 0 10px 28px rgba(0, 0, 0, .2); backdrop-filter: blur(4px); pointer-events: auto; transition: opacity .2s ease, transform .2s ease; }
        .app-toast-success { background: rgba(24, 110, 59, .14); color: #0b4427; }
        .app-toast-error { background: rgba(173, 41, 41, .15); color: #631616; }
        .app-toast.toast-leaving { opacity: 0; transform: translateY(-8px); }
        .app-toast-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .app-toast-close { background: transparent; border: 0; color: inherit; font-size: 18px; line-height: 1; cursor: pointer; opacity: .8; }
        .app-toast-close:hover { opacity: 1; }
        .app-toast ul { margin: 8px 0 0; padding-left: 18px; }
        @media (max-width: 991.98px) { .app-toast-stack { top: 10px; right: 10px; width: calc(100vw - 20px); } }
    </style>
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
                    <a href="{{ route('profile.show') }}">Mon profil</a>
                    <a href="{{ route('app.leaderboards.index') }}">Vue publique /app</a>

                    <form method="POST" action="{{ route('auth.logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="btn btn-danger">Deconnexion</button>
                    </form>
                @else
                    <a href="{{ route('marketing.index') }}">Accueil site</a>
                    <a href="{{ route('app.leaderboards.index') }}">Plateforme /app</a>
                    <a href="{{ route('login') }}">Se connecter</a>
                    <a href="{{ route('register') }}">Inscription</a>
                @endauth
            </nav>
        </div>
    </div>
</header>

<main class="main-content">
    <div class="container">
        @if (session('success') || session('error') || $errors->any())
            <div class="app-toast-stack" id="app-toast-stack" aria-live="polite">
                @if (session('success'))
                    <div class="app-toast app-toast-success" role="status">
                        <div class="app-toast-head">
                            <strong>Succes</strong>
                            <button type="button" class="app-toast-close" data-app-toast-close aria-label="Fermer">&times;</button>
                        </div>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="app-toast app-toast-error" role="alert">
                        <div class="app-toast-head">
                            <strong>Erreur</strong>
                            <button type="button" class="app-toast-close" data-app-toast-close aria-label="Fermer">&times;</button>
                        </div>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="app-toast app-toast-error" role="alert">
                        <div class="app-toast-head">
                            <strong>Verification</strong>
                            <button type="button" class="app-toast-close" data-app-toast-close aria-label="Fermer">&times;</button>
                        </div>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var stack = document.getElementById('app-toast-stack');
        if (!stack) {
            return;
        }

        var removeToast = function (toast) {
            if (!toast) {
                return;
            }

            toast.classList.add('toast-leaving');
            window.setTimeout(function () {
                toast.remove();
                if (!stack.querySelector('.app-toast')) {
                    stack.remove();
                }
            }, 180);
        };

        stack.querySelectorAll('[data-app-toast-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                removeToast(button.closest('.app-toast'));
            });
        });

        window.setTimeout(function () {
            stack.querySelectorAll('.app-toast').forEach(function (toast) {
                removeToast(toast);
            });
        }, 5200);
    });
</script>
</body>
</html>
