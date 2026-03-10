<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title inertia>{{ config('app.name', 'ERAH Plateforme') }}</title>
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.getRegistrations().then((registrations) => {
                        registrations.forEach((registration) => registration.unregister());
                    });

                    if ('caches' in window) {
                        caches.keys().then((keys) => {
                            keys.forEach((key) => caches.delete(key));
                        });
                    }
                });
            }
        </script>

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/help-center.jsx'])
        @inertiaHead
    </head>
    <body class="min-h-screen bg-ui-bg text-white antialiased">
        @inertia
    </body>
</html>
