@props([
    'label' => null,
    'name',
    'value' => null,
    'rows' => 5,
])

<div {{ $attributes->class('tt-form-group') }}>
    @if($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <textarea
        class="tt-form-control"
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->except(['class', 'name', 'id', 'rows']) }}
    >{{ old($name, $value) }}</textarea>
    @error($name)
    <small class="tt-form-text" style="color:#f87171;">{{ $message }}</small>
    @enderror
</div>
