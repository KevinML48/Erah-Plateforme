<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ERAH') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="container main-content">
    <section class="section">
        <h1>UI React desactivee</h1>
        <p>La plateforme utilise maintenant le front Blade test console.</p>
        <p><a href="{{ route('dashboard') }}">Ouvrir la console</a></p>
    </section>
</main>
</body>
</html>
