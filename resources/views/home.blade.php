@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <section class="section">
        <h1>Home</h1>
        <p>Page home minimaliste.</p>
        <div class="actions">
            @auth
                <a class="tt-btn tt-btn-link" href="{{ route('dashboard') }}">
                    <span data-hover="Dashboard console">Dashboard console</span>
                </a>
            @else
                <a class="tt-btn tt-btn-link" href="{{ route('login') }}">
                    <span data-hover="Login">Login</span>
                </a>
            @endauth
        </div>
    </section>
@endsection
