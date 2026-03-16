<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @php
        $metaTitle = trim($__env->yieldContent('title', 'ERAH Plateforme'));
        $metaDescription = trim($__env->yieldContent('meta_description', 'ERAH Plateforme, espace membre, progression, missions et modules competitifs.'));
        $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
        $socialImage = trim($__env->yieldContent('meta_image', asset('template/assets/img/logo.png')));

        if (! \Illuminate\Support\Str::startsWith($socialImage, ['http://', 'https://'])) {
            $socialImage = asset(ltrim($socialImage, '/'));
        }
    @endphp
    <title>@yield('title', 'ERAH Plateforme')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="@yield('meta_robots', 'noindex,nofollow,noarchive')">
    <meta name="theme-color" content="#d80707">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="ERAH Plateforme">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $socialImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $socialImage }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/template/assets/img/logo.png" type="image/png" sizes="512x512">
    <link rel="shortcut icon" href="/template/assets/img/logo.png" type="image/png">
    <link rel="apple-touch-icon" href="/template/assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Big+Shoulders+Display:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/template/assets/css/helper.css">
    <link rel="stylesheet" href="/template/assets/css/theme.css">
    <link rel="stylesheet" href="/template/assets/css/theme-light.css">
    <link rel="stylesheet" href="/template/assets/css/platform-responsive.css">
    <link rel="preload" href="/template/assets/vendor/fontawesome/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="/template/assets/vendor/fontawesome/css/all.min.css">
    </noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-google-analytics />
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
<body class="platform-app">
<div class="platform-header">
    @include('marketing.partials.header')
</div>

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
