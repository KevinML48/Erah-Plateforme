@extends('marketing.layouts.template')

@section('title', 'Succes | ERAH Plateforme')
@section('meta_description', 'Succes communautaires ERAH et progressions individuelles.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.community.partials.styles')
@endsection

@section('page_scripts')
    @include('marketing.partials.theme-scripts')
@endsection

@section('content')
    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Progression</h2>
                    <h1 class="ph-caption-title">Succes</h1>
                    <div class="ph-caption-description max-width-700">Caps clips, paris, duels et progression communautaire debloques au fil de votre activite.</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="community-head">
                    <div>
                        <h1>Mes succes</h1>
                        <p>Chaque succes debloque peut apporter des rewards supplementaires et sert aussi de vitrine sur votre profil communautaire.</p>
                    </div>
                </div>

                @if($achievements->count())
                    <section class="community-grid">
                        @foreach($achievements as $row)
                            @php($achievement = $row->achievement)
                            <article class="community-card">
                                <div class="community-meta">
                                    <span class="community-pill">{{ strtoupper((string) ($achievement->type ?? 'COMMUNAUTE')) }}</span>
                                    @if($achievement?->badge_label)
                                        <span class="community-pill">{{ $achievement->badge_label }}</span>
                                    @endif
                                </div>
                                <h3>{{ $achievement?->name ?? 'Succes' }}</h3>
                                <p class="no-margin">{{ $achievement?->description }}</p>
                                <div class="community-meta">
                                    <span class="community-pill">Progression {{ (int) $row->progress_value }} / {{ (int) ($achievement->threshold ?? 0) }}</span>
                                    <span class="community-pill">{{ $row->unlocked_at ? 'Debloque' : 'En cours' }}</span>
                                </div>
                                <div class="community-meta">
                                    <span class="community-pill">Debloque le {{ optional($row->unlocked_at)->format('d/m/Y H:i') ?: 'A venir' }}</span>
                                </div>
                            </article>
                        @endforeach
                    </section>
                    <div class="margin-top-40">{{ $achievements->links() }}</div>
                @else
                    <div class="community-empty">Aucun succes debloque pour le moment.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
