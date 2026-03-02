@extends('dev-console.layout')

@section('title', 'Hub')

@section('content')
    <div class="dev-grid dev-grid-3">
        <x-dev.card title="Application Status">
            <div class="dev-kv-list">
                @foreach($status as $label => $value)
                    <div class="dev-kv-item">
                        <span>{{ $label }}</span>
                        <strong>{{ $value }}</strong>
                    </div>
                @endforeach
            </div>
        </x-dev.card>

        <x-dev.card title="Quick Data Snapshot">
            <div class="dev-list">
                @foreach($tableCounts as $table)
                    <div class="dev-list-row">
                        <span>{{ $table['table'] }}</span>
                        @if(!$table['exists'])
                            <x-dev.badge variant="warn">absent</x-dev.badge>
                        @else
                            <x-dev.badge variant="info">{{ $table['count'] ?? 'N/A' }}</x-dev.badge>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-dev.card>

        <x-dev.card title="Impersonation">
            <form method="POST" action="{{ route('dev.impersonate') }}" class="dev-stack">
                @csrf
                <x-dev.input label="User ID or Email" name="identifier" placeholder="1 or user@example.com" />
                <x-dev.button variant="primary" type="submit">Login As User</x-dev.button>
            </form>

            <form method="POST" action="{{ route('dev.impersonate') }}" class="mt-3">
                @csrf
                <input type="hidden" name="action" value="logout">
                <x-dev.button variant="ghost" type="submit">Logout Current Session</x-dev.button>
            </form>
        </x-dev.card>
    </div>

    <div class="dev-grid dev-grid-2 mt-4">
        <x-dev.card title="Dispatch Job">
            @if(empty($availableJobs))
                <p class="dev-muted">No zero-argument jobs discovered.</p>
            @else
                <form method="POST" action="{{ route('dev.jobs.dispatch') }}" class="dev-stack">
                    @csrf
                    <label class="dev-field">
                        <span class="dev-field-label">Job Class</span>
                        <select class="dev-input" name="job_class">
                            @foreach($availableJobs as $job)
                                <option value="{{ $job['class'] }}">{{ $job['label'] }} ({{ $job['class'] }})</option>
                            @endforeach
                        </select>
                    </label>
                    <x-dev.button variant="primary" type="submit">Dispatch Job</x-dev.button>
                </form>
            @endif
        </x-dev.card>

        <x-dev.card title="Users (latest)">
            @if($users->isEmpty())
                <p class="dev-muted">No users found.</p>
            @else
                <x-dev.table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role ?? 'user' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </x-dev.table>
            @endif
        </x-dev.card>
    </div>
@endsection

