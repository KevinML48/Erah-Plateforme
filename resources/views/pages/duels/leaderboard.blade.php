@extends('marketing.layouts.template')

@section('title', 'Classement duel | ERAH Plateforme')
@section('meta_description', 'Classement duel ERAH avec score duel, serie en cours, meilleure serie et ratio victoire / defaite.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.community.partials.styles')
    <style>
        .community-player-link {
            color: inherit;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: color .2s ease, border-color .2s ease, opacity .2s ease;
        }

        .community-player-link:hover,
        .community-player-link:focus-visible {
            color: #ffffff;
            border-color: rgba(255, 255, 255, .42);
            opacity: 1;
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $duelsRouteName = $isPublicApp ? 'app.duels.index' : 'duels.index';
        $statisticsRouteName = $isPublicApp ? 'app.statistics.index' : 'statistics.index';
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Matchups</h2>
                    <h1 class="ph-caption-title">Classement duel</h1>
                    <div class="ph-caption-description max-width-700">Le classement duel est separe de vos duels en cours. Il met en avant la performance competitive sur la duree.</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="community-head">
                    <div>
                        <h1>Top duel ERAH</h1>
                        <p>Ordre de classement: victoires, serie en cours, ratio victoire / defaite, puis score duel.</p>
                    </div>
                    <div class="community-actions">
                        <a href="{{ route($duelsRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Mes duels">Mes duels</span></a>
                        <a href="{{ route($statisticsRouteName) }}" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Statistiques">Statistiques</span></a>
                    </div>
                </div>

                <div class="community-kpis">
                    <article class="community-kpi"><strong>{{ (int) $stats['players_ranked'] }}</strong><span>Joueurs classes</span></article>
                    <article class="community-kpi"><strong>{{ (int) $stats['duels_settled'] }}</strong><span>Duels regles</span></article>
                    <article class="community-kpi"><strong>{{ number_format((int) $stats['best_score'], 0, ',', ' ') }}</strong><span>Meilleur score duel</span></article>
                    <article class="community-kpi"><strong>{{ (int) $stats['best_streak'] }}</strong><span>Meilleure serie</span></article>
                </div>

                @if($progress)
                    <section class="community-surface">
                        <h3 class="no-margin">Mon bilan duel</h3>
                        <div class="community-meta margin-top-20">
                            <span class="community-pill">Score duel {{ number_format((int) $progress->duel_score, 0, ',', ' ') }}</span>
                            <span class="community-pill">{{ (int) $progress->duel_wins }} victoire(s)</span>
                            <span class="community-pill">{{ (int) $progress->duel_losses }} defaite(s)</span>
                            <span class="community-pill">Serie en cours {{ (int) $progress->duel_current_streak }}</span>
                            <span class="community-pill">Meilleure serie {{ (int) $progress->duel_best_streak }}</span>
                        </div>
                    </section>
                @endif

                <section class="community-surface">
                    <h3 class="no-margin">Classement complet</h3>
                    @if($duelLeaderboard->isNotEmpty())
                        <table class="community-table margin-top-20">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Joueur</th>
                                    <th>Score duel</th>
                                    <th>Serie</th>
                                    <th>Bilan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($duelLeaderboard as $entry)
                                    @php($playerProfileUrl = filled($entry['user_id']) ? route('users.public', (int) $entry['user_id']) : null)
                                    <tr>
                                        <td>{{ (int) $entry['position'] }}</td>
                                        <td>
                                            @if($playerProfileUrl)
                                                <a href="{{ $playerProfileUrl }}" class="community-player-link" aria-label="Voir le profil public de {{ $entry['name'] }}">
                                                    {{ $entry['name'] }}
                                                </a>
                                            @else
                                                {{ $entry['name'] }}
                                            @endif
                                        </td>
                                        <td>{{ number_format((int) $entry['duel_score'], 0, ',', ' ') }}</td>
                                        <td>{{ (int) $entry['duel_current_streak'] }} en cours / {{ (int) $entry['duel_best_streak'] }} max</td>
                                        <td>{{ (int) $entry['duel_wins'] }} V / {{ (int) $entry['duel_losses'] }} D (ratio {{ number_format((float) $entry['duel_ratio'], 2, ',', ' ') }})</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="community-empty margin-top-20">Aucun duel classe pour le moment.</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('marketing.partials.theme-scripts')
@endsection
