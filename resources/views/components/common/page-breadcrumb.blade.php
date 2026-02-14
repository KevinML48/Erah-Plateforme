@props(['pageTitle' => 'Page'])

<div class="mb-5 flex flex-wrap items-start justify-between gap-2 sm:mb-6 sm:items-center sm:gap-3">
    <h2 class="max-w-full break-words pr-2 text-lg font-semibold text-gray-800 dark:text-white/90 sm:text-xl">
        {{ $pageTitle }}
    </h2>
    <nav class="w-full sm:w-auto">
        <ol class="flex flex-wrap items-center gap-1.5 text-xs sm:text-sm">
            <li>
                <a
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                    href="{{ url('/') }}"
                >
                    Home
                    <svg
                        class="stroke-current"
                        width="17"
                        height="16"
                        viewBox="0 0 17 16"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366"
                            stroke=""
                            stroke-width="1.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </a>
            </li>
            <li class="text-sm text-gray-800 dark:text-white/90">
                {{ $pageTitle }}
            </li>
        </ol>
    </nav>
</div>
