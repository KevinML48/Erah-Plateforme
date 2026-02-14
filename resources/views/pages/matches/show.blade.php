@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Parier sur match" />

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-success-500/20 bg-success-500/10 px-4 py-3 text-sm text-success-300">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-error-500/20 bg-error-500/10 px-4 py-3 text-sm text-error-300">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $match->title }}</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $match->game }} · {{ $match->starts_at?->format('d/m/Y H:i') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full bg-brand-500/15 px-3 py-1 text-xs font-medium text-brand-300">
                        Status: {{ $match->status?->value }}
                    </span>
                    <span class="inline-flex rounded-full bg-success-500/15 px-3 py-1 text-xs font-medium text-success-300">
                        Bonus: {{ (int) $match->points_reward }}%
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="xl:col-span-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pari</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choisis ton resultat et ta mise.</p>

                    @if ($myTicket)
                        <div class="mt-5 rounded-xl border border-brand-500/20 bg-brand-500/10 p-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">Ticket deja enregistre</p>
                            <p class="mt-2 text-sm text-white/90">
                                Mise: <span class="font-semibold">{{ number_format((int) $myTicket->stake_points) }} pts</span>
                                · Cote totale: <span class="font-semibold">{{ number_format((float) $myTicket->total_odds_decimal, 3) }}</span>
                                · Gain potentiel: <span class="font-semibold text-success-400">+{{ number_format((int) $myTicket->potential_payout_points) }} pts</span>
                            </p>

                            <div class="mt-3 space-y-2">
                                @foreach ($myTicket->selections as $selection)
                                    <div class="rounded-lg border border-gray-700 bg-gray-800/40 px-3 py-2 text-sm text-gray-300">
                                        {{ $selection->market?->name }}: <span class="font-semibold text-white/90">{{ $selection->option?->label }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif (!$match->isOpen() || $match->isBetLockPassed())
                        <div class="mt-5 rounded-xl border border-warning-500/20 bg-warning-500/10 p-4 text-sm text-warning-300">
                            Les tickets sont fermes pour ce match.
                        </div>
                    @else
                        <form method="POST" action="{{ route('matches.tickets.store', $match) }}" class="mt-5 space-y-4">
                            @csrf

                            @forelse ($match->markets as $market)
                                @if ($market->status?->value === 'OPEN')
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $market->name }}</p>
                                        <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">{{ $market->code }}</p>

                                        <select
                                            name="selections[]"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                                            @foreach ($market->options as $option)
                                                <option value="{{ $option->id }}">
                                                    {{ $option->label }} (cote {{ number_format((float) $option->odds_decimal, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            @empty
                                <div class="rounded-xl border border-warning-500/20 bg-warning-500/10 p-4 text-sm text-warning-300">
                                    Aucun market ouvert pour ce match.
                                </div>
                            @endforelse

                            <div>
                                <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Mise (points)</label>
                                <input
                                    id="stake_points"
                                    type="number"
                                    min="10"
                                    max="{{ max((int) config('betting.stake_min', 10), $userPointsBalance) }}"
                                    name="stake_points"
                                    value="{{ old('stake_points', 100) }}"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white/90"
                                    required
                                />
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-300">
                                    <span>Solde actuel</span>
                                    <span class="font-semibold">{{ number_format((int) $userPointsBalance) }} pts</span>
                                </div>
                                <div class="mt-2 flex items-center justify-between text-sm text-gray-600 dark:text-gray-300">
                                    <span>Gain potentiel (estimation)</span>
                                    <span id="potential_preview" class="font-semibold text-success-500 dark:text-success-400">+0 pts</span>
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex h-11 items-center justify-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600">
                                Valider mon ticket
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div>
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">Regles</h4>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>1 pronostic maximum par match.</li>
                        <li>La mise est debitee immediatement.</li>
                        <li>Si ticket gagnant: credit selon cotes snapshot au settlement.</li>
                        <li>Si mauvais pronostic: perte de la mise.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stakeInput = document.getElementById('stake_points');
            const potentialPreview = document.getElementById('potential_preview');
            const optionSelects = Array.from(document.querySelectorAll('select[name="selections[]"]'));

            if (!stakeInput || !potentialPreview) return;

            const getTotalOdds = () => {
                if (optionSelects.length === 0) return 1;

                let total = 1;
                optionSelects.forEach((select) => {
                    const label = select.options[select.selectedIndex]?.textContent || '';
                    const match = label.match(/cote\\s([0-9]+(?:\\.[0-9]+)?)/i);
                    const odds = match ? parseFloat(match[1]) : 1;
                    total *= Number.isFinite(odds) ? odds : 1;
                });

                return total;
            };

            const refreshPreview = () => {
                const stake = Math.max(0, parseInt(stakeInput.value || '0', 10));
                const potential = Math.floor(stake * getTotalOdds());
                potentialPreview.textContent = `+${potential.toLocaleString()} pts`;
            };

            stakeInput.addEventListener('input', refreshPreview);
            optionSelects.forEach((select) => select.addEventListener('change', refreshPreview));
            refreshPreview();
        });
    </script>
@endsection
