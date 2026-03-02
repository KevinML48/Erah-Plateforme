@props([
    'variant' => 'default',
    'type' => 'button',
])

@php
    $base = 'dev-btn';
    $variants = [
        'default' => 'dev-btn-default',
        'primary' => 'dev-btn-primary',
        'danger' => 'dev-btn-danger',
        'ghost' => 'dev-btn-ghost',
    ];
    $variantClass = $variants[$variant] ?? $variants['default'];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $base.' '.$variantClass]) }}>
    {{ $slot }}
</button>

