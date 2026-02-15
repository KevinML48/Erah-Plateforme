<div class="premium-card p-4 md:p-5">
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h3 class="premium-title text-base">Actions rapides</h3>
            <p class="premium-subtitle text-xs">Acces direct aux zones les plus utiles.</p>
        </div>
        <span class="premium-chip">
            {{ number_format((int) (auth()->user()?->points_balance ?? 0)) }} pts
        </span>
    </div>

    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
        <a href="{{ route('missions.index') }}" class="premium-btn-ghost px-3 py-2 text-center text-xs">
            Missions
        </a>
        <a href="{{ route('matches.index') }}" class="premium-btn-ghost px-3 py-2 text-center text-xs">
            Matchs
        </a>
        <a href="{{ route('rewards.index') }}" class="premium-btn-ghost px-3 py-2 text-center text-xs">
            Rewards
        </a>
        <a href="{{ route('leaderboard.all-time') }}" class="premium-btn-ghost px-3 py-2 text-center text-xs">
            Leaderboard
        </a>
        <a href="{{ route('points.activity') }}" class="premium-btn-ghost px-3 py-2 text-center text-xs">
            Activite points
        </a>
        <a href="{{ route('profile') }}" class="premium-btn-ghost px-3 py-2 text-center text-xs">
            Profil
        </a>
    </div>
</div>
