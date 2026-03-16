@php
    $quickStats = $homeQuickAccess['quick_stats'] ?? [];
    $primaryStats = [
        [
            'value' => (int) ($quickStats['total_xp'] ?? 0),
            'label' => 'XP cumule',
            'meta' => 'Progression globale',
        ],
        [
            'value' => (int) ($quickStats['league_points'] ?? 0),
            'label' => 'Points classement',
            'meta' => 'Impact competition',
        ],
        [
            'value' => (int) ($quickStats['active_missions'] ?? 0),
            'label' => 'Missions actives',
            'meta' => 'Actions a suivre',
        ],
        [
            'value' => (int) ($quickStats['unread_notifications'] ?? 0),
            'label' => 'Notifications',
            'meta' => 'A consulter',
        ],
    ];
    $secondaryStats = [
        [
            'value' => (int) ($quickStats['pending_duels'] ?? 0),
            'label' => 'Demandes duel',
        ],
        [
            'value' => (int) ($quickStats['active_duels'] ?? 0),
            'label' => 'Duels en cours',
        ],
        [
            'value' => (int) ($quickStats['available_missions'] ?? 0),
            'label' => 'Missions disponibles',
        ],
        [
            'value' => (int) ($quickStats['live_matches'] ?? 0),
            'label' => 'Matchs live',
        ],
    ];
@endphp

<style>
    .home-summary-shell {
        display: grid;
        gap: 22px;
    }

    .home-summary-top {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 20px;
    }

    .home-summary-copy {
        max-width: 720px;
    }

    .home-summary-copy p {
        margin: 10px 0 0;
        color: rgba(255, 255, 255, .62);
        line-height: 1.65;
    }

    .home-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .home-summary-card {
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 18px;
        padding: 18px 18px 16px;
        background: linear-gradient(160deg, rgba(255, 255, 255, .06), rgba(255, 255, 255, .015));
        min-height: 146px;
    }

    .home-summary-card strong {
        display: block;
        font-size: 34px;
        line-height: .95;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .home-summary-card span {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: rgba(255, 255, 255, .72);
    }

    .home-summary-card small {
        display: block;
        margin-top: 14px;
        font-size: 13px;
        line-height: 1.55;
        color: rgba(255, 255, 255, .52);
    }

    .home-summary-user {
        color: rgba(255, 255, 255, .78);
    }

    .home-summary-details {
        border-top: 1px solid rgba(255, 255, 255, .1);
        padding-top: 18px;
    }

    .home-summary-details summary {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        list-style: none;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: rgba(255, 255, 255, .76);
    }

    .home-summary-details summary::-webkit-details-marker {
        display: none;
    }

    .home-summary-details summary::after {
        content: '+';
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border: 1px solid rgba(255, 255, 255, .18);
        border-radius: 999px;
        font-size: 15px;
        line-height: 1;
    }

    .home-summary-details[open] summary::after {
        content: '-';
    }

    .home-summary-secondary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-top: 16px;
    }

    .home-summary-secondary-item {
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 14px;
        padding: 12px 14px;
        background: rgba(255, 255, 255, .03);
    }

    .home-summary-secondary-item strong {
        display: block;
        font-size: 19px;
        line-height: 1;
        margin-bottom: 8px;
    }

    .home-summary-secondary-item span {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: rgba(255, 255, 255, .62);
    }

    body.tt-lightmode-on .home-summary-card {
        border-color: rgba(33, 33, 33, .16);
        background: linear-gradient(160deg, rgba(255, 255, 255, .92), rgba(246, 242, 237, .86));
        box-shadow: 0 10px 24px rgba(33, 33, 33, .05);
    }

    body.tt-lightmode-on .home-summary-card strong {
        color: #171717;
    }

    body.tt-lightmode-on .home-summary-card span {
        color: rgba(23, 23, 23, .62);
    }

    body.tt-lightmode-on .home-summary-card small {
        color: rgba(23, 23, 23, .5);
    }

    body.tt-lightmode-on .home-summary-user {
        color: rgba(23, 23, 23, .66);
    }

    body.tt-lightmode-on .home-summary-meta {
        color: rgba(23, 23, 23, .72);
    }

    body.tt-lightmode-on .home-summary-copy p {
        color: rgba(23, 23, 23, .62);
    }

    body.tt-lightmode-on .home-summary-details {
        border-top-color: rgba(23, 23, 23, .1);
    }

    body.tt-lightmode-on .home-summary-details summary {
        color: rgba(23, 23, 23, .68);
    }

    body.tt-lightmode-on .home-summary-details summary::after {
        border-color: rgba(23, 23, 23, .16);
    }

    body.tt-lightmode-on .home-summary-secondary-item {
        border-color: rgba(23, 23, 23, .1);
        background: rgba(255, 255, 255, .75);
    }

    body.tt-lightmode-on .home-summary-secondary-item strong {
        color: #171717;
    }

    body.tt-lightmode-on .home-summary-secondary-item span {
        color: rgba(23, 23, 23, .58);
    }

    @media (max-width: 1199.98px) {
        .home-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .home-summary-secondary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .home-summary-top {
            flex-direction: column;
            align-items: start;
        }

        .home-summary-grid {
            grid-template-columns: 1fr;
        }

        .home-summary-secondary {
            grid-template-columns: 1fr;
        }

        .home-summary-card {
            min-height: auto;
        }
    }
</style>

<div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top home-summary-section">
    <div class="tt-section-inner tt-wrap max-width-1800">
        <div class="tt-heading tt-heading-lg margin-bottom-30">
            <h3 class="tt-heading-subtitle tt-text-uppercase">Accueil</h3>
            <h2 class="tt-heading-title">Resume profil</h2>
        </div>

        <section class="home-summary-shell">
            <div class="home-summary-top">
                <div class="home-summary-copy">
                    @auth
                        <div class="home-summary-user tt-text-uppercase">
                            {{ auth()->user()->name }} - {{ auth()->user()->email }}
                        </div>
                        <p>
                            Un apercu synthetique de votre activite ERAH, recentre sur la progression, le classement et les actions qui meritent une attention immediate.
                        </p>
                    @else
                        <div class="home-summary-user tt-text-uppercase">
                            Decouvrez les modules publics puis connectez-vous pour suivre votre progression membre.
                        </div>
                        <p>
                            La plateforme met en avant quelques reperes clairs pour suivre l'essentiel sans transformer l'accueil en tableau de bord.
                        </p>
                    @endauth
                </div>

                <p class="tt-form-text home-summary-meta">
                    Ligue actuelle: {{ $quickStats['league_name'] ?? 'Non classee' }}
                </p>
            </div>

            <div class="home-summary-grid">
                @foreach ($primaryStats as $stat)
                    <article class="home-summary-card tt-anim-fadeinup">
                        <strong>{{ $stat['value'] }}</strong>
                        <span>{{ $stat['label'] }}</span>
                        <small>{{ $stat['meta'] }}</small>
                    </article>
                @endforeach
            </div>

            <details class="home-summary-details">
                <summary>Voir plus d'indicateurs</summary>

                <div class="home-summary-secondary">
                    @foreach ($secondaryStats as $stat)
                        <article class="home-summary-secondary-item">
                            <strong>{{ $stat['value'] }}</strong>
                            <span>{{ $stat['label'] }}</span>
                        </article>
                    @endforeach
                </div>
            </details>
        </section>
    </div>
</div>
