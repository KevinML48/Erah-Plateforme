@props([
    'compact' => false,
])

<div {{ $attributes->class(['table-wrap', $compact ? 'table-compact' : '']) }}>
    <table>
        {{ $slot }}
    </table>
</div>
