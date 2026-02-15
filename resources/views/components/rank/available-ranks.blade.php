@php
    $userPoints = (int) ($user?->points_balance ?? 0);
    $currentRankName = $currentRank?->name ?? 'Bronze';
    $nextRankLabel = $nextRank?->name;
    $completionAllRanks = $totalRanksCount > 0 ? (int) round(($unlockedRanksCount / $totalRanksCount) * 100) : 0;
@endphp

<div class="space-y-6">
    <x-ecommerce.points-balance-card
        :current-user-points="$userPoints"
        :current-rank-name="$currentRankName"
        :progress-to-next-rank="$progressToNextRank"
        :next-rank-name="$nextRankLabel"
        class="w-full"
    />

    <x-common.component-card title="ERAH Rank Progress" desc="Vue rapide de ta progression.">
        <div class="flex flex-wrap items-center gap-2">
            <x-ui.badge variant="light" color="primary">Position {{ $currentRankPosition }}/{{ $totalRanksCount }}</x-ui.badge>
            <x-ui.badge variant="light" color="success">{{ $unlockedRanksCount }} debloques</x-ui.badge>
            <x-ui.badge variant="light" color="info">{{ $completionAllRanks }}% global</x-ui.badge>
        </div>

        @if ($user)
            <x-ui.alert
                variant="{{ $nextRank ? 'info' : 'success' }}"
                title="{{ $nextRank ? 'Objectif: '.$nextRankLabel : 'Objectif complete' }}"
                message="{{ $nextRank ? 'Continue, il te manque '.number_format($pointsToNextRank).' points.' : 'Tu as atteint le rang maximal.' }}"
            />
        @endif
    </x-common.component-card>

    <x-common.component-card title="Paliers disponibles" desc="Responsive: carrousel mobile et liste desktop.">
        <div class="md:hidden">
            <div class="no-scrollbar -mx-1 flex snap-x gap-3 overflow-x-auto px-1 pb-1">
                @forelse ($ranks as $rank)
                    @php
                        $isCurrent = $user?->rank_id === $rank->id;
                        $isUnlocked = $userPoints >= (int) $rank->min_points;
                        $missingPoints = max(0, (int) $rank->min_points - $userPoints);
                    @endphp
                    <div class="w-[260px] shrink-0 snap-start rounded-xl border bg-white p-4 dark:bg-white/[0.03] {{ $isUnlocked ? 'border-success-500 dark:border-success-500' : 'border-gray-200 dark:border-gray-800' }}">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                {!! $rankIcons[$rank->slug] ?? $rankIcons['default'] !!}
                            </span>
                            @if ($isCurrent)
                                <x-ui.badge variant="light" color="primary">Actuel</x-ui.badge>
                            @elseif ($isUnlocked)
                                <x-ui.badge variant="light" color="success">Debloque</x-ui.badge>
                            @else
                                <x-ui.badge variant="light" color="warning">Bloque</x-ui.badge>
                            @endif
                        </div>
                        <p class="mt-3 text-base font-semibold text-gray-800 dark:text-white/90">{{ $rank->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Seuil: {{ number_format($rank->min_points) }} pts</p>
                        @if (!$isUnlocked)
                            <p class="mt-2 text-sm text-gray-700 dark:text-white/90">{{ number_format($missingPoints) }} pts manquants</p>
                        @endif
                    </div>
                @empty
                    <div class="w-full">
                        <x-ui.alert
                            variant="warning"
                            title="Aucun rank configure"
                            message="Ajoute des ranks avec le seeder pour activer la progression."
                        />
                    </div>
                @endforelse
            </div>
        </div>

        <div class="hidden md:block">
            <div class="space-y-3">
                @forelse ($ranks as $rank)
                    @php
                        $isCurrent = $user?->rank_id === $rank->id;
                        $isUnlocked = $userPoints >= (int) $rank->min_points;
                        $missingPoints = max(0, (int) $rank->min_points - $userPoints);
                        $localProgress = (int) min(100, round(($userPoints / max(1, (int) $rank->min_points)) * 100));
                    @endphp
                    <div class="rounded-xl border bg-white px-4 py-3 dark:bg-white/[0.03] {{ $isUnlocked ? 'border-success-500 dark:border-success-500' : 'border-gray-200 dark:border-gray-800' }}">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                    {!! $rankIcons[$rank->slug] ?? $rankIcons['default'] !!}
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $rank->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Seuil: {{ number_format($rank->min_points) }} pts</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($isCurrent)
                                    <x-ui.badge variant="light" color="primary">Actuel</x-ui.badge>
                                @elseif ($isUnlocked)
                                    <x-ui.badge variant="light" color="success">Debloque</x-ui.badge>
                                @else
                                    <x-ui.badge variant="light" color="warning">Bloque</x-ui.badge>
                                @endif
                            </div>
                        </div>
                        @if (!$isUnlocked)
                            <div class="mt-3">
                                <div class="mb-1 flex items-center justify-between text-sm text-gray-600 dark:text-gray-300">
                                    <span>{{ number_format($missingPoints) }} pts manquants</span>
                                    <span>{{ $localProgress }}%</span>
                                </div>
                                <div class="h-1.5 rounded-full bg-gray-200 dark:bg-gray-800">
                                    <div class="h-1.5 rounded-full bg-brand-500/80" style="width: {{ max(2, $localProgress) }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <x-ui.alert
                        variant="warning"
                        title="Aucun rank configure"
                        message="Ajoute des ranks avec le seeder pour activer la progression."
                    />
                @endforelse
            </div>
        </div>
    </x-common.component-card>

</div>
