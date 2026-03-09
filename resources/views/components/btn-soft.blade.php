@php($label = trim(strip_tags((string) $slot)) ?: 'Action')

<button {{ $attributes->class('tt-btn tt-btn-secondary') }} type="{{ $type ?? 'button' }}">
    <span data-hover="{{ $label }}">{{ $slot }}</span>
</button>
