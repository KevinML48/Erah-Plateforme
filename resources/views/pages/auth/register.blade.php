@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <section class="section">
        <div style="display:none">Auth\/Register</div>
        <h1>Creation de compte</h1>

        <form method="POST" action="{{ route('auth.register') }}" class="grid">
            @csrf

            <div>
                <label for="name">Nom</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name">
            </div>

            <div>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
            </div>

            <div>
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required autocomplete="new-password">
            </div>

            <div>
                <label for="password_confirmation">Confirmation mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
            </div>

            <div>
                <label>
                    <input type="checkbox" name="remember" value="1" {{ old('remember', '1') ? 'checked' : '' }}>
                    Se souvenir de moi
                </label>
            </div>

            <div class="actions">
                <button type="submit">Creer mon compte</button>
                <a class="button-link" href="{{ route('login') }}">Deja inscrit ?</a>
            </div>
        </form>
    </section>
@endsection
