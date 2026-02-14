<div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 sm:px-6 sm:pt-6 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Resume Points
        </h3>

        <!-- Dropdown Menu -->
        <x-common.dropdown-menu />
        <!-- End Dropdown Menu -->
    </div>

    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-success-500/20 bg-success-500/10 px-3 py-2">
            <p class="text-theme-xs text-success-300">Points gagnes</p>
            <p class="mt-1 text-base font-semibold text-success-400">+{{ number_format($totalGained) }}</p>
        </div>
        <div class="rounded-xl border border-error-500/20 bg-error-500/10 px-3 py-2">
            <p class="text-theme-xs text-error-300">Points perdus</p>
            <p class="mt-1 text-base font-semibold text-error-400">-{{ number_format($totalLost) }}</p>
        </div>
        <div class="rounded-xl border border-brand-500/20 bg-brand-500/10 px-3 py-2">
            <p class="text-theme-xs text-brand-300">Net annuel</p>
            <p class="mt-1 text-base font-semibold {{ $netPoints >= 0 ? 'text-brand-400' : 'text-error-400' }}">
                {{ $netPoints >= 0 ? '+' : '' }}{{ number_format($netPoints) }}
            </p>
        </div>
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <div
            id="chartOne"
            class="-ml-5 h-full min-w-[690px] pl-2 xl:min-w-full"
            data-chart-labels='@json($labels)'
            data-chart-gains='@json($gains)'
            data-chart-losses='@json($losses)'
        ></div>
    </div>
</div>

