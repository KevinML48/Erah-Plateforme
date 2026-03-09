@extends('marketing.layouts.template')

@section('title', 'Statistiques | ERAH Plateforme')
@section('meta_description', 'Vue statistique communautaire ERAH: XP, rank, duel et activite globale.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.community.partials.styles')
@endsection

@section('content')
    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Insights</h2>
                    <h1 class="ph-caption-title">Statistiques</h1>
                    <div class="ph-caption-description max-width-700">Vision globale des performances XP, du classement competitif et du score duel.</div>
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
                        <p>Trois classements coexistent: experience communautaire, classement competitif et score duel. Le module consolide aussi le volume clips, paris et duels termines.</p>
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
                    <h3 class="no-margin">Leaderboard classement</h3>
                    <table class="community-table margin-top-20">
                        <thead><tr><th>#</th><th>Joueur</th><th>Ligue</th><th>Points classement</th></tr></thead>
                        <tbody>
                            @foreach($rankLeaderboard as $entry)
                                <tr>
                                    <td>{{ $entry['position'] }}</td>
                                    <td>{{ $entry['name'] }}</td>
                                    <td>{{ $entry['league'] ?: 'N/A' }}</td>
                                    <td>{{ number_format((int) $entry['total_rank_points'], 0, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>

                <section class="community-surface">
                    <h3 class="no-margin">Leaderboard duel</h3>
                    <table class="community-table margin-top-20">
                        <thead><tr><th>#</th><th>Joueur</th><th>Score duel</th><th>Bilan</th></tr></thead>
                        <tbody>
                            @foreach($duelLeaderboard as $entry)
                                <tr>
                                    <td>{{ $entry['position'] }}</td>
                                    <td>{{ $entry['name'] }}</td>
                                    <td>{{ number_format((int) $entry['duel_score'], 0, ',', ' ') }}</td>
                                    <td>{{ (int) $entry['duel_wins'] }} V / {{ (int) $entry['duel_losses'] }} D</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </div>
@endsection
