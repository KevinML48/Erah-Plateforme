@extends('dev-console.layout')

@section('title', 'Data')

@section('content')
    <x-dev.card title="SQLite Tables">
        <div class="dev-grid dev-grid-cards">
            @foreach($tableSummaries as $table)
                <div class="dev-mini-card">
                    <strong>{{ $table['name'] }}</strong>
                    <x-dev.badge variant="info">{{ $table['count'] ?? 'N/A' }}</x-dev.badge>
                </div>
            @endforeach
        </div>
    </x-dev.card>

    <div class="dev-grid dev-grid-2 mt-4">
        @foreach($snapshots as $table => $snapshot)
            <x-dev.card :title="'Latest: '.$table">
                @if(!$snapshot['exists'])
                    <p class="dev-muted">Table absent.</p>
                @elseif(empty($snapshot['rows']))
                    <p class="dev-muted">No rows.</p>
                @else
                    <x-dev.table>
                        <thead>
                        <tr>
                            @foreach(array_keys($snapshot['rows'][0]) as $column)
                                <th>{{ $column }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($snapshot['rows'] as $row)
                            <tr>
                                @foreach($row as $value)
                                    <td>{{ is_null($value) ? 'null' : (string) $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </x-dev.table>
                @endif
            </x-dev.card>
        @endforeach
    </div>
@endsection

