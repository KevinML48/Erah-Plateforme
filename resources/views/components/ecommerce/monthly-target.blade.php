<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <div class="mb-5 flex items-start justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Match a parier</h3>
            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Prochain match ERAH disponible au pronostic</p>
        </div>
        <x-common.dropdown-menu />
    </div>

    @if ($targetMatch)
        @php
            $startsAt = $targetMatch->starts_at;
            $hoursToStart = $startsAt ? now()->diffInHours($startsAt, false) : null;
            $minutesToStart = $startsAt ? now()->diffInMinutes($startsAt, false) : null;

            $status = $targetMatch->status?->value ?? 'N/A';
            $statusClasses = match ($status) {
                'OPEN' => 'border-success-500/30 bg-success-500/15 text-success-300',
                'LOCKED' => 'border-warning-500/30 bg-warning-500/15 text-warning-300',
                'COMPLETED' => 'border-gray-500/30 bg-gray-500/15 text-gray-300',
                default => 'border-brand-500/30 bg-brand-500/15 text-brand-300',
            };

            $urgencyPercent = 0;
            if ($hoursToStart !== null) {
                $urgencyPercent = max(5, min(100, (int) round(100 - ((max($hoursToStart, 0) / 72) * 100))));
            }

            $titleParts = preg_split('/\s+vs\s+/i', (string) $targetMatch->title, 2);
            $teamA = trim($titleParts[0] ?? 'ERAH');
            $teamB = trim($titleParts[1] ?? 'Adversaire');

            $windowLabel = 'Pronostic ouvert';
            if ($minutesToStart !== null && $minutesToStart < 0) {
                $windowLabel = 'Demarrage imminent';
            }
            if ($status === 'LOCKED') {
                $windowLabel = 'Pronostic verrouille';
            }
            if ($status === 'COMPLETED') {
                $windowLabel = 'Match termine';
            }
        @endphp

        <div class="relative overflow-hidden rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900/90 via-gray-900/65 to-brand-500/10 p-4 sm:p-5">
            <div class="pointer-events-none absolute -top-20 -right-24 h-56 w-56 rounded-full bg-brand-500/15 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-20 h-56 w-56 rounded-full bg-success-500/10 blur-3xl"></div>

            <div class="relative z-10 mb-4 flex flex-wrap items-center justify-between gap-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-brand-500/30 bg-brand-500/15 px-3 py-1 text-theme-xs font-medium text-brand-300">
                    <span class="inline-block h-2 w-2 rounded-full bg-brand-400"></span>
                    {{ $targetMatch->game }}
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full border px-2.5 py-1 text-theme-xs font-medium {{ $statusClasses }}">{{ $status }}</span>
                    <span class="inline-flex rounded-full border border-success-500/30 bg-success-500/15 px-2.5 py-1 text-theme-xs font-medium text-success-300">+{{ number_format((int) $targetMatch->points_reward) }} pts</span>
                </div>
            </div>

            <div class="relative z-10 grid items-center gap-3 sm:grid-cols-[1fr_auto_1fr]">
                <div class="rounded-xl border border-gray-700/80 bg-gray-900/50 p-3 text-center sm:text-left">
                    <p class="text-theme-xs uppercase tracking-wide text-gray-400">Equipe</p>
                    <p class="mt-1 text-base font-semibold text-white/95">{{ $teamA }}</p>
                </div>

                <div class="flex items-center justify-center">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-brand-500/40 bg-brand-500/20 text-sm font-bold text-brand-200 shadow-[0_0_24px_rgba(70,95,255,0.35)]">VS</span>
                </div>

                <div class="rounded-xl border border-gray-700/80 bg-gray-900/50 p-3 text-center sm:text-left">
                    <p class="text-theme-xs uppercase tracking-wide text-gray-400">Equipe</p>
                    <p class="mt-1 text-base font-semibold text-white/95">{{ $teamB }}</p>
                </div>
            </div>

            <div class="relative z-10 mt-4 grid gap-3 lg:grid-cols-[1fr_auto] lg:items-end">
                <div class="rounded-xl border border-gray-700/80 bg-gray-900/50 p-3">
                    <div class="mb-2 flex items-center justify-between text-theme-xs text-gray-300">
                        <span>Debut: {{ $startsAt?->format('d/m/Y H:i') }}</span>
                        <span>{{ $windowLabel }}</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-800">
                        <div class="h-2 rounded-full bg-brand-500 transition-all" style="width: {{ $urgencyPercent }}%"></div>
                    </div>
                    <p class="mt-2 text-theme-xs text-gray-400">Intensite: {{ $urgencyPercent }}%</p>
                </div>

                <a
                    href="{{ route('matches.show', $targetMatch) }}"
                    class="inline-flex h-11 items-center justify-center rounded-lg bg-brand-500 px-5 text-theme-sm font-medium text-white hover:bg-brand-600"
                >
                    Parier maintenant
                </a>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-gray-700 bg-gray-800/40 p-5 text-center">
            <p class="text-theme-sm font-medium text-gray-200">Aucun match a parier pour le moment.</p>
            <p class="mt-1 text-theme-xs text-gray-400">Les prochains matchs apparaitront automatiquement ici.</p>
        </div>
    @endif
</div>
