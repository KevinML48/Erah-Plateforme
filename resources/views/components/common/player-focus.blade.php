@php
    $user = auth()->user();
    $nextMatch = null;
    $missionsInProgress = 0;
    $pendingRedemptions = 0;

    if ($user) {
        $nextMatch = \App\Models\EsportMatch::query()
            ->where('status', \App\Enums\MatchStatus::Open->value)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->first();

        $missionsInProgress = \App\Models\MissionProgress::query()
            ->where('user_id', $user->id)
            ->whereNull('completed_at')
            ->count();

        $pendingRedemptions = \App\Models\RewardRedemption::query()
            ->where('user_id', $user->id)
            ->where('status', \App\Enums\RewardRedemptionStatus::Pending->value)
            ->count();
    }
@endphp

@if ($user)
    <div class="premium-card p-4 md:p-5">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div>
                <h3 class="premium-title text-base">Focus du moment</h3>
                <p class="premium-subtitle text-xs">Ce que tu peux faire maintenant pour progresser.</p>
            </div>
            <span class="inline-flex items-center rounded-full border border-success-500/30 bg-success-500/15 px-3 py-1 text-xs font-medium text-success-300">
                Rang: {{ $user->rank?->name ?? 'Pas de rank' }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">
            <div class="rounded-xl border border-brand-500/20 bg-gray-900/60 p-3">
                <p class="text-xs text-gray-300">Prochain match ouvert</p>
                <p class="mt-1 truncate text-sm font-semibold text-white">{{ $nextMatch?->title ?? 'Aucun match ouvert' }}</p>
                <p class="text-xs text-gray-300">{{ $nextMatch?->starts_at?->format('d/m H:i') ?? 'N/A' }}</p>
                <a href="{{ route('matches.index') }}" class="mt-2 inline-flex text-xs font-medium text-brand-400 hover:text-brand-300">Voir les matchs</a>
            </div>

            <div class="rounded-xl border border-warning-500/20 bg-gray-900/60 p-3">
                <p class="text-xs text-gray-300">Missions en cours</p>
                <p class="mt-1 text-sm font-semibold text-warning-300">{{ $missionsInProgress }}</p>
                <p class="text-xs text-gray-300">Continue pour gagner plus de points.</p>
                <a href="{{ route('me.missions.progression') }}" class="mt-2 inline-flex text-xs font-medium text-brand-400 hover:text-brand-300">Voir mes missions</a>
            </div>

            <div class="rounded-xl border border-success-500/20 bg-gray-900/60 p-3">
                <p class="text-xs text-gray-300">Demandes rewards en attente</p>
                <p class="mt-1 text-sm font-semibold text-brand-300">{{ $pendingRedemptions }}</p>
                <p class="text-xs text-gray-300">Suis ton historique et tes statuts.</p>
                <a href="{{ route('me.redemptions.index') }}" class="mt-2 inline-flex text-xs font-medium text-brand-400 hover:text-brand-300">Mes demandes</a>
            </div>
        </div>
    </div>
@endif
