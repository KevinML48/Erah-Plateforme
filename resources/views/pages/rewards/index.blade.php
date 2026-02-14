@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Rewards" />

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Catalogue Rewards</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Depense tes points pour debloquer des recompenses.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($rewards as $reward)
                <a href="{{ route('rewards.show', $reward->slug) }}"
                    class="block rounded-xl border border-gray-200 bg-gray-50/60 p-4 transition hover:border-brand-400 dark:border-gray-700 dark:bg-gray-800/40">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-gray-800 dark:text-white/90">{{ $reward->name }}</p>
                        <span class="rounded-full bg-brand-500/15 px-2.5 py-1 text-xs font-medium text-brand-300">
                            {{ number_format((int) $reward->points_cost) }} pts
                        </span>
                    </div>

                    @if ($reward->description)
                        <p class="mt-2 line-clamp-2 text-sm text-gray-500 dark:text-gray-400">{{ $reward->description }}</p>
                    @endif

                    <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        @if ($reward->stock === null)
                            Stock: illimite
                        @else
                            Stock: {{ (int) $reward->stock }}
                        @endif
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                    Aucune reward disponible actuellement.
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $rewards->links() }}
        </div>
    </div>
@endsection

