@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Missions" />

    <div class="space-y-6">
        @php
            $missionCollection = collect($missions->items());
            $inProgressCount = $missionCollection->filter(fn ($m) => (bool) data_get($m, 'user_progress.is_started') && !(bool) data_get($m, 'user_progress.is_completed'))->count();
            $completedCount = $missionCollection->filter(fn ($m) => (bool) data_get($m, 'user_progress.is_completed'))->count();
            $totalPotential = $missionCollection->sum(fn ($m) => (int) $m->points_reward);
        @endphp

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-warning-500/25 bg-gray-900/60 p-4">
                <p class="text-xs text-gray-300">Missions en cours</p>
                <p class="mt-1 text-xl font-semibold text-warning-400">{{ $inProgressCount }}</p>
            </div>
            <div class="rounded-xl border border-success-500/25 bg-gray-900/60 p-4">
                <p class="text-xs text-gray-300">Missions completees (periode)</p>
                <p class="mt-1 text-xl font-semibold text-success-400">{{ $completedCount }}</p>
            </div>
            <div class="rounded-xl border border-brand-500/25 bg-gray-900/60 p-4">
                <p class="text-xs text-gray-300">Potentiel total visible</p>
                <p class="mt-1 text-xl font-semibold text-brand-300">+{{ number_format($totalPotential) }} pts</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('missions.index') }}" class="premium-btn">Toutes les missions</a>
            <a href="{{ route('me.missions.progression') }}" class="premium-btn-ghost">En cours</a>
            <a href="{{ route('me.missions.history') }}" class="premium-btn-ghost">Missions finies</a>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($missions as $mission)
                @php($progress = $mission->user_progress ?? [])
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] card-3d animate-fade-up">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $mission->title }}</h3>
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium
                            {{ ($progress['is_completed'] ?? false) ? 'border border-success-500/30 bg-success-500/15 text-success-300' : (($progress['is_started'] ?? false) ? 'border border-warning-500/30 bg-warning-500/15 text-warning-300' : 'border border-brand-500/30 bg-brand-500/15 text-brand-300') }}">
                            {{ $progress['status_label'] ?? 'Non demarree' }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $mission->description ?: 'Mission communautaire ERAH.' }}</p>

                    <div class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                        <p>Points: <span class="font-semibold text-success-400">+{{ number_format((int) $mission->points_reward) }}</span></p>
                        <p>Recurrence: <span class="font-semibold">{{ $mission->recurrence->value }}</span></p>
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
                    Aucune mission active.
                </div>
            @endforelse
        </div>

        <div>
            {{ $missions->links() }}
        </div>
    </div>
@endsection
