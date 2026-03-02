@props([
    'variant' => 'default',
])

@php
    $variants = [
        'default' => 'dev-badge-default',
        'success' => 'dev-badge-success',
        'warn' => 'dev-badge-warn',
        'danger' => 'dev-badge-danger',
        'info' => 'dev-badge-info',
    ];
    $variantClass = $variants[$variant] ?? $variants['default'];
@endphp

<span {{ $attributes->merge(['class' => 'dev-badge '.$variantClass]) }}>
    {{ $slot }}
</span>

