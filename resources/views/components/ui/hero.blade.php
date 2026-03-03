@props([
    'title' => null,
    'subtitle' => null,
    'description' => null,
])

<div {{ $attributes->class(['tt-heading tt-heading-lg']) }}>
    @if($subtitle)
        <h3 class="tt-heading-subtitle tt-text-reveal">{{ $subtitle }}</h3>
    @endif

    @if($title)
        <h2 class="tt-heading-title tt-text-reveal">{!! $title !!}</h2>
    @endif

    @if($description)
        <p class="max-width-700 tt-anim-fadeinup text-muted">{!! $description !!}</p>
    @endif

    {{ $slot }}
</div>

