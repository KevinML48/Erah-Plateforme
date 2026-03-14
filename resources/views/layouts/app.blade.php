<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', 'ERAH Plateforme')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ERAH Plateforme, espace membre, progression, missions et modules competitifs.">
    <meta name="theme-color" content="#d80707">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/template/assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Big+Shoulders+Display:wght@100..900&display=swap" rel="stylesheet">
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
@php
    $isAuthenticated = auth()->check();
    $isAdmin = $isAuthenticated && auth()->user()?->role === 'admin';
    $primaryLinks = $isAuthenticated
        ? [
            ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
            ['label' => 'Matchs', 'href' => route('matches.index'), 'active' => request()->routeIs('matches.*')],
            ['label' => 'Clips', 'href' => route('clips.index'), 'active' => request()->routeIs('clips.*')],
            ['label' => 'Paris', 'href' => route('bets.index'), 'active' => request()->routeIs('bets.*')],
            ['label' => 'Classements', 'href' => route('leaderboards.index'), 'active' => request()->routeIs('leaderboards.*') || request()->routeIs('ranking.*')],
            ['label' => 'Missions', 'href' => route('missions.index'), 'active' => request()->routeIs('missions.*')],
            ['label' => 'Cadeaux', 'href' => route('gifts.index'), 'active' => request()->routeIs('gifts.*')],
            ['label' => 'Boutique', 'href' => route('marketing.boutique'), 'active' => request()->routeIs('marketing.boutique')],
            ['label' => 'Duels', 'href' => route('duels.index'), 'active' => request()->routeIs('duels.*')],
        ]
        : [
            ['label' => 'Accueil site', 'href' => route('marketing.index'), 'active' => request()->routeIs('marketing.*')],
            ['label' => 'Explorer la plateforme', 'href' => route('app.leaderboards.index'), 'active' => request()->routeIs('app.*')],
            ['label' => 'Boutique', 'href' => route('marketing.boutique'), 'active' => request()->routeIs('marketing.boutique')],
        ];
    $adminLinks = $isAdmin
        ? [
            ['label' => 'Pilotage admin', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
            ['label' => 'Matchs admin', 'href' => route('admin.matches.index'), 'active' => request()->routeIs('admin.matches.*')],
            ['label' => 'Clips admin', 'href' => route('admin.clips.index'), 'active' => request()->routeIs('admin.clips.*')],
            ['label' => 'Cadeaux admin', 'href' => route('admin.gifts.index'), 'active' => request()->routeIs('admin.gifts.*') || request()->routeIs('admin.redemptions.*')],
            ['label' => 'Missions admin', 'href' => route('admin.missions.index'), 'active' => request()->routeIs('admin.missions.*')],
            ['label' => 'Galerie admin', 'href' => route('admin.gallery-photos.index'), 'active' => request()->routeIs('admin.gallery-photos.*')],
            ['label' => 'Avis admin', 'href' => route('admin.reviews.index'), 'active' => request()->routeIs('admin.reviews.*')],
        ]
        : [];
    $sessionLinks = $isAuthenticated
        ? [
            ['label' => 'Mon profil', 'href' => route('profile.show'), 'active' => request()->routeIs('profile.*')],
            ['label' => 'Points', 'href' => route('wallet.index'), 'active' => request()->routeIs('wallet.*') || request()->routeIs('wallets.*')],
            ['label' => 'Notifications', 'href' => route('notifications.index'), 'active' => request()->routeIs('notifications.*')],
            ['label' => 'Centre d aide', 'href' => route('console.help'), 'active' => request()->routeIs('console.help') || request()->routeIs('assistant.*')],
        ]
        : [
            ['label' => 'Se connecter', 'href' => route('login'), 'active' => request()->routeIs('login')],
            ['label' => 'Inscription', 'href' => route('register'), 'active' => request()->routeIs('register')],
        ];
@endphp

<body class="platform-app">
<header class="app-header" data-mobile-nav-root>
    <div class="container">
        <div class="header-row">
            <div class="header-main">
                <a class="brand" href="{{ $isAuthenticated ? route('dashboard') : route('marketing.index') }}">
                    ERAH Plateforme
                </a>

                <nav class="nav-links desktop-nav" aria-label="Navigation principale">
                    @foreach ($primaryLinks as $link)
                        <a href="{{ $link['href'] }}" class="{{ $link['active'] ? 'is-active' : '' }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>
            </div>

            <div class="header-actions">
                <nav class="nav-links desktop-session-nav" aria-label="Actions de session">
                    @if ($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'is-active nav-link-admin' : 'nav-link-admin' }}">Admin</a>
                    @endif

                    @foreach ($sessionLinks as $link)
                        <a href="{{ $link['href'] }}" class="{{ $link['active'] ? 'is-active' : '' }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>

                @auth
                    <form method="POST" action="{{ route('auth.logout') }}" class="logout-form desktop-logout">
                        @csrf
                        <button type="submit" class="tt-btn tt-btn-primary">
                            <span data-hover="Deconnexion">Deconnexion</span>
                        </button>
                    </form>
                @endauth

                <button
                    type="button"
                    class="mobile-nav-toggle"
                    data-mobile-nav-toggle
                    aria-expanded="false"
                    aria-controls="app-mobile-nav"
                    aria-label="Ouvrir le menu de navigation"
                >
                    <span class="mobile-nav-toggle-box" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    <span>Menu</span>
                </button>
            </div>
        </div>
    </div>

    <div class="mobile-nav-backdrop" data-mobile-nav-backdrop hidden></div>

    <div id="app-mobile-nav" class="mobile-nav-panel" data-mobile-nav-panel hidden>
        <div class="mobile-nav-shell">
            <div class="mobile-nav-head">
                <div>
                    <p class="mobile-nav-kicker">Navigation</p>
                    <strong>Acces rapide a votre espace</strong>
                </div>
                <button type="button" class="mobile-nav-close" data-mobile-nav-close aria-label="Fermer le menu">
                    Fermer
                </button>
            </div>

            <section class="mobile-nav-section">
                <p class="mobile-nav-section-label">Navigation principale</p>
                <div class="mobile-nav-list">
                    @foreach ($primaryLinks as $link)
                        <a href="{{ $link['href'] }}" class="mobile-nav-link {{ $link['active'] ? 'is-active' : '' }}" data-mobile-nav-link>
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </section>

            @if (count($adminLinks))
                <section class="mobile-nav-section">
                    <p class="mobile-nav-section-label">Admin</p>
                    <div class="mobile-nav-list">
                        @foreach ($adminLinks as $link)
                            <a href="{{ $link['href'] }}" class="mobile-nav-link {{ $link['active'] ? 'is-active' : '' }}" data-mobile-nav-link>
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="mobile-nav-section">
                <p class="mobile-nav-section-label">{{ $isAuthenticated ? 'Session' : 'Compte' }}</p>
                <div class="mobile-nav-list">
                    @foreach ($sessionLinks as $link)
                        <a href="{{ $link['href'] }}" class="mobile-nav-link {{ $link['active'] ? 'is-active' : '' }}" data-mobile-nav-link>
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>

                @auth
                    <form method="POST" action="{{ route('auth.logout') }}" class="mobile-nav-logout">
                        @csrf
                        <button type="submit" class="tt-btn tt-btn-primary tt-btn-full">
                            <span data-hover="Se deconnecter">Se deconnecter</span>
                        </button>
                    </form>
                @endauth
            </section>
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
        ERAH Plateforme - espace membre, progression et modules competitifs.
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
@include('partials.mission-live-toasts')
</body>
</html>
