@extends('layouts.app')

@section('title', 'Admin matches create legacy')

@section('content')
    <section class="section">
        <h1>Admin matches - create (legacy view)</h1>
        <p>Cette vue legacy est conservee pour compatibilite. Utilisez la route principale ci-dessous.</p>
        <div class="actions">
            <a class="button-link" href="{{ route('admin.matches.create') }}">Ouvrir le formulaire principal</a>
            <a class="button-link" href="{{ route('admin.matches.index') }}">Retour liste admin matches</a>
        </div>
    </section>
@endsection
