@props([
    'title',
    'desc' => '',
])

<div {{ $attributes->merge(['class' => 'premium-card']) }}>
    <!-- Card Header -->
    <div class="px-4 py-4 sm:px-6 sm:py-5">
        <h3 class="premium-title text-base">
            {{ $title }}
        </h3>
        @if($desc)
            <p class="premium-subtitle mt-1 text-sm">
                {{ $desc }}
            </p>
        @endif
    </div>

    <!-- Card Body -->
    <div class="border-t border-brand-500/15 p-4 sm:p-6">
        <div class="space-y-4 sm:space-y-6">
            {{ $slot }}
        </div>
    </div>
</div>
