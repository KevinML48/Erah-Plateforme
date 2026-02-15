@php
    $flashMessages = collect([
        ['key' => 'success', 'style' => 'border-success-500/30 bg-success-500/15 text-success-100'],
        ['key' => 'status', 'style' => 'border-brand-500/30 bg-brand-500/15 text-brand-100'],
        ['key' => 'error', 'style' => 'border-error-500/30 bg-error-500/15 text-error-100'],
        ['key' => 'warning', 'style' => 'border-warning-500/30 bg-warning-500/15 text-warning-100'],
    ])->filter(fn ($item) => session($item['key']));
@endphp

@if ($flashMessages->isNotEmpty() || $errors->any())
    <div class="fixed right-4 top-20 z-[120] flex w-[calc(100vw-2rem)] max-w-md flex-col gap-3 sm:right-6 sm:top-24">
        @foreach ($flashMessages as $item)
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 4200)"
                x-show="show"
                x-transition.opacity.duration.250ms
                class="rounded-xl border px-4 py-3 shadow-lg backdrop-blur {{ $item['style'] }}"
            >
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium">{{ session($item['key']) }}</p>
                    <button type="button" class="text-xs opacity-70 hover:opacity-100" @click="show = false">Fermer</button>
                </div>
            </div>
        @endforeach

        @if ($errors->any())
            <div
                x-data="{ show: true }"
                class="rounded-xl border border-error-500/30 bg-error-500/15 px-4 py-3 text-error-100 shadow-lg backdrop-blur"
                x-show="show"
                x-transition.opacity.duration.250ms
            >
                <div class="mb-2 flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold">Certains champs sont invalides.</p>
                    <button type="button" class="text-xs opacity-70 hover:opacity-100" @click="show = false">Fermer</button>
                </div>
                <ul class="list-disc space-y-1 pl-4 text-xs">
                    @foreach ($errors->take(3)->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
