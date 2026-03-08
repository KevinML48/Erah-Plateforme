@extends('layouts.app')

@section('title', 'Onboarding')

@section('content')
    <section class="section">
        <h1>Onboarding</h1>
        <p>Activez ou non les canaux de notifications principaux.</p>

        <form method="POST" action="{{ route('onboarding.store') }}" class="grid">
            @csrf

            @php($channelsData = $channels ?? [])

            <label class="checkbox-field">
                <input type="checkbox" name="email_opt_in" value="1" {{ old('email_opt_in', $channelsData['email_opt_in'] ?? false) ? 'checked' : '' }}>
                <span class="checkbox-field-content">
                    <span>Notifications email</span>
                    <span class="meta">Recevoir les rappels et infos importantes par email.</span>
                </span>
            </label>

            <label class="checkbox-field">
                <input type="checkbox" name="push_opt_in" value="1" {{ old('push_opt_in', $channelsData['push_opt_in'] ?? false) ? 'checked' : '' }}>
                <span class="checkbox-field-content">
                    <span>Notifications push</span>
                    <span class="meta">Etre alerte rapidement sur les interactions principales.</span>
                </span>
            </label>

            <div class="actions actions-stack-mobile">
                <button type="submit">Terminer</button>
                <a class="button-link" href="{{ route('dashboard') }}">Passer</a>
            </div>
        </form>
    </section>
@endsection
