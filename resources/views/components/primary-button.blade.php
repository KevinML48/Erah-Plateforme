@php($label = trim(strip_tags((string) $slot)) ?: 'Action')

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'tt-btn tt-btn-primary']) }}>
    <span data-hover="{{ $label }}">{{ $slot }}</span>
</button>
