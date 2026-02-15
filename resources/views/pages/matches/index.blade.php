@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Matchs" />

    <div class="space-y-6">
        <div class="premium-card p-5 md:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Espace Matchs ERAH</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Suivi des rencontres et acces rapide aux pronostics actifs.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full bg-brand-500/15 px-3 py-1 text-xs font-medium text-brand-300">
                        {{ $upcoming->count() }} a venir
                    </span>
                    <span class="rounded-full bg-gray-500/15 px-3 py-1 text-xs font-medium text-gray-300">
                        {{ $past->count() }} passes
                    </span>
                </div>
            </div>
        </div>

        <div class="premium-card p-5 md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Matchs a venir</h3>
            <div class="mt-4 space-y-3">
                @forelse ($upcoming as $match)
                    <a href="{{ route('matches.show', $match) }}" class="block rounded-xl border border-brand-500/25 bg-gray-900/60 p-4 transition hover:border-brand-400">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white/90">{{ $match->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $match->game }} · {{ $match->starts_at?->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="text-sm text-brand-400">{{ $match->status?->value }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun match a venir.</p>
                @endforelse
            </div>
        </div>

        <div class="premium-card p-5 md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Matchs passes</h3>
            <div class="mt-4 space-y-3">
                @forelse ($past as $match)
                    <a href="{{ route('matches.show', $match) }}" class="block rounded-xl border border-brand-500/25 bg-gray-900/60 p-4 transition hover:border-brand-400">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-white/90">{{ $match->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $match->game }} · {{ $match->starts_at?->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="text-sm text-gray-400">{{ $match->result?->value ?? 'N/A' }}</span>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun match termine.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
