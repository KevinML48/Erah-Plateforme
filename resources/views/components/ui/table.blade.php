@props([
    'compact' => false,
])

<div {{ $attributes->merge(['data-responsive' => 'cards'])->class(['table-wrap', $compact ? 'table-compact' : '']) }}>
    <table>
        {{ $slot }}
    </table>
</div>
