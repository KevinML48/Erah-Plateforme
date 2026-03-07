@extends('marketing.layouts.template')

@section('title', 'Campagnes Clips | ERAH Plateforme')
@section('meta_description', 'Gestion admin des votes clip de la semaine et action du mois pour le programme supporter.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-campaign-clip-pool { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
        .adm-campaign-clip-option { border: 1px solid var(--adm-border-soft); border-radius: 14px; padding: 10px; display: grid; gap: 8px; }
        .adm-campaign-clip-option img { width: 100%; height: 120px; object-fit: cover; border-radius: 10px; }
        .adm-campaign-list { display: grid; gap: 14px; }
        .adm-campaign-card { border: 1px solid var(--adm-border); border-radius: 22px; padding: 18px; background: rgba(255,255,255,.03); display: grid; gap: 16px; }
        .adm-campaign-entry-list { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
        .adm-campaign-entry { border: 1px solid var(--adm-border-soft); border-radius: 14px; padding: 10px; }
        .adm-campaign-entry img { width: 100%; height: 120px; object-fit: cover; border-radius: 10px; margin-bottom: 8px; }
        @media (max-width: 1199.98px) {
            .adm-campaign-clip-pool,
            .adm-campaign-entry-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 767.98px) {
            .adm-campaign-clip-pool,
            .adm-campaign-entry-list { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Supporter Votes',
        'heroTitle' => 'Campagnes clips',
        'heroDescription' => 'Creation, fermeture et cloture des campagnes supporter pour clip de la semaine et action du mois.',
        'heroMaskDescription' => 'Pilotage editorial des votes premium supporters.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Nouvelle campagne</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Choisissez les clips deja publies, puis ouvrez la campagne weekly ou monthly pour les supporters actifs.</p>
                        </div>

                        <form method="POST" action="{{ route('admin.clips.campaigns.store') }}" class="adm-form tt-form tt-form-creative tt-form-lg">
                            @csrf
                            <div class="adm-form-grid-4">
                                <div class="tt-form-group">
                                    <label for="campaign-type">Type</label>
                                    <select class="tt-form-control" id="campaign-type" name="type">
                                        @foreach($types as $type)
                                            <option value="{{ $type }}">{{ strtoupper($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tt-form-group adm-col-span-2">
                                    <label for="campaign-title">Titre</label>
                                    <input class="tt-form-control" id="campaign-title" name="title" type="text" value="{{ old('title') }}" required>
                                </div>
                                <div class="tt-form-group">
                                    <label for="campaign-status">Statut</label>
                                    <select class="tt-form-control" id="campaign-status" name="status">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" @selected($status === \App\Models\ClipVoteCampaign::STATUS_ACTIVE)>{{ strtoupper($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="tt-form-group">
                                    <label for="campaign-starts">Debut</label>
                                    <input class="tt-form-control" id="campaign-starts" name="starts_at" type="datetime-local" value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}" required>
                                </div>
                                <div class="tt-form-group">
                                    <label for="campaign-ends">Fin</label>
                                    <input class="tt-form-control" id="campaign-ends" name="ends_at" type="datetime-local" value="{{ old('ends_at', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required>
                                </div>
                            </div>

                            <div class="tt-heading tt-heading-sm margin-bottom-20">
                                <h3 class="tt-heading-subtitle">Selection clips</h3>
                                <h2 class="tt-heading-title">Pool de vote</h2>
                            </div>
                            <div class="adm-campaign-clip-pool">
                                @foreach($clips as $clip)
                                    <label class="adm-campaign-clip-option">
                                        <input type="checkbox" name="clip_ids[]" value="{{ $clip->id }}" @checked(collect(old('clip_ids', []))->contains($clip->id))>
                                        <img src="{{ $clip->thumbnail_url ?: '/template/assets/img/logo.png' }}" alt="{{ $clip->title }}">
                                        <strong>{{ $clip->title }}</strong>
                                        <span class="text-gray">{{ optional($clip->published_at)->format('d/m/Y H:i') }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="adm-row-actions margin-top-30">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Creer campagne">Creer campagne</span></button>
                            </div>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Campagnes existantes</h2>
                        </div>

                        @if($campaigns->count())
                            <div class="adm-campaign-list">
                                @foreach($campaigns as $campaign)
                                    <article class="adm-campaign-card tt-anim-fadeinup">
                                        <div class="adm-row-actions" style="justify-content: space-between; align-items: flex-start;">
                                            <div>
                                                <h3 class="no-margin">{{ $campaign->title }}</h3>
                                                <p class="text-gray margin-top-10 no-margin">{{ strtoupper($campaign->type) }} - {{ strtoupper($campaign->status) }} - {{ (int) $campaign->votes_count }} vote(s)</p>
                                                <p class="text-gray no-margin">{{ optional($campaign->starts_at)->format('d/m/Y H:i') }} -> {{ optional($campaign->ends_at)->format('d/m/Y H:i') }}</p>
                                                @if($campaign->winnerClip)
                                                    <p class="text-gray margin-top-10 no-margin">Gagnant: {{ $campaign->winnerClip->title }}</p>
                                                @endif
                                            </div>
                                            <div class="adm-row-actions">
                                                <form method="POST" action="{{ route('admin.clips.campaigns.close', $campaign->id) }}">
                                                    @csrf
                                                    <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Clore">Clore</span></button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.clips.campaigns.settle', $campaign->id) }}">
                                                    @csrf
                                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Setter">Setter</span></button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="adm-campaign-entry-list">
                                            @foreach($campaign->entries as $entry)
                                                @if($entry->clip)
                                                    <div class="adm-campaign-entry">
                                                        <img src="{{ $entry->clip->thumbnail_url ?: '/template/assets/img/logo.png' }}" alt="{{ $entry->clip->title }}">
                                                        <strong>{{ $entry->clip->title }}</strong>
                                                        <div class="text-gray">Slug: {{ $entry->clip->slug }}</div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        <details class="adm-advanced">
                                            <summary>Modifier cette campagne</summary>
                                            <div class="adm-advanced-body">
                                                <form method="POST" action="{{ route('admin.clips.campaigns.update', $campaign->id) }}" class="adm-form tt-form tt-form-creative tt-form-lg">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="adm-form-grid-4">
                                                        <div class="tt-form-group">
                                                            <label>Type</label>
                                                            <select class="tt-form-control" name="type">
                                                                @foreach($types as $type)
                                                                    <option value="{{ $type }}" @selected($campaign->type === $type)>{{ strtoupper($type) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="tt-form-group adm-col-span-2">
                                                            <label>Titre</label>
                                                            <input class="tt-form-control" name="title" type="text" value="{{ $campaign->title }}">
                                                        </div>
                                                        <div class="tt-form-group">
                                                            <label>Statut</label>
                                                            <select class="tt-form-control" name="status">
                                                                @foreach($statuses as $status)
                                                                    <option value="{{ $status }}" @selected($campaign->status === $status)>{{ strtoupper($status) }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="tt-form-group">
                                                            <label>Debut</label>
                                                            <input class="tt-form-control" name="starts_at" type="datetime-local" value="{{ optional($campaign->starts_at)->format('Y-m-d\TH:i') }}">
                                                        </div>
                                                        <div class="tt-form-group">
                                                            <label>Fin</label>
                                                            <input class="tt-form-control" name="ends_at" type="datetime-local" value="{{ optional($campaign->ends_at)->format('Y-m-d\TH:i') }}">
                                                        </div>
                                                    </div>

                                                    <div class="adm-campaign-clip-pool">
                                                        @foreach($clips as $clip)
                                                            <label class="adm-campaign-clip-option">
                                                                <input type="checkbox" name="clip_ids[]" value="{{ $clip->id }}" @checked($campaign->entries->contains('clip_id', $clip->id))>
                                                                <img src="{{ $clip->thumbnail_url ?: '/template/assets/img/logo.png' }}" alt="{{ $clip->title }}">
                                                                <strong>{{ $clip->title }}</strong>
                                                            </label>
                                                        @endforeach
                                                    </div>

                                                    <div class="adm-row-actions">
                                                        <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Enregistrer">Enregistrer</span></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </details>
                                    </article>
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $campaigns->links() }}</div>
                        @else
                            <div class="adm-empty">Aucune campagne clips pour le moment.</div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
