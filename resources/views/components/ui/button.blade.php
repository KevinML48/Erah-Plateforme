@props([
    'variant' => 'default',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'magnetic' => false,
])

@php
    $variantClass = match ($variant) {
        'primary' => 'tt-btn-primary',
        'secondary' => 'tt-btn-secondary',
        'outline' => 'tt-btn-outline',
        'dark' => 'tt-btn-dark',
        'link' => 'tt-btn-link',
        'danger' => 'tt-btn-primary',
        default => 'tt-btn-secondary',
    };

    $sizeClass = match ($size) {
        'sm' => 'tt-btn-sm',
        'lg' => 'tt-btn-lg',
        default => '',
    };

    $classes = trim('tt-btn '.$variantClass.' '.$sizeClass);
    $label = trim(strip_tags((string) $slot)) ?: 'Action';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        <span data-hover="{{ $label }}">{{ $slot }}</span>
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        <span data-hover="{{ $label }}">{{ $slot }}</span>
    </button>
@endif
