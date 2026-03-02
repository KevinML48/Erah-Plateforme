@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <section class="section">
        <h1>Home</h1>
        <p>Page home minimaliste.</p>
        <div class="actions">
            @auth
                <a class="button-link" href="{{ route('dashboard') }}">Dashboard console</a>
            @else
                <a class="button-link" href="{{ route('login') }}">Login</a>
            @endauth
        </div>
    </section>
@endsection
