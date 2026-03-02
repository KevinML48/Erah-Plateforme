@props([
    'title' => 'Aucune donnee',
    'message' => null,
])

<div {{ $attributes->class('ui-empty-state') }}>
    <h4>{{ $title }}</h4>
    @if($message)
        <p class="meta">{{ $message }}</p>
    @endif

    {{ $slot }}
</div>
