@php
    $quickStats = $homeQuickAccess['quick_stats'] ?? [];
@endphp

<style>
    .home-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .home-summary-card {
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 14px;
        padding: 14px 16px;
        background: linear-gradient(160deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .01));
    }

    .home-summary-card strong {
        display: block;
        font-size: 34px;
        line-height: 1;
        margin-bottom: 6px;
        font-weight: 700;
    }

    .home-summary-card span {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: rgba(255, 255, 255, .72);
    }

    .home-summary-user {
        margin-bottom: 18px;
        color: rgba(255, 255, 255, .78);
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

    body.tt-lightmode-on .home-summary-user {
        color: rgba(23, 23, 23, .66);
    }

    body.tt-lightmode-on .home-summary-meta {
        color: rgba(23, 23, 23, .72);
    }

    @media (max-width: 1199.98px) {
        .home-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .home-summary-grid {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }

        .home-summary-grid::-webkit-scrollbar {
            height: 6px;
        }

        .home-summary-grid::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .24);
            border-radius: 999px;
        }

        .home-summary-card {
            flex: 0 0 82%;
            min-width: 82%;
            scroll-snap-align: start;
        }
    }
</style>

<div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top home-summary-section">
    <div class="tt-section-inner tt-wrap max-width-1800">
        <div class="tt-heading tt-heading-lg margin-bottom-30">
            <h3 class="tt-heading-subtitle tt-text-uppercase">Accueil</h3>
            <h2 class="tt-heading-title">Resume profil</h2>
        </div>

        @auth
            <div class="home-summary-user tt-text-uppercase">
                {{ auth()->user()->name }} - {{ auth()->user()->email }}
            </div>
        @else
            <div class="home-summary-user tt-text-uppercase">
                Decouvrez les modules publics puis connectez-vous pour suivre votre progression membre.
            </div>
        @endauth

        <section class="home-summary-grid">
            <article class="home-summary-card tt-anim-fadeinup">
                <strong>{{ (int) ($quickStats['total_xp'] ?? 0) }}</strong>
                <span>XP cumule</span>
            </article>
            <article class="home-summary-card tt-anim-fadeinup">
                <strong>{{ (int) ($quickStats['league_points'] ?? 0) }}</strong>
                <span>Points classement</span>
            </article>
            <article class="home-summary-card tt-anim-fadeinup">
                <strong>{{ (int) ($quickStats['pending_duels'] ?? 0) }}</strong>
                <span>Demandes duel</span>
            </article>
            <article class="home-summary-card tt-anim-fadeinup">
                <strong>{{ (int) ($quickStats['active_duels'] ?? 0) }}</strong>
                <span>Duels en cours</span>
            </article>
            <article class="home-summary-card tt-anim-fadeinup">
                <strong>{{ (int) ($quickStats['available_missions'] ?? 0) }}</strong>
                <span>Missions disponibles</span>
            </article>
            <article class="home-summary-card tt-anim-fadeinup">
                <strong>{{ (int) ($quickStats['available_missions'] ?? 0) }}</strong>
                <span>Missions dispo</span>
            </article>
            <article class="home-summary-card tt-anim-fadeinup">
                <strong>{{ (int) ($quickStats['unread_notifications'] ?? 0) }}</strong>
                <span>Notifications</span>
            </article>
            <article class="home-summary-card tt-anim-fadeinup">
                <strong>{{ (int) ($quickStats['live_matches'] ?? 0) }}</strong>
                <span>Matchs live</span>
            </article>
        </section>

        <p class="tt-form-text margin-top-20 home-summary-meta">
            Ligue actuelle: {{ $quickStats['league_name'] ?? 'Non classee' }}
        </p>
    </div>
</div>
