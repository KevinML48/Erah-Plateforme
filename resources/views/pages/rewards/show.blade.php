@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Reward Details" />

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $reward->name }}</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $reward->slug }}</p>
                </div>
                <span class="rounded-full bg-brand-500/15 px-3 py-1 text-sm font-medium text-brand-300">
                    {{ number_format((int) $reward->points_cost) }} points
                </span>
            </div>

            @if ($reward->description)
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">{{ $reward->description }}</p>
            @endif

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800/40">
                    <span class="text-gray-500 dark:text-gray-400">Disponibilite</span>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $reward->is_active ? 'Active' : 'Inactive' }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800/40">
                    <span class="text-gray-500 dark:text-gray-400">Stock</span>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $reward->stock === null ? 'Illimite' : (int) $reward->stock }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800/40">
                    <span class="text-gray-500 dark:text-gray-400">Periode</span>
                    <p class="font-medium text-gray-800 dark:text-white/90">
                        {{ $reward->starts_at?->format('d/m/Y') ?? 'Maintenant' }} - {{ $reward->ends_at?->format('d/m/Y') ?? 'Sans fin' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

