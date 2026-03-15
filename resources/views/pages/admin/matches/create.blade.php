@extends('marketing.layouts.template')

@section('title', 'Admin Matchs Legacy | ERAH Plateforme')
@section('meta_description', 'Point d'entrée legacy vers le formulaire principal des matchs admin.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Admin Matchs Legacy',
        'heroDescription' => 'Vue de compatibilite conservee pour anciens liens.',
        'heroMaskDescription' => 'Redirection vers le formulaire principal.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1200">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Vue legacy</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Cette page est maintenue pour compatibilite. Utilisez le flux principal pour créer et gérer les matchs.</p>
                        </div>

                        <div class="adm-legacy-actions">
                            <a href="{{ route('admin.matches.create') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Ouvrir le formulaire principal">Ouvrir le formulaire principal</span>
                            </a>

                            <a href="{{ route('admin.matches.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour liste admin matchs">Retour liste admin matchs</span>
                            </a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
