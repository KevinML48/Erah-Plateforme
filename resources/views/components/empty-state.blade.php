@props(['title' => 'Empty', 'message' => null])
<div {{ $attributes }}>
    <strong>{{ $title }}</strong>
    <p>{{ $message ?? $slot }}</p>
</div>
