@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Mon ticket" />

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-success-500/20 bg-success-500/10 px-4 py-3 text-sm text-success-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($isValidated)
            <div class="rounded-xl border border-success-500/30 bg-success-500/10 px-4 py-3">
                <p class="text-sm font-semibold text-success-300">Ticket valide</p>
                <p class="mt-1 text-sm text-success-400">
                    Ton ticket #{{ $ticket->id }} est gagnant. Paiement credite: {{ number_format((int) $ticket->payout_points) }} points.
                </p>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-800 bg-[#18223a] p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">Ticket</p>
                    <h3 class="mt-1 text-xl font-semibold text-white">#{{ $ticket->id }}</h3>
                    <p class="mt-2 text-sm text-gray-300">{{ $ticket->match?->title }}</p>
                </div>

                <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide
                    @if (($ticket->status?->value ?? $ticket->status) === 'WON') bg-success-500/15 text-success-400
                    @elseif (($ticket->status?->value ?? $ticket->status) === 'LOST') bg-error-500/15 text-error-400
                    @elseif (($ticket->status?->value ?? $ticket->status) === 'VOID') bg-warning-500/15 text-warning-400
                    @else bg-brand-500/15 text-brand-400
                    @endif">
                    {{ $ticket->status?->value ?? $ticket->status }}
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-gray-700 bg-[#1f2b46] px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Mise</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ number_format((int) $ticket->stake_points) }} pts</p>
                </div>

                <div class="rounded-xl border border-gray-700 bg-[#1f2b46] px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Cote totale</p>
                    <p class="mt-1 text-lg font-semibold text-white">{{ number_format((float) $ticket->total_odds_decimal, 3) }}</p>
                </div>

                <div class="rounded-xl border border-gray-700 bg-[#1f2b46] px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Gain potentiel</p>
                    <p class="mt-1 text-lg font-semibold text-brand-300">{{ number_format((int) $ticket->potential_payout_points) }} pts</p>
                </div>

                <div class="rounded-xl border border-gray-700 bg-[#1f2b46] px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-400">Payout</p>
                    <p class="mt-1 text-lg font-semibold text-success-400">{{ number_format((int) $ticket->payout_points) }} pts</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-800 bg-[#18223a]">
            <div class="border-b border-gray-800 px-5 py-4">
                <h3 class="text-lg font-semibold text-white">Selections</h3>
            </div>

            <div class="space-y-3 p-5">
                @foreach ($ticket->selections as $selection)
                    <div class="rounded-xl border border-gray-700 bg-[#1f2b46] p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $selection->market?->name }}</p>
                                <p class="mt-1 text-sm text-gray-300">
                                    Option: {{ $selection->option?->label }} · Cote: {{ number_format((float) $selection->odds_decimal_snapshot, 2) }}
                                </p>
                            </div>

                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-wide
                                @if (($selection->status?->value ?? $selection->status) === 'WON') bg-success-500/15 text-success-400
                                @elseif (($selection->status?->value ?? $selection->status) === 'LOST') bg-error-500/15 text-error-400
                                @elseif (($selection->status?->value ?? $selection->status) === 'VOID') bg-warning-500/15 text-warning-400
                                @else bg-brand-500/15 text-brand-300
                                @endif">
                                {{ $selection->status?->value ?? $selection->status }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
