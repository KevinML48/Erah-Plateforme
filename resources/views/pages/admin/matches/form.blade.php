@extends('layouts.app')

@section('title', ($match ? 'Edit match' : 'Create match'))

@section('content')
    <section class="section">
        <h1>{{ $match ? 'Modifier match #'.$match->id : 'Creer un match' }}</h1>

        <form method="POST" action="{{ $action }}" class="grid">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            <div class="grid grid-2">
                <div>
                    <label for="team_a_name">Team A</label>
                    <input id="team_a_name" name="team_a_name" value="{{ old('team_a_name', $match->team_a_name ?? '') }}" required>
                </div>
                <div>
                    <label for="team_b_name">Team B</label>
                    <input id="team_b_name" name="team_b_name" value="{{ old('team_b_name', $match->team_b_name ?? '') }}" required>
                </div>
            </div>

            <div class="grid grid-2">
                <div>
                    <label for="starts_at">Starts at</label>
                    <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', isset($match->starts_at) ? $match->starts_at->format('Y-m-d\\TH:i') : '') }}" required>
                </div>
                <div>
                    <label for="locked_at">Locked at (optionnel)</label>
                    <input id="locked_at" name="locked_at" type="datetime-local" value="{{ old('locked_at', isset($match->locked_at) ? $match->locked_at->format('Y-m-d\\TH:i') : '') }}">
                </div>
            </div>

            <div>
                <label for="game_key">Game key</label>
                <input id="game_key" name="game_key" value="{{ old('game_key', $match->game_key ?? '') }}">
            </div>

            <div class="actions">
                <button type="submit">Enregistrer</button>
                <a class="button-link" href="{{ route('admin.matches.index') }}">Retour liste</a>
                @if($match)
                    <a class="button-link" href="{{ route('admin.matches.manage', $match->id) }}">Manage</a>
                @endif
            </div>
        </form>
    </section>
@endsection
