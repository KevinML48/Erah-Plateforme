@extends('marketing.layouts.template')

@section('title', ($match ? 'Edit Match | Admin ERAH' : 'Create Match | Admin ERAH'))
@section('meta_description', 'Formulaire admin pour creation et edition des matchs.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $match = $match ?? null;
        $action = $action ?? route('admin.matches.store');
        $method = $method ?? 'POST';
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => $match ? 'Modifier Match' : 'Nouveau Match',
        'heroDescription' => $match ? 'Edition des equipes et horaires du match.' : 'Creation d un match esport dans le calendrier plateforme.',
        'heroMaskDescription' => 'Template forms + actions admin dynamiques.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1400">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">{{ $match ? 'Match #'.$match->id : 'Creation match' }}</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Remplissez les equipes et la fenetre temporelle, puis gerez statut/resultat depuis la page manage.</p>
                        </div>

                        <form method="POST" action="{{ $action }}" class="tt-form tt-form-creative adm-form">
                            @csrf
                            @if($method !== 'POST')
                                @method($method)
                            @endif

                            <div class="adm-form-grid">
                                <div class="tt-form-group">
                                    <label for="team_a_name">Team A</label>
                                    <input class="tt-form-control" id="team_a_name" name="team_a_name" value="{{ old('team_a_name', $match->team_a_name ?? '') }}" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="team_b_name">Team B</label>
                                    <input class="tt-form-control" id="team_b_name" name="team_b_name" value="{{ old('team_b_name', $match->team_b_name ?? '') }}" required>
                                </div>
                            </div>

                            <div class="adm-form-grid">
                                <div class="tt-form-group">
                                    <label for="starts_at">Starts at</label>
                                    <input class="tt-form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', isset($match->starts_at) ? $match->starts_at->format('Y-m-d\\TH:i') : '') }}" required>
                                </div>

                                <div class="tt-form-group">
                                    <label for="locked_at">Locked at (optionnel)</label>
                                    <input class="tt-form-control" id="locked_at" name="locked_at" type="datetime-local" value="{{ old('locked_at', isset($match->locked_at) ? $match->locked_at->format('Y-m-d\\TH:i') : '') }}">
                                </div>
                            </div>

                            <div class="tt-form-group">
                                <label for="game_key">Game key</label>
                                <input class="tt-form-control" id="game_key" name="game_key" value="{{ old('game_key', $match->game_key ?? '') }}" placeholder="valorant, cs2, ...">
                            </div>

                            <div class="adm-row-actions">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Enregistrer">Enregistrer</span>
                                </button>

                                <a href="{{ route('admin.matches.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Retour liste">Retour liste</span>
                                </a>

                                @if($match)
                                    <a href="{{ route('admin.matches.manage', $match->id) }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                        <span data-hover="Manage">Manage</span>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
