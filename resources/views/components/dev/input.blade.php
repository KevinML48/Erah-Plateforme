@props([
    'label' => null,
    'name' => null,
    'value' => '',
    'type' => 'text',
    'placeholder' => '',
])

<label class="dev-field">
    @if($label)
        <span class="dev-field-label">{{ $label }}</span>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'dev-input']) }}
    >
</label>

