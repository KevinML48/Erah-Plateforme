@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <section class="section">
        <div style="display:none">Auth\/Login</div>
        <h1>Connexion</h1>
        <p class="meta">Connexion web classique.</p>

        <form method="POST" action="{{ route('auth.login') }}" class="grid">
            @csrf

            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
            </div>

            <div>
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">
            </div>

            <div>
                <label>
                    <input type="checkbox" name="remember" value="1" {{ old('remember', '1') ? 'checked' : '' }}>
                    Se souvenir de moi
                </label>
            </div>

            <div class="actions">
                <button type="submit">Se connecter</button>
                <a class="button-link" href="{{ route('register') }}">Creer un compte</a>
            </div>
        </form>

        <hr>

        <div class="actions">
            <a class="button-link" href="{{ url('/auth/google/redirect') }}">Connexion Google</a>
            <a class="button-link" href="{{ url('/auth/discord/redirect') }}">Connexion Discord</a>
        </div>
    </section>
@endsection
