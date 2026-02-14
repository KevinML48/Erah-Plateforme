<div
    class="flex h-full min-h-[420px] flex-col rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6"
    x-data="{
        index: 0,
        total: {{ $rewards->count() }},
        timer: null,
        touchStartX: null,
        touchStartY: null,
        next() {
            if (this.total <= 1) return;
            this.index = this.index >= (this.total - 1) ? 0 : this.index + 1;
        },
        prev() {
            if (this.total <= 1) return;
            this.index = this.index <= 0 ? (this.total - 1) : this.index - 1;
        },
        go(i) {
            this.index = Math.max(0, Math.min(i, this.total - 1));
        },
        startAuto() {
            if (this.total <= 1) return;
            this.stopAuto();
            this.timer = setInterval(() => this.next(), 4200);
        },
        stopAuto() {
            if (!this.timer) return;
            clearInterval(this.timer);
            this.timer = null;
        },
        onTouchStart(event) {
            const touch = event.touches?.[0];
            if (!touch) return;
            this.touchStartX = touch.clientX;
            this.touchStartY = touch.clientY;
        },
        onTouchEnd(event) {
            if (this.touchStartX === null || this.touchStartY === null) return;

            const touch = event.changedTouches?.[0];
            if (!touch) return;

            const deltaX = touch.clientX - this.touchStartX;
            const deltaY = touch.clientY - this.touchStartY;

            this.touchStartX = null;
            this.touchStartY = null;

            if (Math.abs(deltaX) < 35 || Math.abs(deltaX) <= Math.abs(deltaY)) return;

            if (deltaX < 0) this.next();
            else this.prev();
        },
        init() {
            this.startAuto();
        }
    }"
    @mouseenter="stopAuto()"
    @mouseleave="startAuto()"
>
    <div class="mb-5 flex items-start justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Rewards disponibles</h3>
            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">Slide auto + swipe tactile des recompenses en points</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="rounded-full border border-brand-500/30 bg-brand-500/15 px-3 py-1 text-xs font-medium text-brand-400">
                {{ $rewards->count() }} rewards
            </span>
            <button
                type="button"
                @click="prev()"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
            >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="stroke-current">
                    <path d="M10 3.5L5.5 8L10 12.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <button
                type="button"
                @click="next()"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
            >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="stroke-current">
                    <path d="M6 3.5L10.5 8L6 12.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </div>

    @if ($rewards->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-700 bg-gray-800/40 p-5 text-center">
            <p class="text-theme-sm font-medium text-gray-200">Aucun reward disponible.</p>
            <p class="mt-1 text-theme-xs text-gray-400">Ajoute des rewards actifs pour alimenter ce carrousel.</p>
        </div>
    @else
        <div
            class="flex-1 overflow-hidden"
            @touchstart.passive="onTouchStart($event)"
            @touchend.passive="onTouchEnd($event)"
        >
            <div class="flex h-full transition-transform duration-500 ease-out" :style="`transform: translateX(-${index * 100}%);`">
                @foreach ($rewards as $reward)
                    @php
                        $image = $reward->image_url ?: asset('images/grid-image/image-01.png');
                    @endphp
                    <div class="w-full shrink-0">
                        <div class="relative flex h-full min-h-[250px] flex-col overflow-hidden rounded-2xl border border-gray-700 bg-gradient-to-br from-gray-900/90 via-gray-900/70 to-brand-500/10 p-4 sm:p-5">
                            <div class="pointer-events-none absolute -top-16 -right-16 h-40 w-40 rounded-full bg-brand-500/20 blur-3xl"></div>
                            <div class="pointer-events-none absolute -bottom-14 -left-14 h-36 w-36 rounded-full bg-success-500/10 blur-3xl"></div>

                            <div class="relative z-10">
                                <div class="overflow-hidden rounded-xl border border-gray-700 bg-gray-800/50" style="height:220px;">
                                    <img
                                        src="{{ $image }}"
                                        alt="{{ $reward->name }}"
                                        class="block h-full w-full object-cover"
                                        style="width:100%;height:100%;object-fit:cover;"
                                    />
                                </div>
                            </div>

                            <div class="relative z-10 mt-4">
                                <p class="line-clamp-1 text-base font-semibold text-white/95">{{ $reward->name }}</p>
                                <p class="mt-1 line-clamp-3 text-theme-sm text-gray-300">
                                    {{ $reward->description ?: 'Reward communautaire ERAH Esport.' }}
                                </p>
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span class="rounded-full border border-success-500/30 bg-success-500/15 px-2.5 py-1 text-theme-xs font-semibold text-success-300">
                                        {{ number_format((int) $reward->points_cost) }} pts
                                    </span>
                                    <span class="rounded-full border border-gray-600 px-2.5 py-1 text-theme-xs font-medium text-gray-300">
                                        {{ $reward->stock === null ? 'Stock illimite' : 'Stock: '.$reward->stock }}
                                    </span>
                                </div>
                            </div>

                            <div class="relative z-10 mt-auto pt-4">
                                <a
                                    href="{{ route('rewards.show', $reward->slug) }}"
                                    class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600"
                                >
                                    Voir le reward
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($rewards->count() > 1)
            <div class="mt-4 flex items-center justify-center gap-2">
                @foreach ($rewards as $reward)
                    <button
                        type="button"
                        @click="go({{ $loop->index }})"
                        class="h-2.5 w-2.5 rounded-full transition-all"
                        :class="index === {{ $loop->index }} ? 'w-6 bg-brand-500' : 'bg-gray-300 dark:bg-gray-700'"
                    ></button>
                @endforeach
            </div>
        @endif
    @endif
</div>
