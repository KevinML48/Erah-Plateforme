@extends('layouts.app')

@section('title', 'Bienvenue')

@section('content')
    <div class="page-shell">
        <section class="section page-hero">
            <span class="section-kicker">Derniere etape</span>
            <h1 class="page-title">Finaliser vos preferences</h1>
            <p class="page-description">
                Choisissez simplement comment ERAH peut vous prevenir. Vous pourrez modifier ces reglages plus tard depuis vos parametres.
            </p>
            <div class="chip-row">
                <span class="chip">1 minute</span>
                <span class="chip">Modifiable a tout moment</span>
            </div>
        </section>

        <section class="section">
            <form method="POST" action="{{ route('onboarding.store') }}" class="field-grid">
                @csrf

                @php($channelsData = $channels ?? [])

                <div class="surface-grid">
                    <label class="surface-card checkbox-field">
                        <input type="checkbox" name="email_opt_in" value="1" {{ old('email_opt_in', $channelsData['email_opt_in'] ?? false) ? 'checked' : '' }}>
                        <span class="checkbox-field-content">
                            <span class="surface-card-title">Email</span>
                            <strong>Recevoir les rappels importants</strong>
                            <span class="field-note">Confirmations, informations utiles et rappels qui meritent une trace ecrite.</span>
                        </span>
                    </label>

                    <label class="surface-card checkbox-field">
                        <input type="checkbox" name="push_opt_in" value="1" {{ old('push_opt_in', $channelsData['push_opt_in'] ?? false) ? 'checked' : '' }}>
                        <span class="checkbox-field-content">
                            <span class="surface-card-title">Push</span>
                            <strong>Etre alerte plus vite</strong>
                            <span class="field-note">Pour les activites importantes de la plateforme quand vous voulez repondre rapidement.</span>
                        </span>
                    </label>
                </div>

                <div class="actions actions-stack-mobile">
                    <button type="submit" class="tt-btn tt-btn-primary">
                        <span data-hover="Enregistrer et continuer">Enregistrer et continuer</span>
                    </button>
                    <a class="tt-btn tt-btn-outline" href="{{ route('dashboard') }}">
                        <span data-hover="Continuer sans changer">Continuer sans changer</span>
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection
