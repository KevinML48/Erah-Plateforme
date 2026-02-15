@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="$mission->title" />

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $mission->description ?: 'Mission communautaire ERAH.' }}</p>

            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <p class="text-xs text-gray-500">Points</p>
                    <p class="text-xl font-semibold text-success-400">+{{ number_format((int) $mission->points_reward) }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <p class="text-xs text-gray-500">Recurrence</p>
                    <p class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $mission->recurrence->value }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <p class="text-xs text-gray-500">Etat</p>
                    <p class="text-xl font-semibold {{ ($progress['is_completed'] ?? false) ? 'text-success-400' : (($progress['is_started'] ?? false) ? 'text-warning-300' : 'text-brand-400') }}">
                        {{ $progress['status_label'] ?? 'Non demarree' }}
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <div class="mb-2 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <span>Progression</span>
                    <span>{{ (int) ($progress['progress_percent'] ?? 0) }}%</span>
                </div>
                <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                    <div class="h-full rounded-full bg-brand-500" style="width: {{ min(100, max(0, (int) ($progress['progress_percent'] ?? 0))) }}%"></div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Etapes</h3>
            <div class="mt-4 space-y-3">
                @foreach ($mission->steps as $step)
                    @php($done = in_array($step->id, $mission->user_progress['completed_step_ids'] ?? [], true))
                    <div class="flex items-center justify-between rounded-lg border px-4 py-3 {{ $done ? 'border-success-500/30 bg-success-500/10' : 'border-gray-200 dark:border-gray-700' }}">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $step->label }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $step->step_key }}{{ $step->step_value ? ':' . $step->step_value : '' }}</p>
                        </div>
                        <span class="text-xs font-semibold {{ $done ? 'text-success-300' : 'text-gray-400' }}">{{ $done ? 'OK' : 'En attente' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
