@extends('dev-console.layout')

@section('title', 'Routes')

@section('content')
    <x-dev.card title="Route Explorer">
        <form method="GET" class="dev-filters">
            <x-dev.input name="search" label="Search" :value="$filters['search']" placeholder="uri, name, middleware..." />
            <label class="dev-field">
                <span class="dev-field-label">Prefix</span>
                <select name="prefix" class="dev-input">
                    <option value="">all</option>
                    @foreach($prefixes as $entry)
                        <option value="{{ $entry }}" @selected($filters['prefix'] === $entry)>{{ $entry }}</option>
                    @endforeach
                </select>
            </label>
            <label class="dev-field">
                <span class="dev-field-label">Method</span>
                <select name="method" class="dev-input">
                    <option value="">all</option>
                    @foreach(['GET','POST','PUT','PATCH','DELETE'] as $m)
                        <option value="{{ $m }}" @selected($filters['method'] === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </label>
            <x-dev.button type="submit" variant="primary">Filter</x-dev.button>
        </form>
    </x-dev.card>

    <x-dev.card title="Routes ({{ $routes->count() }})" class="mt-4">
        <x-dev.table>
            <thead>
            <tr>
                <th>Methods</th>
                <th>URI</th>
                <th>Name</th>
                <th>Action</th>
                <th>Middleware</th>
                <th>Open</th>
            </tr>
            </thead>
            <tbody>
            @forelse($routes as $route)
                <tr>
                    <td>
                        <div class="dev-badge-line">
                            @foreach($route['methods'] as $m)
                                <x-dev.badge variant="{{ $m === 'GET' ? 'success' : 'default' }}">{{ $m }}</x-dev.badge>
                            @endforeach
                        </div>
                    </td>
                    <td><code>{{ $route['uri'] }}</code></td>
                    <td>{{ $route['name'] }}</td>
                    <td class="dev-small">{{ $route['action'] }}</td>
                    <td class="dev-small">{{ implode(', ', $route['middleware']) }}</td>
                    <td>
                        @if($route['open_url'])
                            <a href="{{ $route['open_url'] }}" target="_blank" class="dev-link">Open</a>
                        @else
                            <span class="dev-muted">dynamic</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <div class="dev-curl-row">
                            <code id="curl-{{ $loop->index }}">{{ $route['curl'] }}</code>
                            <x-dev.button
                                variant="ghost"
                                class="dev-copy-btn"
                                data-copy-target="curl-{{ $loop->index }}"
                                type="button"
                            >
                                Copy
                            </x-dev.button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="dev-muted">No route matched current filters.</td>
                </tr>
            @endforelse
            </tbody>
        </x-dev.table>
    </x-dev.card>
@endsection

