@props([
    'label' => null,
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => null,
])

<div {{ $attributes->class('tt-form-group') }}>
    @if($label)
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <select class="tt-form-control" id="{{ $name }}" name="{{ $name }}" {{ $attributes->except(['class', 'name', 'id']) }}>
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($name, $value) === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($name)
    <small class="tt-form-text" style="color:#f87171;">{{ $message }}</small>
    @enderror
</div>
