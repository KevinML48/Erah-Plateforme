@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
    'animated' => true,
])

<div {{ $attributes->class('ui-card') }}>
    @if($title || $subtitle)
        <header class="ui-card-header">
            @if($title)
                <h3 class="ui-card-title">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="ui-card-subtitle">{{ $subtitle }}</p>
            @endif
        </header>
    @endif

    {{ $slot }}
</div>
