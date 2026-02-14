@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Points Metrics" />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-common.component-card title="Credits aujourd'hui">
            <p class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ number_format($dailyCredits) }}</p>
        </x-common.component-card>
        <x-common.component-card title="Debits aujourd'hui">
            <p class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ number_format($dailyDebits) }}</p>
        </x-common.component-card>
        <x-common.component-card title="Transactions 7j">
            <p class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ number_format($weeklyTransactions) }}</p>
        </x-common.component-card>
        <x-common.component-card title="Anomalies 7j">
            <p class="text-2xl font-semibold {{ $anomalyCount > 0 ? 'text-error-600 dark:text-error-400' : 'text-success-600 dark:text-success-400' }}">
                {{ number_format($anomalyCount) }}
            </p>
        </x-common.component-card>
    </div>
@endsection

