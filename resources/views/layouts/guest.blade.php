<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-slate-100">
            <div class="mx-auto flex min-h-screen w-full max-w-6xl items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
                <div class="w-full max-w-md">
                    <div class="mb-6 flex justify-center sm:mb-8">
                        <a href="/" class="inline-flex rounded-full bg-white/90 p-4 shadow-lg shadow-slate-900/5 ring-1 ring-slate-200">
                            <x-application-logo class="h-16 w-16 fill-current text-gray-500 sm:h-20 sm:w-20" />
                        </a>
                    </div>

                    <div class="w-full rounded-3xl border border-slate-200 bg-white/95 px-5 py-6 shadow-xl shadow-slate-900/5 backdrop-blur sm:px-8 sm:py-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
