@extends('layouts.app')

@section('title', 'Settings')

@section('content')
    <section class="section">
        <h1>Settings</h1>
        <p>Page simple pour centraliser les liens utiles.</p>

        <ul class="grid">
            <li><a href="{{ route('notifications.preferences') }}">Preferences notifications</a></li>
            <li><a href="{{ route('profile.show') }}">Mon profil</a></li>
            <li><a href="{{ route('wallet.index') }}">Wallet bet_points</a></li>
            <li><a href="{{ route('gifts.wallet') }}">Wallet reward_points</a></li>
        </ul>

        <form method="POST" action="{{ route('auth.logout') }}" class="actions actions-stack-mobile">
            @csrf
            <button type="submit">Se deconnecter</button>
        </form>
    </section>
@endsection
