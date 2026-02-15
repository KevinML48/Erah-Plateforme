@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Mes claims missions" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-xs text-gray-500">Mission</th>
                        <th class="px-5 py-3 text-left text-xs text-gray-500">Periode</th>
                        <th class="px-5 py-3 text-left text-xs text-gray-500">Points</th>
                        <th class="px-5 py-3 text-left text-xs text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($claims as $claim)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $claim->mission?->title ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $claim->period_key }}</td>
                            <td class="px-5 py-4 text-sm text-success-400">+{{ number_format((int) ($claim->mission?->points_reward ?? 0)) }}</td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $claim->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-5">
            {{ $claims->links() }}
        </div>
    </div>
@endsection
