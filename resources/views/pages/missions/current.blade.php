@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Vos missions" />

    <div class="space-y-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('missions.index') }}" class="premium-btn-ghost">Toutes les missions</a>
            <a href="{{ route('me.missions.progression') }}" class="inline-flex items-center justify-center rounded-lg bg-warning-500 px-4 py-2 text-sm font-semibold text-white">En cours</a>
            <a href="{{ route('me.missions.history') }}" class="premium-btn-ghost">Missions finies</a>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($missions as $mission)
                @php($progress = $mission->user_progress ?? [])
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $mission->title }}</h3>
                        <span class="inline-flex rounded-full border border-warning-500/30 bg-warning-500/15 px-3 py-1 text-xs font-medium text-warning-300">En cours</span>
                    </div>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $mission->description ?: 'Mission communautaire ERAH.' }}</p>

                    <div class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                        <p>Points: <span class="font-semibold text-success-400">+{{ number_format((int) $mission->points_reward) }}</span></p>
                        <p>Progression: <span class="font-semibold">{{ (int) ($progress['progress_percent'] ?? 0) }}%</span></p>
                    </div>

                    <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                        <div class="h-full rounded-full bg-brand-500" style="width: {{ min(100, max(0, (int) ($progress['progress_percent'] ?? 0))) }}%"></div>
                    </div>

                    <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ (int) ($progress['completed_steps'] ?? 0) }} / {{ (int) ($progress['target_steps'] ?? 0) }} objectifs</span>
                        <a href="{{ route('missions.show', $mission->slug) }}" class="text-brand-400 hover:text-brand-300">Details</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400">
                    Aucune mission en cours pour cette periode.
                </div>
            @endforelse
        </div>

        <div>
            {{ $missions->links() }}
        </div>
    </div>
@endsection
