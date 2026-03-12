@php
    $feedItems = collect($feedItems ?? []);
@endphp

@if($feedItems->isEmpty())
    <tr>
        <td colspan="8">
            <div class="adm-empty">Aucun evenement pour ces filtres.</div>
        </td>
    </tr>
@else
    @foreach($feedItems as $item)
        <tr>
            <td><span class="adm-pill">{{ $item['source_label'] ?? '-' }}</span></td>
            <td>{{ $item['type_label'] ?? '-' }}</td>
            <td>{{ $item['module_label'] ?? '-' }}</td>
            <td>{{ $item['occurred_at_label'] ?? '-' }}</td>
            <td>
                @if(!empty($item['user_url']))
                    <a href="{{ $item['user_url'] }}">{{ $item['user_label'] ?? '-' }}</a>
                @else
                    {{ $item['user_label'] ?? '-' }}
                @endif
            </td>
            <td>
                @if(!empty($item['target_url']))
                    <a href="{{ $item['target_url'] }}">{{ $item['target_label'] ?? '-' }}</a>
                @else
                    {{ $item['target_label'] ?? '-' }}
                @endif
            </td>
            <td>{{ $item['summary'] ?? '-' }}</td>
            <td>
                <div class="adm-row-actions">
                    <a href="{{ $item['detail_url'] ?? route('admin.dashboard') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                        <span data-hover="Detail">Detail</span>
                    </a>
                    @if(!empty($item['primary_action']) && ($item['primary_action']['method'] ?? '') === 'POST')
                        <form method="POST" action="{{ $item['primary_action']['url'] ?? route('admin.dashboard') }}">
                            @csrf
                            <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                <span data-hover="{{ $item['primary_action']['label'] ?? 'Action' }}">{{ $item['primary_action']['label'] ?? 'Action' }}</span>
                            </button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@endif

