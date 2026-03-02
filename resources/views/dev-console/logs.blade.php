@extends('dev-console.layout')

@section('title', 'Logs')

@section('content')
    <x-dev.card title="Laravel Log Viewer">
        <form method="GET" class="dev-filters">
            <x-dev.input name="search" label="Search" :value="$search" placeholder="error, exception, class..." />
            <x-dev.button type="submit" variant="primary">Filter</x-dev.button>
        </form>
        <p class="dev-muted mt-3">Source: {{ $logPath }}</p>
    </x-dev.card>

    <x-dev.card title="Last lines ({{ count($lines) }})" class="mt-4">
        @if(empty($lines))
            <p class="dev-muted">No log line available.</p>
        @else
            <pre class="dev-log-block">@foreach($lines as $line){{ $line }}
@endforeach</pre>
        @endif
    </x-dev.card>
@endsection

