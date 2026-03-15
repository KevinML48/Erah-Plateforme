@extends('layouts.app')

@section('title', 'Parametres')

@section('content')
    <div class="page-shell">
        <section class="section page-hero">
            <span class="section-kicker">Compte et préférences</span>
            <h1 class="page-title">Parametres</h1>
            <p class="page-description">
                Retrouvez vos réglages utiles au meme endroit: notifications, profil, portefeuille, aide et acces rapides a votre espace membre.
            </p>
        </section>

        <section class="section">
            <span class="section-kicker">Acces directs</span>
            <h2>Les réglages qui servent vraiment</h2>
            <p class="page-description">Chaque bloc renvoie vers une page deja existante de la plateforme, sans ajouter de parcours parasite.</p>

            <div class="link-grid">
                <a href="{{ route('profile.show') }}" class="link-card">
                    <span class="link-card-title">Profil</span>
                    <strong>Mon profil</strong>
                    <p>Mettre a jour vos informations, vos reseaux et votre presentation membre.</p>
                </a>

                <a href="{{ route('notifications.préférences') }}" class="link-card">
                    <span class="link-card-title">Notifications</span>
                    <strong>Préférences d alertes</strong>
                    <p>Choisir les canaux et les notifications a recevoir selon vos usages.</p>
                </a>

                <a href="{{ route('wallet.index') }}" class="link-card">
                    <span class="link-card-title">Points</span>
                    <strong>Portefeuille plateforme</strong>
                    <p>Consulter votre solde unique, vos entrées et vos débits recents.</p>
                </a>

                <a href="{{ route('missions.index') }}" class="link-card">
                    <span class="link-card-title">Progression</span>
                    <strong>Missions et focus</strong>
                    <p>Suivre vos missions actives, vos priorites et votre progression globale.</p>
                </a>

                <a href="{{ route('assistant.index') }}" class="link-card">
                    <span class="link-card-title">Assistant</span>
                    <strong>Poser une question</strong>
                    <p>Ouvrir l assistant ERAH pour comprendre un module ou reprendre vos repères.</p>
                </a>

                <a href="{{ route('console.help') }}" class="link-card">
                    <span class="link-card-title">Documentation</span>
                    <strong>Help center</strong>
                    <p>Retrouver les guides, les questions frequentes et la visite guidee.</p>
                </a>
            </div>

            @if (($user ?? null)?->role === 'admin')
                <div class="actions">
                    <a href="{{ route('admin.dashboard') }}" class="tt-btn tt-btn-outline">
                        <span data-hover="Administration">Ouvrir l administration</span>
                    </a>
                </div>
            @endif
        </section>

        <section class="section">
            <span class="section-kicker">Session</span>
            <h2>Sortir de la session</h2>
            <p class="page-description">Vous pouvez vous deconnecter ici sans impacter vos donnees ni vos réglages enregistres.</p>

            <form method="POST" action="{{ route('auth.logout') }}" class="actions actions-stack-mobile">
                @csrf
                <button type="submit" class="tt-btn tt-btn-primary">
                    <span data-hover="Se deconnecter">Se deconnecter</span>
                </button>
            </form>
        </section>
    </div>
@endsection
