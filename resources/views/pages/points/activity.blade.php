@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Activite Points" />

    <div class="mt-2 space-y-8 sm:mt-3 sm:space-y-7">
        <x-ecommerce.points-balance-card
            :current-user-points="$user->points_balance"
            :current-rank-name="$user->rank?->name ?? 'Pas de rank'"
            :progress-to-next-rank="$user->getProgressToNextRank()"
            :next-rank-name="$user->getNextRank()?->name"
            class="w-full mb-4 sm:mb-5"
        />

        <x-common.component-card title="Historique des transactions" desc="Tous les mouvements de points">
            <div class="space-y-4 px-1 py-1 md:hidden">
                @forelse ($logs as $log)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">{{ $log->type }}</p>
                                <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ $log->created_at?->format('d/m/Y H:i') }}</p>
                                <p class="mt-1 text-theme-xs text-gray-600 dark:text-gray-300">{{ $log->description ?? 'Aucune description' }}</p>
                                <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">
                                    {{ $log->reference_type ?? 'n/a' }}#{{ $log->reference_id ?? 'n/a' }}
                                </p>
                            </div>
                            <p class="shrink-0 text-theme-sm font-semibold {{ $log->amount >= 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                                {{ $log->amount >= 0 ? '+' : '' }}{{ number_format((int) $log->amount) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-theme-sm text-gray-500 dark:text-gray-400">Aucune transaction disponible.</p>
                @endforelse
            </div>

            <div class="hidden overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] md:block">
                <div class="table-responsive">
                    <table class="table-compact w-full min-w-[760px]">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-gray-900/80">
                                <th class="px-6 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Date</th>
                                <th class="px-6 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Type</th>
                                <th class="px-6 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Description</th>
                                <th class="px-6 py-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Reference</th>
                                <th class="px-6 py-3 text-right text-theme-xs font-medium text-gray-500 dark:text-gray-400">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-6 py-4 text-theme-sm text-gray-700 dark:text-gray-300">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-theme-sm text-gray-700 dark:text-gray-300">{{ $log->type }}</td>
                                    <td class="px-6 py-4 text-theme-sm text-gray-700 dark:text-gray-300">{{ $log->description ?? 'Aucune description' }}</td>
                                    <td class="px-6 py-4 text-theme-sm text-gray-500 dark:text-gray-400">
                                        {{ $log->reference_type ?? 'n/a' }}#{{ $log->reference_id ?? 'n/a' }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-theme-sm font-semibold {{ $log->amount >= 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                                        {{ $log->amount >= 0 ? '+' : '' }}{{ number_format((int) $log->amount) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400">
                                        Aucune transaction disponible.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-4">
                {{ $logs->withQueryString()->links() }}
            </div>
        </x-common.component-card>
    </div>
@endsection
