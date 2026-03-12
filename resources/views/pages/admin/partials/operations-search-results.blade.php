@php
    $search = $search ?? ['query' => '', 'groups' => [], 'total_hits' => 0];
    $groups = collect($search['groups'] ?? []);
@endphp

@if(trim((string) ($search['query'] ?? '')) !== '')
    <section class="adm-surface">
        <div class="tt-heading tt-heading-lg margin-bottom-20">
            <h2 class="tt-heading-title tt-text-reveal">Resultats recherche globale</h2>
            <p class="max-width-700 tt-anim-fadeinup text-gray">Requete "{{ $search['query'] }}" · {{ (int) ($search['total_hits'] ?? 0) }} resultat(s).</p>
        </div>

        @if($groups->isEmpty())
            <div class="adm-empty">Aucun resultat global pour cette recherche.</div>
        @else
            <div class="adm-search-grid">
                @foreach($groups as $group)
                    <article class="adm-search-card">
                        <h3>{{ $group['title'] ?? 'Resultats' }}</h3>
                        <div class="adm-mini-list">
                            @foreach(($group['items'] ?? []) as $result)
                                <div class="adm-mini-item">
                                    <div>
                                        <strong>{{ $result['title'] ?? '-' }}</strong>
                                        <p>{{ $result['meta'] ?? '' }}</p>
                                    </div>
                                    <a href="{{ $result['url'] ?? route('admin.dashboard') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Ouvrir">Ouvrir</span></a>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endif

