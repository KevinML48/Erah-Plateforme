@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
    <section class="section">
        <h1>Welcome</h1>
        <p>Base neutre Laravel.</p>
        <div class="actions">
            @auth
                <a class="button-link" href="{{ route('dashboard') }}">Ouvrir la console</a>
            @else
                <a class="button-link" href="{{ route('login') }}">Se connecter</a>
            @endauth
        </div>
    </section>
@endsection
