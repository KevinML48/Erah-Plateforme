@props([
    'variant' => 'default',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'magnetic' => false,
])

@php
    $variantClass = match ($variant) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'outline' => 'btn-outline',
        'dark' => 'btn-dark',
        'link' => 'btn-link',
        'danger' => 'btn-danger',
        default => 'btn-secondary',
    };

    $sizeClass = match ($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => '',
    };

    $classes = trim('btn '.$variantClass.' '.$sizeClass);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
