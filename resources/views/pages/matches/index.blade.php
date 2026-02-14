@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Matchs" />

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Matchs a venir</h3>
            <div class="mt-4 space-y-3">
                @forelse ($upcoming as $match)
                    <a href="{{ route('matches.show', $match) }}" class="block rounded-xl border border-gray-200 bg-gray-50/60 p-4 transition hover:border-brand-400 dark:border-gray-700 dark:bg-gray-800/50">
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

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Matchs passes</h3>
            <div class="mt-4 space-y-3">
                @forelse ($past as $match)
                    <a href="{{ route('matches.show', $match) }}" class="block rounded-xl border border-gray-200 bg-gray-50/60 p-4 transition hover:border-brand-400 dark:border-gray-700 dark:bg-gray-800/50">
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

