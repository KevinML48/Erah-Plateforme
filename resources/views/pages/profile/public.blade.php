@extends('marketing.layouts.template')

@section('title', ($userProfile->name ?? 'Profil public').' | ERAH')
@section('meta_description', 'Profil public membre ERAH, progression, activite recente et avis publie.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.reviews.partials.styles')
    <style>
        .public-profile-side-card,
        .public-profile-surface,
        .public-profile-note-card {
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 24px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .018)),
                rgba(255, 255, 255, .02);
            backdrop-filter: blur(6px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, .2);
        }

        .public-profile-panel-head {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 16px;
            align-items: center;
        }

        .public-profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .16);
            object-fit: cover;
            background: rgba(255, 255, 255, .06);
        }

        .public-profile-avatar--compact {
            width: 84px;
            height: 84px;
        }

        .public-profile-eyebrow {
            display: inline-block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, .62);
            font-size: 12px;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .public-profile-panel-head h2,
        .public-profile-name {
            margin: 0;
            color: #fff;
            line-height: .94;
        }

        .public-profile-panel-head h2 {
            font-size: clamp(28px, 3vw, 42px);
        }

        .public-profile-panel-head p,
        .public-profile-subtitle {
            margin: 8px 0 0;
            color: rgba(255, 255, 255, .68);
            line-height: 1.5;
        }

        .public-profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .public-profile-pill,
        .public-profile-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 32px;
            padding: 6px 12px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .04);
            color: rgba(255, 255, 255, .82);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            line-height: 1;
        }

        .public-profile-pill.is-supporter,
        .public-profile-badge.is-supporter {
            border-color: rgba(255, 87, 87, .45);
            background: rgba(225, 11, 11, .14);
            color: #ffd2d2;
        }

        .public-profile-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 8px;
        }

        .public-profile-actions .tt-btn,
        .public-profile-admin-toolbar .tt-btn {
            margin: 0;
            white-space: nowrap;
        }

        .public-profile-highlight-grid,
        .public-profile-kpi-grid,
        .public-profile-summary-grid,
        .public-profile-presence-grid {
            display: grid;
            gap: 12px;
            align-items: start;
        }

        .public-profile-highlight-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .public-profile-highlight-card,
        .public-profile-kpi-card,
        .public-profile-summary-card,
        .public-profile-presence-card {
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 18px;
            padding: 16px 18px;
            background: rgba(255, 255, 255, .03);
            min-height: 0;
        }

        .public-profile-highlight-card span,
        .public-profile-kpi-card span,
        .public-profile-summary-card span,
        .public-profile-presence-card span {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, .62);
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .public-profile-highlight-card strong,
        .public-profile-kpi-card strong,
        .public-profile-summary-card strong,
        .public-profile-presence-card strong {
            display: block;
            color: #fff;
            line-height: 1;
        }

        .public-profile-highlight-card strong {
            font-size: clamp(24px, 2vw, 34px);
        }

        .public-profile-shell {
            display: grid;
            gap: 24px;
        }

        .public-profile-page-start {
            padding-top: 138px;
        }

        .public-profile-top-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(340px, .92fr);
            gap: 24px;
        }

        .public-profile-identity-card,
        .public-profile-quick-card {
            padding: 28px;
            display: grid;
            gap: 22px;
        }

        .public-profile-identity-card {
            position: relative;
            overflow: hidden;
        }

        .public-profile-identity-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(520px 220px at 10% 0, rgba(225, 11, 11, .15), transparent 62%),
                repeating-linear-gradient(
                    90deg,
                    rgba(255, 255, 255, .008) 0,
                    rgba(255, 255, 255, .008) 1px,
                    transparent 1px,
                    transparent 92px
                );
            pointer-events: none;
        }

        .public-profile-identity-card > * {
            position: relative;
            z-index: 1;
        }

        .public-profile-identity-copy {
            display: grid;
            gap: 12px;
        }

        .public-profile-intro-copy {
            max-width: 780px;
            color: rgba(255, 255, 255, .74);
            line-height: 1.65;
        }

        .public-profile-quick-card {
            align-content: start;
        }

        .public-profile-top-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .public-profile-top-badges .public-profile-pill {
            min-height: 28px;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: .09em;
        }

        .public-profile-name {
            font-size: clamp(34px, 4vw, 54px);
        }

        .public-profile-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .public-profile-kpi-card strong {
            font-size: 30px;
        }

        .public-profile-kpi-card,
        .public-profile-highlight-card,
        .public-profile-summary-card,
        .public-profile-presence-card {
            display: grid;
            align-content: start;
            gap: 4px;
        }

        .public-profile-side-socials ul {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .public-profile-side-socials a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .03);
            color: #fff;
        }

        .public-profile-main {
            display: grid;
            gap: 24px;
        }

        .public-profile-surface {
            padding: 28px;
        }

        .public-profile-heading.tt-heading {
            margin: 0 0 24px;
        }

        .public-profile-heading .tt-heading-title {
            margin-bottom: 0;
            font-size: clamp(30px, 3.5vw, 48px);
        }

        .public-profile-heading .tt-heading-subtitle {
            margin-bottom: 14px;
        }

        .public-profile-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .public-profile-summary-card strong {
            font-size: 28px;
        }

        .public-profile-presence-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 14px;
        }

        .public-profile-presence-card {
            display: grid;
            gap: 8px;
        }

        .public-profile-presence-card p {
            margin: 0;
            color: rgba(255, 255, 255, .72);
            line-height: 1.55;
        }

        .public-profile-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .public-profile-review-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
            gap: 18px;
        }

        .public-profile-review-surface {
            overflow: hidden;
        }

        .public-profile-review-surface .review-card {
            height: 100%;
            border-radius: 24px;
        }

        .public-profile-note-card {
            padding: 22px;
            display: grid;
            gap: 14px;
        }

        .public-profile-note-card h3 {
            margin: 0;
            color: #fff;
            font-size: 28px;
            line-height: 1;
        }

        .public-profile-note-card p,
        .public-profile-note-card ul {
            margin: 0;
            color: rgba(255, 255, 255, .72);
            line-height: 1.55;
        }

        .public-profile-note-card ul {
            padding-left: 18px;
            display: grid;
            gap: 8px;
        }

        .public-profile-empty {
            border: 1px dashed rgba(255, 255, 255, .18);
            border-radius: 18px;
            padding: 24px;
            color: rgba(255, 255, 255, .68);
            text-align: center;
        }

        .public-profile-admin-surface {
            border-color: rgba(255, 87, 87, .18);
            background:
                radial-gradient(560px 200px at 8% 0, rgba(225, 11, 11, .12), transparent 58%),
                linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .02)),
                rgba(255, 255, 255, .02);
        }

        .public-profile-admin-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(300px, .8fr);
            gap: 18px;
        }

        .public-profile-admin-form,
        .public-profile-admin-card {
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 20px;
            padding: 22px;
            background: rgba(255, 255, 255, .03);
        }

        .public-profile-admin-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .public-profile-admin-fields {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .public-profile-admin-fields .tt-form-group--full {
            grid-column: 1 / -1;
        }

        .public-profile-admin-checks {
            display: grid;
            gap: 12px;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        .public-profile-admin-card {
            display: grid;
            gap: 14px;
        }

        .public-profile-admin-card h3 {
            margin: 0;
            color: #fff;
            font-size: 30px;
            line-height: 1;
        }

        .public-profile-admin-card p {
            margin: 0;
            color: rgba(255, 255, 255, .72);
            line-height: 1.55;
        }

        .public-profile-admin-facts {
            display: grid;
            gap: 10px;
        }

        .public-profile-admin-fact {
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, .02);
        }

        .public-profile-admin-fact span {
            display: block;
            color: rgba(255, 255, 255, .58);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .public-profile-admin-fact strong {
            display: block;
            color: #fff;
            font-size: 18px;
            line-height: 1.2;
        }

        .public-profile-admin-warning {
            border-color: rgba(255, 87, 87, .24);
            background: rgba(225, 11, 11, .1);
        }

        .public-profile-admin-danger-form {
            display: grid;
            gap: 12px;
        }

        .public-profile-activity-list .tt-avlist-item {
            border-color: rgba(255, 255, 255, .08);
        }

        .public-profile-activity-meta {
            margin-top: 8px;
            color: rgba(255, 255, 255, .54);
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .public-profile-activity-points {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 88px;
            min-height: 42px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .05);
            color: #fff;
            font-size: 16px;
            line-height: 1;
        }

        .public-profile-activity-points.is-rank {
            border-color: rgba(255, 87, 87, .35);
            color: #ffd7d7;
            background: rgba(225, 11, 11, .12);
        }

        body:not(.is-mobile) .public-profile-activity-list .tt-avlist-item:hover .public-profile-activity-meta,
        body:not(.is-mobile) .public-profile-activity-list .tt-avlist-item:focus .public-profile-activity-meta {
            color: rgba(255, 255, 255, .74);
        }

        @media (max-width: 1199.98px) {
            .public-profile-top-grid,
            .public-profile-review-grid,
            .public-profile-admin-grid {
                grid-template-columns: 1fr;
            }

            .public-profile-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .public-profile-page-start {
                padding-top: 116px;
            }
        }

        @media (max-width: 767.98px) {
            .public-profile-highlight-grid,
            .public-profile-kpi-grid,
            .public-profile-summary-grid,
            .public-profile-presence-grid,
            .public-profile-admin-fields {
                grid-template-columns: 1fr;
            }

            .public-profile-panel-head {
                grid-template-columns: 1fr;
            }

            .public-profile-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .public-profile-actions .tt-btn,
            .public-profile-admin-toolbar .tt-btn {
                width: 100%;
                white-space: normal;
                text-align: center;
            }

            .public-profile-page-start {
                padding-top: 98px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $avatarUrl = $userProfile->avatar_url ?: '/template/assets/img/logo.png';
        $supporterBadges = collect([
            ($supporterSummary['is_founder'] ?? false) ? 'Supporter fondateur' : null,
            $supporterSummary['loyalty_badge'] ?? null,
        ])->filter()->values();
        $socialLinks = collect([
            ['url' => $userProfile->twitter_url, 'icon' => 'fa-brands fa-x-twitter', 'label' => 'Twitter / X'],
            ['url' => $userProfile->instagram_url, 'icon' => 'fa-brands fa-instagram', 'label' => 'Instagram'],
            ['url' => $userProfile->tiktok_url, 'icon' => 'fa-brands fa-tiktok', 'label' => 'TikTok'],
            ['url' => $userProfile->discord_url, 'icon' => 'fa-brands fa-discord', 'label' => 'Discord'],
        ])->filter(fn (array $item) => filled($item['url']))->values();

        $supporterStatusLabel = match (true) {
            ($supporterSummary['is_active'] ?? false) => 'Supporter actif',
            ((int) ($supporterSummary['months'] ?? 0)) > 0 => 'Ancien supporter',
            default => 'Membre standard',
        };

        $supporterStatusMeta = collect([
            $supporterSummary['current_plan_name'] ?? null,
            !empty($supporterSummary['ends_at']) ? 'Jusqu au '.optional($supporterSummary['ends_at'])->format('d/m/Y') : null,
        ])->filter()->implode(' - ');
        $reviewStatuses = \App\Models\ClubReview::statusLabels();
        $moderationReview = $moderationReview ?? null;
        $canDeleteAccountFromModeration = ($canModerateProfile ?? false) && (($viewer?->id ?? null) !== $userProfile->id);
        $adminUsersConsoleUrl = route('users.index', ['user_id' => $userProfile->id]);
        $adminReviewsConsoleUrl = route('admin.reviews.index', ['q' => $userProfile->name]);
        $linksCount = $socialLinks->count();
        $quickProfileCards = collect([
            ['label' => 'Ligue actuelle', 'value' => $progress?->league?->name ?: 'Non classe'],
            ['label' => 'Classement global', 'value' => $rankPosition ? '#'.$rankPosition : '-'],
            ['label' => 'Points classement', 'value' => number_format((int) ($progress->total_rank_points ?? 0), 0, ',', ' ')],
            ['label' => 'XP total', 'value' => number_format((int) ($progress->total_xp ?? 0), 0, ',', ' ')],
            ['label' => 'Avis membre', 'value' => $moderationReview ? ($reviewStatuses[$moderationReview->status] ?? ucfirst((string) $moderationReview->status)) : 'Aucun'],
            ['label' => 'Liens publics', 'value' => $linksCount.' lien(s)'],
        ]);

        $heroHighlights = collect([
            ['label' => 'Ligue', 'value' => $progress?->league?->name ?: 'Non classe'],
            ['label' => 'Classement global', 'value' => $rankPosition ? '#'.$rankPosition : '-'],
            ['label' => 'Points classement', 'value' => number_format((int) ($progress->total_rank_points ?? 0), 0, ',', ' ')],
            ['label' => 'XP total', 'value' => number_format((int) ($progress->total_xp ?? 0), 0, ',', ' ')],
        ]);

        $overviewCards = collect([
            ['label' => 'Likes clips', 'value' => (int) ($stats['likes'] ?? 0)],
            ['label' => 'Commentaires', 'value' => (int) ($stats['comments'] ?? 0)],
            ['label' => 'Duels', 'value' => (int) ($stats['duels'] ?? 0)],
            ['label' => 'Pronostics', 'value' => (int) ($stats['bets'] ?? 0)],
        ]);

        $presenceCards = collect([
            [
                'label' => 'Statut membre',
                'value' => $supporterStatusLabel,
                'body' => $supporterStatusMeta ?: 'Profil public visible sur la plateforme.',
            ],
            [
                'label' => 'Bio',
                'value' => filled($userProfile->bio) ? 'Presentation publique' : 'Bio non renseignee',
                'body' => $userProfile->bio ?: 'Le membre n a pas encore ajoute de description detaillee.',
            ],
            [
                'label' => 'Progression',
                'value' => $progress?->league?->name ? 'Ligue '.$progress->league->name : 'Classement en construction',
                'body' => 'Position globale '.($rankPosition ? '#'.$rankPosition : 'non disponible').' - '.number_format((int) ($progress->total_rank_points ?? 0), 0, ',', ' ').' points classement.',
            ],
            [
                'label' => 'Signaux communautaires',
                'value' => $supporterBadges->isNotEmpty() ? 'Badges disponibles' : 'Profil simple',
                'body' => $supporterBadges->isNotEmpty() ? $supporterBadges->implode(' - ') : 'Aucun badge particulier a afficher pour le moment.',
            ],
        ]);

        $reviewCard = $publishedReview
            ? [
                'author_name' => $userProfile->name,
                'author_url' => null,
                'author_cta' => null,
                'avatar_url' => $avatarUrl,
                'initials' => strtoupper(substr((string) $userProfile->name, 0, 2)),
                'content' => $publishedReview->content,
                'published_at' => $publishedReview->published_at,
                'is_member' => true,
                'supporter_label' => ($supporterSummary['is_active'] ?? false) ? 'Supporter actif' : null,
                'meta' => collect([
                    $progress?->league?->name ? 'Ligue '.$progress->league->name : null,
                    $rankPosition ? 'Classement #'.$rankPosition : null,
                    ((int) ($progress->total_rank_points ?? 0)) > 0 ? number_format((int) ($progress->total_rank_points ?? 0), 0, ',', ' ').' points classement' : null,
                ])->filter()->values()->all(),
                'badges' => $supporterBadges->all(),
                'source_label' => 'Avis membre',
            ]
            : null;

        $txKindLabels = [
            \App\Models\PointsTransaction::KIND_XP => 'XP',
            \App\Models\PointsTransaction::KIND_RANK => 'Classement',
        ];

        $activityFeed = collect($recentTransactions ?? [])
            ->map(function ($tx) use ($txKindLabels): array {
                $kindLabel = $txKindLabels[$tx->kind] ?? strtoupper((string) $tx->kind);
                $beforeValue = (int) ($tx->kind === \App\Models\PointsTransaction::KIND_RANK ? $tx->before_rank_points : $tx->before_xp);
                $afterValue = (int) ($tx->kind === \App\Models\PointsTransaction::KIND_RANK ? $tx->after_rank_points : $tx->after_xp);

                $sourceLabel = match (true) {
                    str_starts_with((string) $tx->source_type, 'mission.') => 'Mission completee',
                    str_starts_with((string) $tx->source_type, 'duel.') => 'Interaction duel',
                    str_starts_with((string) $tx->source_type, 'bet.') => 'Prediction esport',
                    str_starts_with((string) $tx->source_type, 'clip.') => 'Activite clips',
                    str_starts_with((string) $tx->source_type, 'supporter.') => 'Programme supporter',
                    str_starts_with((string) $tx->source_type, 'admin.') => 'Action admin',
                    default => 'Activite plateforme',
                };

                return [
                    'title' => $sourceLabel,
                    'description' => $kindLabel.' - Avant '.$beforeValue.' - Apres '.$afterValue,
                    'meta' => optional($tx->created_at)->format('d/m/Y H:i'),
                    'points' => (((int) $tx->points) >= 0 ? '+' : '').(int) $tx->points,
                    'is_rank' => $tx->kind === \App\Models\PointsTransaction::KIND_RANK,
                ];
            })
            ->values();
    @endphp

    <div id="tt-page-content">
        <div class="tt-section public-profile-page-start padding-bottom-xlg-120 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="public-profile-shell">
                    <section class="public-profile-top-grid">
                        <div class="public-profile-surface public-profile-identity-card">
                            <div class="public-profile-panel-head">
                                <img src="{{ $avatarUrl }}" alt="Avatar {{ $userProfile->name }}" class="public-profile-avatar">

                                <div class="public-profile-identity-copy">
                                    <div>
                                        <span class="public-profile-eyebrow">Profil public membre</span>
                                        <h1 class="public-profile-name">{{ $userProfile->name }}</h1>
                                        <p class="public-profile-subtitle">
                                            {{ $progress?->league?->name ? 'Ligue '.$progress->league->name.' - ' : '' }}
                                            {{ $supporterStatusLabel }}
                                        </p>
                                    </div>

                                    <p class="public-profile-intro-copy">
                                        {{ $userProfile->bio ?: 'Profil public d un membre ERAH avec progression, activite recente et signaux communautaires visibles en un coup d oeil.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="public-profile-top-badges">
                                <span class="public-profile-pill">{{ number_format((int) ($progress->total_rank_points ?? 0), 0, ',', ' ') }} points classement</span>
                                @if($rankPosition)
                                    <span class="public-profile-pill">Classement global #{{ $rankPosition }}</span>
                                @endif
                                <span class="public-profile-pill {{ ($supporterSummary['is_active'] ?? false) ? 'is-supporter' : '' }}">
                                    {{ $supporterStatusLabel }}
                                </span>
                                @if($canModerateProfile ?? false)
                                    <span class="public-profile-pill is-supporter">Mode moderation admin</span>
                                @endif
                            </div>

                            @if($supporterBadges->isNotEmpty())
                                <div class="public-profile-badges">
                                    @foreach($supporterBadges as $badge)
                                        <span class="public-profile-badge {{ str_contains(strtolower($badge), 'supporter') ? 'is-supporter' : '' }}">{{ $badge }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if($socialLinks->isNotEmpty())
                                <div class="public-profile-side-socials">
                                    <div class="tt-social-buttons">
                                        <ul>
                                            @foreach($socialLinks as $social)
                                                <li>
                                                    <a href="{{ $social['url'] }}" class="tt-magnetic-item" target="_blank" rel="noopener" title="{{ $social['label'] }}">
                                                        <i class="{{ $social['icon'] }}"></i>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <div class="public-profile-actions">
                                @if(($viewer?->id ?? null) === $userProfile->id)
                                    <a href="{{ route('profile.show') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Modifier mon profil">Modifier mon profil</span>
                                    </a>
                                @else
                                    <a href="{{ route('reviews.index') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Voir tous les avis">Voir tous les avis</span>
                                    </a>
                                @endif

                                @foreach($socialLinks->take(2) as $social)
                                    <a href="{{ $social['url'] }}" class="tt-btn tt-btn-outline tt-magnetic-item" target="_blank" rel="noopener">
                                        <span data-hover="{{ $social['label'] }}">{{ $social['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <aside class="public-profile-surface public-profile-quick-card">
                            <div class="tt-heading tt-heading-sm public-profile-heading">
                                <h3 class="tt-heading-subtitle">Reperes rapides</h3>
                                <h2 class="tt-heading-title">Lecture immediate</h2>
                                <p class="max-width-700 text-muted">
                                    Une synthese plus dense pour eviter le vide et rendre les informations utiles visibles des l entree sur la page.
                                </p>
                            </div>

                            <div class="public-profile-highlight-grid">
                                @foreach($quickProfileCards as $card)
                                    <article class="public-profile-highlight-card">
                                        <span>{{ $card['label'] }}</span>
                                        <strong>{{ $card['value'] }}</strong>
                                    </article>
                                @endforeach
                            </div>
                        </aside>
                    </section>

                    <div class="public-profile-main">
                        @if($canModerateProfile ?? false)
                            <section class="public-profile-surface public-profile-admin-surface">
                                <div class="tt-heading tt-heading-sm public-profile-heading">
                                    <h3 class="tt-heading-subtitle">Moderation admin</h3>
                                    <h2 class="tt-heading-title">Controle du profil</h2>
                                    <p class="max-width-900 text-muted">
                                        Corrigez rapidement le pseudo, la bio, les liens publics, la photo de profil et l avis membre sans quitter cette page.
                                    </p>
                                </div>

                                <div class="public-profile-admin-grid">
                                    <form method="POST" action="{{ route('admin.users.public-profile.update', $userProfile) }}" class="tt-form tt-form-creative public-profile-admin-form">
                                        @csrf
                                        @method('PUT')

                                        <div class="public-profile-admin-toolbar">
                                            <span class="public-profile-pill is-supporter">Moderation en direct</span>
                                            <a href="{{ $adminUsersConsoleUrl }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                <span data-hover="Console users">Console users</span>
                                            </a>
                                            @if($reviewsModuleReady ?? false)
                                                <a href="{{ $adminReviewsConsoleUrl }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                    <span data-hover="Console avis">Console avis</span>
                                                </a>
                                            @endif
                                        </div>

                                        <div class="public-profile-admin-fields">
                                            <div class="tt-form-group">
                                                <label for="admin_name">Pseudo public</label>
                                                <input id="admin_name" name="name" class="tt-form-control" value="{{ old('name', $userProfile->name) }}" maxlength="120">
                                            </div>

                                            <div class="tt-form-group">
                                                <label for="admin_review_status">Etat de l avis</label>
                                                <select id="admin_review_status" name="review_status" class="tt-form-control" data-lenis-prevent>
                                                    <option value="">Ne pas changer</option>
                                                    @foreach($reviewStatuses as $statusValue => $statusLabel)
                                                        <option value="{{ $statusValue }}" @selected(old('review_status', $moderationReview?->status) === $statusValue)>{{ $statusLabel }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="tt-form-group tt-form-group--full">
                                                <label for="admin_bio">Bio publique</label>
                                                <textarea id="admin_bio" name="bio" class="tt-form-control" rows="5" maxlength="1500">{{ old('bio', $userProfile->bio) }}</textarea>
                                            </div>

                                            <div class="tt-form-group">
                                                <label for="admin_twitter_url">Lien Twitter / X</label>
                                                <input id="admin_twitter_url" name="twitter_url" class="tt-form-control" value="{{ old('twitter_url', $userProfile->twitter_url) }}" placeholder="https://x.com/...">
                                            </div>

                                            <div class="tt-form-group">
                                                <label for="admin_instagram_url">Lien Instagram</label>
                                                <input id="admin_instagram_url" name="instagram_url" class="tt-form-control" value="{{ old('instagram_url', $userProfile->instagram_url) }}" placeholder="https://instagram.com/...">
                                            </div>

                                            <div class="tt-form-group">
                                                <label for="admin_tiktok_url">Lien TikTok</label>
                                                <input id="admin_tiktok_url" name="tiktok_url" class="tt-form-control" value="{{ old('tiktok_url', $userProfile->tiktok_url) }}" placeholder="https://tiktok.com/...">
                                            </div>

                                            <div class="tt-form-group">
                                                <label for="admin_discord_url">Lien Discord</label>
                                                <input id="admin_discord_url" name="discord_url" class="tt-form-control" value="{{ old('discord_url', $userProfile->discord_url) }}" placeholder="https://discord.gg/...">
                                            </div>
                                        </div>

                                        <div class="public-profile-admin-checks">
                                            <label class="tt-form-check">
                                                <input type="hidden" name="remove_avatar" value="0">
                                                <input type="checkbox" name="remove_avatar" value="1" @checked(old('remove_avatar'))>
                                                <span>Retirer la photo de profil si elle est douteuse.</span>
                                            </label>

                                            <label class="tt-form-check">
                                                <input type="hidden" name="clear_social_links" value="0">
                                                <input type="checkbox" name="clear_social_links" value="1" @checked(old('clear_social_links'))>
                                                <span>Vider tous les liens publics si les URLs sont douteuses.</span>
                                            </label>

                                            @if(($reviewsModuleReady ?? false) && $moderationReview)
                                                <label class="tt-form-check">
                                                    <input type="hidden" name="delete_review" value="0">
                                                    <input type="checkbox" name="delete_review" value="1" @checked(old('delete_review'))>
                                                    <span>Supprimer completement l avis membre actuel.</span>
                                                </label>
                                            @endif
                                        </div>

                                        <div class="public-profile-actions" style="margin-top:18px;">
                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Enregistrer la moderation">Enregistrer la moderation</span>
                                            </button>
                                        </div>
                                    </form>

                                    <aside class="public-profile-admin-card">
                                        <span class="public-profile-eyebrow">Resume moderation</span>
                                        <h3>Actions sensibles</h3>
                                        <p>Ce panneau aide a verifier rapidement les points les plus sensibles du profil avant intervention.</p>

                                        <div class="public-profile-admin-facts">
                                            <div class="public-profile-admin-fact">
                                                <span>Email du compte</span>
                                                <strong>{{ $userProfile->email }}</strong>
                                            </div>
                                            <div class="public-profile-admin-fact">
                                                <span>Photo publique</span>
                                                <strong>{{ $userProfile->avatar_url ? 'Photo presente' : 'Aucune photo' }}</strong>
                                            </div>
                                            <div class="public-profile-admin-fact">
                                                <span>Liens publics</span>
                                                <strong>{{ $linksCount }} lien(s) actif(s)</strong>
                                            </div>
                                            <div class="public-profile-admin-fact">
                                                <span>Avis membre</span>
                                                <strong>
                                                    @if($moderationReview)
                                                        {{ $reviewStatuses[$moderationReview->status] ?? ucfirst((string) $moderationReview->status) }}
                                                    @else
                                                        Aucun avis lie
                                                    @endif
                                                </strong>
                                            </div>
                                        </div>

                                        @if($moderationReview)
                                            <div class="public-profile-admin-fact">
                                                <span>Contenu de l avis</span>
                                                <strong>{{ \Illuminate\Support\Str::limit($moderationReview->content, 140) }}</strong>
                                            </div>
                                        @endif

                                        <div class="public-profile-admin-card public-profile-admin-warning">
                                            <span class="public-profile-eyebrow">Suppression du compte</span>
                                            <h3>Action irreversible</h3>
                                            <p>Le compte, son avis membre et ses donnees associees seront supprimes. Les contenus crees sont reassignes a l admin pour garder la coherence technique.</p>

                                            @if($canDeleteAccountFromModeration)
                                                <form method="POST" action="{{ route('admin.users.public-profile.destroy', $userProfile) }}" class="public-profile-admin-danger-form" onsubmit="return confirm('Supprimer definitivement ce compte ?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <div class="tt-form-group">
                                                        <label for="confirmation_name">Confirmez avec le pseudo exact</label>
                                                        <input id="confirmation_name" name="confirmation_name" class="tt-form-control" value="{{ old('confirmation_name') }}" placeholder="{{ $userProfile->name }}">
                                                    </div>

                                                    <button type="submit" class="tt-btn tt-btn-primary tt-btn-full tt-magnetic-item">
                                                        <span data-hover="Supprimer le compte">Supprimer le compte</span>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="public-profile-empty">La suppression de votre propre compte admin n est pas autorisee depuis cette page.</div>
                                            @endif
                                        </div>
                                    </aside>
                                </div>
                            </section>
                        @endif

                        <section class="public-profile-surface">
                            <div class="tt-heading tt-heading-sm public-profile-heading">
                                <h3 class="tt-heading-subtitle">Apercu membre</h3>
                                <h2 class="tt-heading-title">Presence sur la plateforme</h2>
                                <p class="max-width-800 text-muted">
                                    Une lecture rapide du profil, de sa progression et de ses signaux communautaires.
                                </p>
                            </div>

                            <div class="public-profile-summary-grid">
                                @foreach($overviewCards as $card)
                                    <article class="public-profile-summary-card">
                                        <span>{{ $card['label'] }}</span>
                                        <strong>{{ $card['value'] }}</strong>
                                    </article>
                                @endforeach
                            </div>

                            <div class="public-profile-presence-grid">
                                @foreach($presenceCards as $card)
                                    <article class="public-profile-presence-card">
                                        <span>{{ $card['label'] }}</span>
                                        <strong>{{ $card['value'] }}</strong>
                                        <p>{{ $card['body'] }}</p>
                                    </article>
                                @endforeach
                            </div>
                        </section>

                        <section class="public-profile-surface public-profile-review-surface">
                            <div class="tt-heading tt-heading-sm public-profile-heading">
                                <h3 class="tt-heading-subtitle">Avis membre</h3>
                                <h2 class="tt-heading-title">Retour sur le club</h2>
                                <p class="max-width-800 text-muted">
                                    Cette zone reprend le langage visuel de la section avis du site pour mettre le membre en avant.
                                </p>
                            </div>

                            <div class="public-profile-review-grid">
                                @if($reviewCard)
                                    <div class="tt-stte-item">
                                        @include('pages.reviews.partials.card', ['review' => $reviewCard])
                                    </div>
                                @else
                                    <div class="public-profile-empty">
                                        Aucun avis public pour le moment.
                                    </div>
                                @endif

                                <aside class="public-profile-note-card">
                                    <span class="public-profile-eyebrow">En bref</span>
                                    <h3>{{ $reviewCard ? 'Avis publie' : 'Pas encore d avis' }}</h3>
                                    <p>
                                        {{ $reviewCard
                                            ? 'Le membre a deja partage son ressenti sur le club. Son avis reste visible tant qu il est publie.'
                                            : 'Le profil ne dispose pas encore d avis public. Des qu un avis est publie, il apparaitra ici avec la meme presentation que sur la home.' }}
                                    </p>
                                    <ul>
                                        <li>Profil public lie au membre ERAH.</li>
                                        <li>Badges, supporter et progression valorises quand ils existent.</li>
                                        <li>Ordre public gere automatiquement par date de publication.</li>
                                    </ul>

                                    <div class="public-profile-actions">
                                        <a href="{{ route('reviews.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                            <span data-hover="Voir tous les avis">Voir tous les avis</span>
                                        </a>

                                        @if(($viewer?->id ?? null) === $userProfile->id)
                                            <a href="{{ route('profile.show') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Gerer mon avis">Gerer mon avis</span>
                                            </a>
                                        @endif
                                    </div>
                                </aside>
                            </div>
                        </section>

                        <section class="public-profile-surface">
                            <div class="tt-heading tt-heading-sm public-profile-heading">
                                <h3 class="tt-heading-subtitle">Activite</h3>
                                <h2 class="tt-heading-title">Historique recent</h2>
                                <p class="max-width-800 text-muted">
                                    Dernieres evolutions XP et classement pour comprendre la dynamique de progression du membre.
                                </p>
                            </div>

                            @if($activityFeed->isNotEmpty())
                                <div class="tt-avards-list public-profile-activity-list">
                                    @foreach($activityFeed as $item)
                                        <div class="tt-avlist-item cursor-alter tt-anim-fadeinup">
                                            <div class="tt-avlist-item-inner">
                                                <div class="tt-avlist-col tt-avlist-col-count">
                                                    <div class="tt-avlist-count">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                                </div>

                                                <div class="tt-avlist-col tt-avlist-col-title">
                                                    <h4 class="tt-avlist-title">{{ $item['title'] }}</h4>
                                                    <div class="public-profile-activity-meta">{{ $item['meta'] }}</div>
                                                </div>

                                                <div class="tt-avlist-col tt-avlist-col-description">
                                                    <div class="tt-avlist-description">{{ $item['description'] }}</div>
                                                </div>

                                                <div class="tt-avlist-col tt-avlist-col-info">
                                                    <div class="public-profile-activity-points {{ $item['is_rank'] ? 'is-rank' : '' }}">
                                                        {{ $item['points'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="public-profile-empty">Aucune transaction recente.</div>
                            @endif
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
