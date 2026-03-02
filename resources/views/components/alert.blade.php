@props(['type' => 'info', 'message' => null])
@if($message)
    <div {{ $attributes }}>{{ $message }}</div>
@else
    <div {{ $attributes }}>{{ $slot }}</div>
@endif
