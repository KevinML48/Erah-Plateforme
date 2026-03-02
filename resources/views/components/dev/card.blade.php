@props([
    'title' => null,
])

<section {{ $attributes->merge(['class' => 'dev-card']) }}>
    @if($title)
        <h2 class="dev-card-title">{{ $title }}</h2>
    @endif
    {{ $slot }}
</section>

