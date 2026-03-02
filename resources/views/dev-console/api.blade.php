@extends('dev-console.layout')

@section('title', 'API Explorer')

@section('content')
    <x-dev.card title="API Token + Filters">
        <form method="POST" action="{{ route('dev.api.token') }}" class="dev-filters">
            @csrf
            <x-dev.input name="token" label="Sanctum token" :value="$token" placeholder="paste bearer token" />
            <x-dev.button type="submit" variant="primary">Save token</x-dev.button>
        </form>

        <form method="GET" class="dev-filters mt-3">
            <x-dev.input name="search" label="Search endpoint" :value="$filters['search']" placeholder="matches, clips, admin..." />
            <label class="dev-field">
                <span class="dev-field-label">Method</span>
                <select class="dev-input" name="method">
                    <option value="">all</option>
                    @foreach(['GET','POST','PUT','PATCH','DELETE'] as $m)
                        <option value="{{ $m }}" @selected($filters['method'] === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </label>
            <x-dev.button type="submit" variant="primary">Filter</x-dev.button>
        </form>
    </x-dev.card>

    <x-dev.card title="API Routes ({{ $routes->count() }})" class="mt-4">
        <x-dev.table>
            <thead>
            <tr>
                <th>Methods</th>
                <th>URI</th>
                <th>Name</th>
                <th>Middleware</th>
                <th>cURL</th>
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
                    <td class="dev-small">{{ implode(', ', $route['middleware']) }}</td>
                    <td>
                        <div class="dev-curl-row">
                            <code id="api-curl-{{ $loop->index }}">{{ $route['curl'] }}</code>
                            <x-dev.button type="button" variant="ghost" data-copy-target="api-curl-{{ $loop->index }}">Copy</x-dev.button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="dev-muted">No API routes found for filters.</td>
                </tr>
            @endforelse
            </tbody>
        </x-dev.table>
    </x-dev.card>
@endsection

