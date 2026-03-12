@php
    $alertsCollection = collect($alerts ?? []);
@endphp

@if($alertsCollection->isEmpty())
    <div class="adm-empty">Aucune alerte operationnelle.</div>
@else
    <div class="adm-alert-list">
        @foreach($alertsCollection as $alert)
            @php
                $count = (int) ($alert['count'] ?? 0);
                $severity = (string) ($alert['severity'] ?? 'warning');
                $isActive = $count > 0;
            @endphp
            <article class="adm-alert-item is-{{ $severity }} {{ $isActive ? 'is-active' : 'is-idle' }}">
                <div class="adm-alert-head">
                    <h3>{{ $alert['title'] ?? 'Alerte' }}</h3>
                    <span class="adm-pill">{{ $count }}</span>
                </div>
                <p>{{ $alert['description'] ?? '' }}</p>
                <a href="{{ $alert['url'] ?? route('admin.dashboard') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                    <span data-hover="Investiguer">Investiguer</span>
                </a>
            </article>
        @endforeach
    </div>
@endif

