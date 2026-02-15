@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Missions finies" />

    <div class="space-y-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('missions.index') }}" class="premium-btn-ghost">Toutes les missions</a>
            <a href="{{ route('me.missions.progression') }}" class="premium-btn-ghost">En cours</a>
            <a href="{{ route('me.missions.history') }}" class="inline-flex items-center justify-center rounded-lg bg-success-500 px-4 py-2 text-sm font-semibold text-white">Missions finies</a>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Missions terminees</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Historique de tes missions validees automatiquement.</p>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($history as $entry)
                <div class="flex items-center justify-between gap-4 px-5 py-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $entry->mission?->title ?? 'Mission supprimee' }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Periode: {{ $entry->period_key }}
                            · Completee le {{ $entry->completed_at?->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-success-400">+{{ number_format((int) ($entry->mission?->points_reward ?? 0)) }} pts</p>
                        <p class="mt-1 text-xs {{ $entry->awarded_points ? 'text-success-300' : 'text-gray-400' }}">
                            {{ $entry->awarded_points ? 'Points attribues' : 'Points non attribues' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-sm text-gray-500 dark:text-gray-400">
                    Aucune mission completee pour le moment.
                </div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">
            {{ $history->links() }}
        </div>
        </div>
    </div>
@endsection
