@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Admin Redemptions" />

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-success-500/20 bg-success-500/10 px-4 py-3 text-sm text-success-300">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-error-500/20 bg-error-500/10 px-4 py-3 text-sm text-error-300">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Demandes Rewards</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">#</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">User</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">Reward</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">Cost</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-right text-xs text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($redemptions as $redemption)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $redemption->id }}</td>
                                <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                    {{ $redemption->user?->name }}
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $redemption->user?->email }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $redemption->reward_name_snapshot }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ number_format((int) $redemption->points_cost_snapshot) }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $redemption->status?->value ?? $redemption->status }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.redemptions.approve', $redemption) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-success-500/40 px-3 py-2 text-xs text-success-400">Approve</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.redemptions.reject', $redemption) }}" class="flex gap-2">
                                            @csrf
                                            <input name="note" placeholder="Note" class="h-9 rounded-lg border border-gray-300 bg-white px-3 text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                            <button type="submit" class="rounded-lg border border-warning-500/40 px-3 py-2 text-xs text-warning-400">Reject</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.redemptions.ship', $redemption) }}" class="flex gap-2">
                                            @csrf
                                            <input name="tracking_code" placeholder="Tracking" class="h-9 rounded-lg border border-gray-300 bg-white px-3 text-xs text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" />
                                            <button type="submit" class="rounded-lg border border-brand-500/40 px-3 py-2 text-xs text-brand-400">Ship</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5">
                {{ $redemptions->links() }}
            </div>
        </div>
    </div>
@endsection

