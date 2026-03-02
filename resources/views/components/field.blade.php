<input {{ $attributes->merge(['class' => '']) }} name="{{ $name ?? '' }}" value="{{ $value ?? old($name ?? '') }}">
