@props([
    'label' => 'Actions',
])

<détails {{ $attributes->class('app-dropdown') }}>
    <summary class="app-btn app-btn-ghost">{{ $label }}</summary>
    <div class="app-dropdown-menu">
        {{ $slot }}
    </div>
</détails>
