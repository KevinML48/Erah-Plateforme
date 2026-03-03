@props([
    'wrap' => true,
    'innerClass' => '',
])

<div {{ $attributes->class(['tt-section']) }}>
    <div class="{{ trim('tt-section-inner '.($wrap ? 'tt-wrap ' : '').$innerClass) }}">
        {{ $slot }}
    </div>
</div>

