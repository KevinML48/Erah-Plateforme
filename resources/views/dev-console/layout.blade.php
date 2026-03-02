<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dev Console') - ERAH</title>
    @vite(['resources/css/app.css', 'resources/css/dev-console.css', 'resources/js/dev-console.js'])
</head>
<body class="dev-body">
<div class="dev-shell">
    <aside class="dev-sidebar">
        <h1 class="dev-logo">ERAH Dev</h1>
        <nav class="dev-nav">
            <a href="{{ route('dev.index') }}" class="{{ request()->routeIs('dev.index') ? 'is-active' : '' }}">Hub</a>
            <a href="{{ route('dev.routes') }}" class="{{ request()->routeIs('dev.routes') ? 'is-active' : '' }}">Routes</a>
            <a href="{{ route('dev.data') }}" class="{{ request()->routeIs('dev.data') ? 'is-active' : '' }}">Data</a>
            <a href="{{ route('dev.api') }}" class="{{ request()->routeIs('dev.api') ? 'is-active' : '' }}">API Explorer</a>
            <a href="{{ route('dev.logs') }}" class="{{ request()->routeIs('dev.logs') ? 'is-active' : '' }}">Logs</a>
        </nav>
    </aside>

    <main class="dev-main">
        <header class="dev-topbar">
            <div class="dev-topbar-left">
                <x-dev.badge variant="info">ENV {{ app()->environment() }}</x-dev.badge>
                <x-dev.badge>{{ config('database.default') }}</x-dev.badge>
                <x-dev.badge>{{ config('queue.default') }}</x-dev.badge>
                @auth
                    <x-dev.badge variant="success">User {{ auth()->user()->email }}</x-dev.badge>
                @else
                    <x-dev.badge variant="warn">Guest</x-dev.badge>
                @endauth
            </div>
            <div class="dev-topbar-right">
                <form method="POST" action="{{ route('dev.seed') }}">
                    @csrf
                    <x-dev.button variant="primary" type="submit">Seed Data</x-dev.button>
                </form>
                <form method="POST" action="{{ route('dev.db.reset') }}" class="dev-inline-form">
                    @csrf
                    <input name="confirm" class="dev-input dev-confirm-input" placeholder="RESET">
                    <x-dev.button variant="danger" type="submit">Reset DB</x-dev.button>
                </form>
            </div>
        </header>

        @if(session('success'))
            <div class="dev-alert dev-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="dev-alert dev-alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
