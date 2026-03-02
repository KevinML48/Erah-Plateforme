@props([
    'variant' => 'default',
])

@php
    $badgeClass = $variant === 'accent'
        ? 'badge badge-accent'
        : 'badge';
@endphp

<span {{ $attributes->class($badgeClass) }}>
    {{ $slot }}
</span>
