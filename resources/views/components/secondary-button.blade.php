@php($label = trim(strip_tags((string) $slot)) ?: 'Action')

<button {{ $attributes->merge(['type' => 'button', 'class' => 'tt-btn tt-btn-secondary']) }}>
    <span data-hover="{{ $label }}">{{ $slot }}</span>
</button>
