@props([
    'id' => null,
    'title' => null,
])

<div {{ $attributes->class('app-modal') }} @if($id) id="{{ $id }}" @endif role="dialog" aria-modal="true">
    @if($title)
        <h3>{{ $title }}</h3>
    @endif

    {{ $slot }}
</div>
