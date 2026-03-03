@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
])

<div {{ $attributes->class('tt-form-group') }}>
    @if($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <input
        class="tt-form-control"
        id="{{ $name }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->except(['class', 'value', 'name', 'type', 'id']) }}
    >
    @error($name)
    <small class="tt-form-text" style="color:#f87171;">{{ $message }}</small>
    @enderror
</div>
