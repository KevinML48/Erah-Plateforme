@extends('marketing.layouts.template')

@section('title', 'Statistiques | ERAH Plateforme')
@section('meta_description', 'Vue statistique communautaire ERAH: XP, rank, duel et activite globale.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.community.partials.styles')
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $duelLeaderboardRouteName = $isPublicApp ? 'app.duels.leaderboard' : 'duels.leaderboard';
        $duelsRouteName = $isPublicApp ? 'app.duels.index' : 'duels.index';
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Insights</h2>
                    <h1 class="ph-caption-title">Statistiques</h1>
                    <div class="ph-caption-description max-width-700">Vision globale de la progression XP, des ligues communautaires et du duel ranking.</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="community-head">
                    <div>
                        <h1>Vue d ensemble</h1>
                        <p>La lecture reste simple: un classement XP principal, votre ligue communautaire et un acces separe au classement duel.</p>
                    </div>
                    <div class="community-actions">
                        <a href="{{ route($duelLeaderboardRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item no-transition"><span data-hover="Classement duel">Classement duel</span></a>
                        <a href="{{ route($duelsRouteName) }}" class="tt-btn tt-btn-primary tt-magnetic-item no-transition"><span data-hover="Mes duels">Mes duels</span></a>
                    </div>
                </div>

                <div class="community-kpis">
                    <article class="community-kpi"><strong>{{ (int) $stats['users'] }}</strong><span>Profils suivis</span></article>
                    <article class="community-kpi"><strong>{{ (int) $stats['clips'] }}</strong><span>Clips publies</span></article>
                    <article class="community-kpi"><strong>{{ (int) $stats['bets_won'] }}</strong><span>Paris gagnes</span></article>
                    <article class="community-kpi"><strong>{{ (int) $stats['duels_settled'] }}</strong><span>Duels regles</span></article>
                </div>

                @if($communityRank)
                    <section class="community-surface">
                        <h3 class="no-margin">Mon rang communautaire</h3>
                        <div class="community-meta margin-top-20">
                            <span class="community-pill">{{ $communityRank['name'] }}</span>
                            <span class="community-pill">Seuil {{ (int) $communityRank['xp_threshold'] }} XP</span>
                            <span class="community-pill">{{ (int) ($progress->total_xp ?? 0) }} XP cumul</span>
                        </div>
                    </section>
                @endif

                <section class="community-surface">
                    <h3 class="no-margin">Leaderboard XP</h3>
                    <table class="community-table margin-top-20">
                        <thead><tr><th>#</th><th>Joueur</th><th>Rang commu</th><th>XP</th></tr></thead>
                        <tbody>
                            @foreach($xpLeaderboard as $entry)
                                <tr>
                                    <td>{{ $entry['position'] }}</td>
                                    <td>{{ $entry['name'] }}</td>
                                    <td>{{ $entry['community_rank'] }}</td>
                                    <td>{{ number_format((int) $entry['total_xp'], 0, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>

                <section class="community-surface">
                    <h3 class="no-margin">Performance duel</h3>
                    <p class="margin-top-20">Le classement duel dispose maintenant de sa propre page pour separer clairement la competition globale et vos duels personnels.</p>
                    <div class="community-actions margin-top-20">
                        <a href="{{ route($duelLeaderboardRouteName) }}" class="tt-btn tt-btn-primary tt-magnetic-item no-transition"><span data-hover="Ouvrir le classement duel">Ouvrir le classement duel</span></a>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('marketing.partials.theme-scripts')
@endsection
