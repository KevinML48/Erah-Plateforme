@extends('layouts.app')

@section('title', 'Parametres')

@section('content')
    @php
        $activeChannels = collect([
            'Email' => (bool) ($user->notificationChannels?->email_opt_in ?? false),
            'Push' => (bool) ($user->notificationChannels?->push_opt_in ?? false),
        ])->filter();

        $activePreferenceCount = $user->notificationPreferences
            ->filter(fn ($preference) => $preference->email_enabled || $preference->push_enabled)
            ->count();

        $settingsCards = [
            [
                'label' => 'Profil',
                'title' => 'Mon identite membre',
                'description' => 'Mettre a jour vos informations, vos reseaux et votre presentation sans quitter votre espace membre.',
                'meta' => 'Coordonnees, avatar et presence publique',
                'icon' => 'fas fa-user-circle',
                'url' => route('profile.show'),
            ],
            [
                'label' => 'Notifications',
                'title' => 'Preferences d alertes',
                'description' => 'Choisir les categories utiles et les canaux a activer selon votre rythme d usage.',
                'meta' => 'Email, push et categories prioritaires',
                'icon' => 'fas fa-bell',
                'url' => route('notifications.preferences'),
            ],
            [
                'label' => 'Points',
                'title' => 'Portefeuille ERAH',
                'description' => 'Consulter votre solde, vos gains, vos depenses recentes et vos mouvements importants.',
                'meta' => 'Transactions et vue rapide de votre balance',
                'icon' => 'fas fa-wallet',
                'url' => route('wallet.index'),
            ],
            [
                'label' => 'Progression',
                'title' => 'Missions et priorites',
                'description' => 'Retrouver vos objectifs actifs, vos favoris et les parcours a faire avancer en priorite.',
                'meta' => 'Parcours membre et progression platforme',
                'icon' => 'fas fa-flag-checkered',
                'url' => route('missions.index'),
            ],
            [
                'label' => 'Assistant',
                'title' => 'Aide contextuelle ERAH',
                'description' => 'Ouvrir l assistant pour retrouver un module, comprendre une fonctionnalite ou reprendre vos reperes.',
                'meta' => 'Reponses rapides et orientation dans la plateforme',
                'icon' => 'fas fa-robot',
                'url' => route('assistant.index'),
            ],
            [
                'label' => 'Support',
                'title' => 'Help center',
                'description' => 'Acceder aux guides, aux questions frequentes et a la documentation utile a votre session.',
                'meta' => 'Guides pratiques et documentation centrale',
                'icon' => 'fas fa-life-ring',
                'url' => route('console.help'),
            ],
        ];
    @endphp

    <div class="page-shell page-shell-settings">
        <section class="section settings-hero">
            <div class="settings-hero-copy">
                <span class="section-kicker">Compte et preferences</span>
                <h1 class="page-title">Parametres</h1>
                <p class="page-description">
                    Pilotez votre compte depuis une seule interface claire, avec des acces rapides, des reglages mieux hierarchises et une lecture immediate de ce qui compte vraiment dans votre espace membre.
                </p>

                <div class="chip-row settings-chip-row">
                    <span class="chip">{{ $user->role === 'admin' ? 'Acces administration' : 'Espace membre actif' }}</span>
                    <span class="chip">{{ $activeChannels->isEmpty() ? 'Alertes en pause' : $activeChannels->keys()->implode(' + ') }}</span>
                    <span class="chip">{{ $user->email }}</span>
                </div>

                <div class="actions settings-hero-actions">
                    <a href="{{ route('profile.show') }}" class="tt-btn tt-btn-secondary">
                        <span data-hover="Mon profil">Mon profil</span>
                    </a>
                    <a href="{{ route('notifications.preferences') }}" class="tt-btn tt-btn-outline">
                        <span data-hover="Notifications">Notifications</span>
                    </a>
                    @if ($user->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="tt-btn tt-btn-outline">
                            <span data-hover="Administration">Administration</span>
                        </a>
                    @endif
                </div>
            </div>

            <aside class="settings-hero-panel">
                <span class="settings-panel-kicker">Vue d ensemble</span>
                <strong class="settings-panel-title">{{ $user->name }}</strong>
                <p class="settings-panel-copy">
                    Une synthese rapide de votre compte pour reprendre la main sans chercher les bons modules page par page.
                </p>

                <div class="settings-panel-stats">
                    <div class="settings-panel-stat">
                        <span>Canaux actifs</span>
                        <strong>{{ $activeChannels->count() }}/2</strong>
                    </div>
                    <div class="settings-panel-stat">
                        <span>Categories reglees</span>
                        <strong>{{ $activePreferenceCount }}</strong>
                    </div>
                    <div class="settings-panel-stat">
                        <span>Role</span>
                        <strong>{{ $user->role === 'admin' ? 'Admin' : 'Membre' }}</strong>
                    </div>
                    <div class="settings-panel-stat">
                        <span>Session</span>
                        <strong>Active</strong>
                    </div>
                </div>

                <div class="settings-panel-links">
                    <a href="{{ route('wallet.index') }}" class="settings-inline-link">Voir le portefeuille</a>
                    <a href="{{ route('missions.index') }}" class="settings-inline-link">Reprendre mes missions</a>
                </div>
            </aside>
        </section>

        <section class="section settings-section">
            <div class="settings-section-head">
                <span class="section-kicker">Acces directs</span>
                <h2>Les reglages vraiment utiles</h2>
                <p class="page-description">
                    Une grille de modules plus lisible, inspiree des compositions editoriales de `templates-neuf`, pour garder une bonne hierarchie entre les actions majeures et les outils secondaires.
                </p>
            </div>

            <div class="settings-card-grid">
                @foreach ($settingsCards as $card)
                    <a href="{{ $card['url'] }}" class="link-card settings-card">
                        <div class="settings-card-head">
                            <span class="settings-card-icon" aria-hidden="true">
                                <i class="{{ $card['icon'] }}"></i>
                            </span>
                            <span class="link-card-title">{{ $card['label'] }}</span>
                        </div>
                        <strong>{{ $card['title'] }}</strong>
                        <p>{{ $card['description'] }}</p>
                        <span class="settings-card-meta">{{ $card['meta'] }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="settings-support-grid">
            <section class="section settings-support-card">
                <span class="section-kicker">Organisation</span>
                <h2>Actions secondaires bien rangees</h2>
                <p class="page-description">
                    Les acces complementaires restent presents, mais mieux regroupes pour alleger la lecture sur desktop comme sur mobile.
                </p>

                <div class="settings-inline-grid">
                    <a href="{{ route('assistant.index') }}" class="settings-inline-card">
                        <strong>Assistant ERAH</strong>
                        <span>Demarrer une aide contextuelle sans changer de parcours.</span>
                    </a>
                    <a href="{{ route('console.help') }}" class="settings-inline-card">
                        <strong>Documentation</strong>
                        <span>Retrouver les guides et les questions frequentes en quelques secondes.</span>
                    </a>
                    <a href="{{ route('notifications.preferences') }}" class="settings-inline-card">
                        <strong>Alertes prioritaires</strong>
                        <span>Ajuster les categories que vous souhaitez vraiment recevoir.</span>
                    </a>
                    <a href="{{ route('wallet.index') }}" class="settings-inline-card">
                        <strong>Flux du portefeuille</strong>
                        <span>Verifier vos mouvements sans quitter la zone compte.</span>
                    </a>
                </div>
            </section>

            <section class="section settings-logout-card">
                <span class="section-kicker">Session</span>
                <h2>Se deconnecter proprement</h2>
                <p class="page-description">
                    La deconnexion est integree comme une action secondaire importante, visible sans casser le rythme visuel de la page.
                </p>

                <form method="POST" action="{{ route('auth.logout') }}" class="settings-logout-form">
                    @csrf
                    <button type="submit" class="tt-btn tt-btn-primary tt-btn-full">
                        <span data-hover="Se deconnecter">Se deconnecter</span>
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
