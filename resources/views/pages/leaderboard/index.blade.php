@extends('layouts.app')

@php
    $titleMap = [
        'all_time' => 'All-time',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
    ];

    $scoreLabel = $type === 'all_time' ? 'Points total' : 'Points gagnes';
    $entriesCollection = collect($entries);
    $topThree = $entriesCollection->take(3)->values();
    $currentUserEntry = $entriesCollection->firstWhere('user_id', auth()->id());

    $periodDescription = match ($type) {
        'weekly' => 'Classement des 7 derniers jours',
        'monthly' => 'Classement des 30 derniers jours',
        default => 'Classement global de tous les temps',
    };
@endphp

@section('content')
    <x-common.page-breadcrumb pageTitle="LeaderBoard {{ $titleMap[$type] ?? '' }}" />

    <div class="space-y-6">
        <x-common.component-card title="Classement {{ $titleMap[$type] ?? '' }}" :desc="$periodDescription">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full bg-brand-500/[0.12] px-3 py-1 text-theme-xs font-medium text-brand-500 dark:text-brand-400">
                        Top {{ $limit }}
                    </span>
                    @if (!is_null($currentUserPosition))
                        <span class="inline-flex rounded-full bg-success-500/[0.12] px-3 py-1 text-theme-xs font-medium text-success-600 dark:text-success-400">
                            Position #{{ $currentUserPosition }}
                        </span>
                    @endif
                    @if ($currentUserEntry)
                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-theme-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            Ton score: {{ number_format((int) $currentUserEntry['score']) }}
                        </span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('leaderboard.all-time', ['q' => $search ?? null, 'limit' => $limit]) }}"
                        class="rounded-lg px-4 py-2 text-theme-sm font-medium transition-colors {{ $type === 'all_time' ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]' }}">
                        All-time
                    </a>
                    <a href="{{ route('leaderboard.weekly', ['q' => $search ?? null, 'limit' => $limit]) }}"
                        class="rounded-lg px-4 py-2 text-theme-sm font-medium transition-colors {{ $type === 'weekly' ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]' }}">
                        Weekly
                    </a>
                    <a href="{{ route('leaderboard.monthly', ['q' => $search ?? null, 'limit' => $limit]) }}"
                        class="rounded-lg px-4 py-2 text-theme-sm font-medium transition-colors {{ $type === 'monthly' ? 'bg-brand-500 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]' }}">
                        Monthly
                    </a>
                </div>
            </div>
            <form method="GET" class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
                <input type="hidden" name="limit" value="{{ $limit }}">
                <input
                    type="text"
                    name="q"
                    value="{{ $search ?? '' }}"
                    placeholder="Rechercher un joueur..."
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90"
                />
                <button type="submit" class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white sm:w-auto">
                    Rechercher
                </button>
            </form>
        </x-common.component-card>

        @if ($topThree->isNotEmpty())
            <x-common.component-card title="Podium" desc="Top 3 joueurs">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    @foreach ($topThree as $podiumEntry)
                        @php
                            $podiumStyle = match ($podiumEntry['position']) {
                                1 => 'border-warning-500/40 bg-warning-500/[0.08]',
                                2 => 'border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03]',
                                default => 'border-brand-500/40 bg-brand-500/[0.08]',
                            };
                        @endphp
                        <div class="rounded-xl border p-4 {{ $podiumStyle }}">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-theme-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Top {{ $podiumEntry['position'] }}</p>
                                <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format((int) $podiumEntry['score']) }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 overflow-hidden rounded-full border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                    @if (!empty($podiumEntry['avatar_url']))
                                        <img src="{{ $podiumEntry['avatar_url'] }}" alt="{{ $podiumEntry['name'] }}" class="h-full w-full object-cover" loading="lazy" />
                                    @else
                                        <span class="flex h-full w-full items-center justify-center text-theme-xs font-semibold text-gray-700 dark:text-gray-200">
                                            {{ strtoupper(substr($podiumEntry['name'], 0, 2)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-theme-sm font-semibold text-gray-800 dark:text-white/90">{{ $podiumEntry['name'] }}</p>
                                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ $podiumEntry['rank_name'] ?? 'Pas de rank' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-common.component-card>
        @endif

        <x-common.component-card title="Classement complet" desc="Tous les joueurs du leaderboard">
            <div class="space-y-4 px-1 py-1 md:hidden">
                @forelse ($entriesCollection as $entry)
                    @php
                        $isCurrentUser = (int) $entry['user_id'] === (int) auth()->id();
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] {{ $isCurrentUser ? 'ring-1 ring-success-500/40' : '' }}">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-full border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                    @if (!empty($entry['avatar_url']))
                                        <img
                                            src="{{ $entry['avatar_url'] }}"
                                            alt="{{ $entry['name'] }}"
                                            class="h-10 w-10 object-cover"
                                            loading="lazy"
                                        />
                                    @else
                                        <span class="flex h-full w-full items-center justify-center text-theme-xs font-semibold text-gray-700 dark:text-gray-200">
                                            {{ strtoupper(substr($entry['name'], 0, 2)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                                        #{{ $entry['position'] }} - {{ $entry['name'] }}
                                    </p>
                                    <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ $entry['rank_name'] ?? 'Pas de rank' }}</p>
                                </div>
                            </div>
                            <p class="shrink-0 text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                                {{ number_format((int) $entry['score']) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-theme-sm text-gray-500 dark:text-gray-400">Aucun resultat pour ce leaderboard.</p>
                @endforelse
            </div>

            <div class="hidden overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] md:block">
                <div class="table-responsive">
                    <table class="table-compact w-full min-w-[720px]">
                        <thead style="background-color: rgba(17, 24, 39, 0.85);">
                            <tr class="border-b border-gray-700" style="background-color: rgba(17, 24, 39, 0.85);">
                                <th class="px-6 py-3 text-left font-medium text-gray-300 text-theme-xs" style="background-color: rgba(17, 24, 39, 0.85);">#</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-300 text-theme-xs" style="background-color: rgba(17, 24, 39, 0.85);">Joueur</th>
                                <th class="px-6 py-3 text-left font-medium text-gray-300 text-theme-xs" style="background-color: rgba(17, 24, 39, 0.85);">Rang</th>
                                <th class="px-6 py-3 text-right font-medium text-gray-300 text-theme-xs" style="background-color: rgba(17, 24, 39, 0.85);">{{ $scoreLabel }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($entriesCollection as $entry)
                                @php
                                    $isCurrentUser = (int) $entry['user_id'] === (int) auth()->id();
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-gray-800 {{ $isCurrentUser ? 'bg-success-500/[0.06] dark:bg-success-500/[0.08]' : '' }}">
                                    <td class="px-6 py-4">
                                        <span class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">{{ $entry['position'] }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 min-h-[40px] min-w-[40px] max-h-[40px] max-w-[40px] shrink-0 overflow-hidden rounded-full border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                                @if (!empty($entry['avatar_url']))
                                                    <img
                                                        src="{{ $entry['avatar_url'] }}"
                                                        alt="{{ $entry['name'] }}"
                                                        class="h-10 w-10 min-h-[40px] min-w-[40px] max-h-[40px] max-w-[40px] object-cover"
                                                        style="width:40px;height:40px;min-width:40px;min-height:40px;max-width:40px;max-height:40px;object-fit:cover;display:block;border-radius:9999px;"
                                                        loading="lazy"
                                                    />
                                                @else
                                                    <span class="flex h-full w-full items-center justify-center text-theme-xs font-semibold text-gray-700 dark:text-gray-200">
                                                        {{ strtoupper(substr($entry['name'], 0, 2)) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $entry['name'] }}</p>
                                                @if ($isCurrentUser)
                                                    <p class="text-theme-xs text-success-600 dark:text-success-400">Toi</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $entry['rank_name'] ?? 'Pas de rank' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format((int) $entry['score']) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400">
                                        Aucun resultat pour ce leaderboard.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection
