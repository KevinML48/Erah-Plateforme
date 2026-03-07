@extends('marketing.layouts.template')

@section('title', 'Admin Matchs | ERAH Plateforme')
@section('meta_description', 'Gestion admin des matchs, statuts et settlements.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-match-list .tt-avlist-item {
            text-decoration: none;
        }

        .adm-match-list .tt-avlist-col-info {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .adm-match-list .tt-avlist-info {
            font-size: 12px;
            color: rgba(255, 255, 255, .76);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .adm-match-list .tt-avlist-item:hover .tt-btn-outline {
            border-color: rgba(18, 22, 30, .55);
            box-shadow: inset 0 0 0 2px rgba(18, 22, 30, .55);
        }

        .adm-match-list .tt-avlist-item:hover .tt-btn-outline > *,
        .adm-match-list .tt-avlist-item:hover .tt-btn-outline > *::after {
            color: rgba(18, 22, 30, .92);
        }

        .adm-match-list .tt-avlist-item:hover .tt-avlist-title,
        .adm-match-list .tt-avlist-item:hover .adm-pill {
            color: rgba(18, 22, 30, .92);
        }

        @media (max-width: 991.98px) {
            .adm-match-list .tt-avlist-col-info {
                justify-content: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $status = $status ?? 'all';
        $matches = $matches ?? collect();
        $statuses = $statuses ?? [];
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Admin Matchs',
        'heroDescription' => 'Creation, edition et supervision des matchs et de leur cycle de vie.',
        'heroMaskDescription' => 'Statuts, resultats, settlements.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Filtres matchs</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Affichez les matchs par statut et accedez a la gestion detaillee de chaque rencontre.</p>
                        </div>

                        <div class="adm-filter-actions">
                            <a href="{{ route('admin.matches.create') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Creer match">Creer match</span>
                            </a>

                            <a href="{{ route('admin.matches.index', ['status' => 'all']) }}" class="tt-btn {{ $status === 'all' ? 'tt-btn-secondary' : 'tt-btn-outline' }} tt-magnetic-item">
                                <span data-hover="Tous">Tous</span>
                            </a>

                            @foreach($statuses as $statusName)
                                <a href="{{ route('admin.matches.index', ['status' => $statusName]) }}" class="tt-btn {{ $status === $statusName ? 'tt-btn-secondary' : 'tt-btn-outline' }} tt-magnetic-item">
                                    <span data-hover="{{ ucfirst($statusName) }}">{{ ucfirst($statusName) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Liste des matchs</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Structure inspiree du template awards list pour un scan rapide.</p>
                        </div>

                        @if($matches->count())
                            <div class="tt-avards-list adm-match-list">
                                @foreach($matches as $match)
                                    @php
                                        $teamA = $match->team_a_name ?? $match->home_team ?? 'Team A';
                                        $teamB = $match->team_b_name ?? $match->away_team ?? 'Team B';
                                        $statusValue = (string) ($match->status ?? '-');
                                        $isLive = in_array($statusValue, ['live', 'locked'], true);
                                        $startAt = optional($match->starts_at)->format('d/m/Y H:i') ?: '-';
                                    @endphp
                                    <article class="tt-avlist-item tt-anim-fadeinup">
                                        <div class="tt-avlist-item-inner">
                                            <div class="tt-avlist-col tt-avlist-col-count">
                                                <div class="tt-avlist-count"></div>
                                            </div>

                                            <div class="tt-avlist-col tt-avlist-col-title">
                                                <h4 class="tt-avlist-title">{{ $teamA }} vs {{ $teamB }}</h4>
                                            </div>

                                            <div class="tt-avlist-col tt-avlist-col-description">
                                                <div class="tt-avlist-description">
                                                    <span class="adm-pill {{ $isLive ? 'adm-pill-live' : '' }}">{{ $statusValue }}</span>
                                                    <span class="adm-pill">Debut {{ $startAt }}</span>
                                                    <span class="adm-pill">Bets {{ (int) ($match->bets_count ?? 0) }}</span>
                                                    <span class="adm-pill">Resultat {{ $match->result ?: '-' }}</span>
                                                </div>
                                            </div>

                                            <div class="tt-avlist-col tt-avlist-col-info">
                                                <div class="adm-row-actions">
                                                    <a href="{{ route('admin.matches.edit', $match->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                        <span data-hover="Edit">Edit</span>
                                                    </a>
                                                    <a href="{{ route('admin.matches.manage', $match->id) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                        <span data-hover="Manage">Manage</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <div class="adm-pagin">{{ $matches->links() }}</div>
                        @else
                            <div class="adm-empty">Aucun match pour ce filtre.</div>
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
