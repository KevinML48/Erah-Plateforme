@php($label = trim(strip_tags((string) $slot)) ?: 'Action')

<button {{ $attributes->class('tt-btn tt-btn-outline') }} type="{{ $type ?? 'button' }}">
    <span data-hover="{{ $label }}">{{ $slot }}</span>
</button>
