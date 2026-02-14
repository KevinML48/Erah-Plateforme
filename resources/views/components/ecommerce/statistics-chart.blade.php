<div
    class="rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6"
    x-data="{
        index: 0,
        total: {{ $matches->count() }},
        perView: 1,
        timer: null,
        get maxIndex() {
            return Math.max(0, this.total - this.perView);
        },
        updatePerView() {
            const w = window.innerWidth;
            this.perView = w >= 1280 ? 3 : (w >= 768 ? 2 : 1);
            if (this.index > this.maxIndex) this.index = this.maxIndex;
        },
        next() {
            if (this.total <= this.perView) return;
            this.index = this.index >= this.maxIndex ? 0 : this.index + 1;
        },
        prev() {
            if (this.total <= this.perView) return;
            this.index = this.index <= 0 ? this.maxIndex : this.index - 1;
        },
        go(i) {
            this.index = Math.max(0, Math.min(i, this.maxIndex));
        },
        startAuto() {
            if (this.total <= this.perView) return;
            this.stopAuto();
            this.timer = setInterval(() => this.next(), 4200);
        },
        stopAuto() {
            if (!this.timer) return;
            clearInterval(this.timer);
            this.timer = null;
        },
        init() {
            this.updatePerView();
            this.startAuto();
            window.addEventListener('resize', () => this.updatePerView());
        }
    }"
    @mouseenter="stopAuto()"
    @mouseleave="startAuto()"
>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">ERAH Match Arena</h3>
            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Slide competitif des matchs et activite</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="rounded-full border border-brand-500/30 bg-brand-500/15 px-3 py-1 text-xs font-medium text-brand-400">
                {{ $matches->count() }} matchs
            </span>
            <button type="button" @click="prev()" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="stroke-current">
                    <path d="M10 3.5L5.5 8L10 12.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <button type="button" @click="next()" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="stroke-current">
                    <path d="M6 3.5L10.5 8L6 12.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </div>

    @if ($matches->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
            Aucun match disponible pour le moment.
        </div>
    @else
        <div class="overflow-hidden">
            <div class="flex gap-3 transition-transform duration-500 ease-out" :style="`transform: translateX(-${index * (100 / perView)}%);`">
                @foreach ($matches as $match)
                    @php
                        $teams = $component->resolveCardTeams($match);
                        $teamA = $teams['left'];
                        $teamB = $teams['right'];
                        $status = $match->status?->value ?? (string) $match->status;
                        $statusStyle = match ($status) {
                            'OPEN' => 'border-success-500/30 bg-success-500/15 text-success-300',
                            'LIVE' => 'border-error-500/30 bg-error-500/15 text-error-300',
                            'LOCKED' => 'border-warning-500/30 bg-warning-500/15 text-warning-300',
                            'COMPLETED' => 'border-gray-500/30 bg-gray-500/15 text-gray-300',
                            default => 'border-brand-500/30 bg-brand-500/15 text-brand-300',
                        };
                    @endphp
                    <div class="shrink-0" :style="`width: calc(${100 / perView}% - 8px)`">
                        <div class="group relative overflow-hidden rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900/90 via-gray-900/70 to-brand-500/10 p-4">
                            <div class="pointer-events-none absolute -top-10 -right-8 h-24 w-24 rounded-full bg-brand-500/25 blur-2xl"></div>
                            <div class="pointer-events-none absolute -bottom-8 -left-8 h-24 w-24 rounded-full bg-cyan-400/10 blur-2xl"></div>
                            <div class="pointer-events-none absolute inset-0 opacity-20" style="background-image: linear-gradient(transparent 60%, rgba(255,255,255,0.08)); background-size: 100% 7px;"></div>

                            <div class="relative z-10 flex items-center justify-between gap-2">
                                <span class="rounded-full border border-brand-500/30 bg-brand-500/15 px-2.5 py-1 text-[11px] font-medium text-brand-300">
                                    {{ $match->game }}{{ $match->format ? ' / '.$match->format : '' }}
                                </span>
                                <span class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusStyle }}">
                                    {{ $status }}
                                </span>
                            </div>

                            <div class="relative z-10 mt-4 rounded-xl border border-gray-700/70 bg-gray-900/50 p-3">
                                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-2">
                                    <div class="rounded-lg border border-gray-700 bg-gray-800/60 px-2 py-2 text-center">
                                        <p class="mb-0.5 text-[10px] uppercase tracking-wide text-gray-400">Equipe A</p>
                                        <p class="line-clamp-1 text-sm font-semibold text-white/90">{{ $teamA }}</p>
                                    </div>
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-brand-500/40 bg-brand-500/20 text-xs font-bold text-brand-200 shadow-[0_0_16px_rgba(70,95,255,0.5)]">VS</span>
                                    <div class="rounded-lg border border-gray-700 bg-gray-800/60 px-2 py-2 text-center">
                                        <p class="mb-0.5 text-[10px] uppercase tracking-wide text-gray-400">Adversaire</p>
                                        <p class="line-clamp-1 text-sm font-semibold text-white/90">{{ $teamB }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative z-10 mt-3 grid grid-cols-2 gap-2">
                                <div class="rounded-lg border border-gray-700 bg-gray-900/45 px-3 py-2">
                                    <p class="text-[11px] text-gray-400">Debut</p>
                                    <p class="text-sm font-medium text-white/90">{{ $match->starts_at?->format('d/m H:i') }}</p>
                                </div>
                                <div class="rounded-lg border border-success-500/30 bg-success-500/10 px-3 py-2">
                                    <p class="text-[11px] text-success-300">Bonus</p>
                                    <p class="text-sm font-semibold text-success-400">+{{ (int) $match->points_reward }}%</p>
                                </div>
                                <div class="rounded-lg border border-gray-700 bg-gray-900/45 px-3 py-2">
                                    <p class="text-[11px] text-gray-400">Markets</p>
                                    <p class="text-sm font-semibold text-white/90">{{ (int) $match->markets_count }}</p>
                                </div>
                                <div class="rounded-lg border border-gray-700 bg-gray-900/45 px-3 py-2">
                                    <p class="text-[11px] text-gray-400">Tickets</p>
                                    <p class="text-sm font-semibold text-white/90">{{ (int) $match->tickets_count }}</p>
                                </div>
                            </div>

                            <div class="relative z-10 mt-4">
                                <a href="{{ route('matches.show', $match) }}" class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600">
                                    Entrer dans l'arena
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($matches->count() > 1)
            <div class="mt-4 flex items-center justify-center gap-2">
                <template x-for="dot in Math.max(1, total - perView + 1)" :key="dot">
                    <button type="button" @click="go(dot - 1)" class="h-2.5 w-2.5 rounded-full transition-all" :class="index === (dot - 1) ? 'w-6 bg-brand-500' : 'bg-gray-300 dark:bg-gray-700'"></button>
                </template>
            </div>
        @endif
    @endif
</div>
