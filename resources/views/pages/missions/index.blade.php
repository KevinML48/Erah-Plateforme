@extends('layouts.app')

@section('title', 'Missions')

@section('content')
    <section class="section">
        <h1>Missions</h1>
        <p>Vue neutre des missions quotidiennes et hebdomadaires.</p>
    </section>

    <section class="section">
        <h2>Daily</h2>
        @if(($dailyMissions ?? null) && $dailyMissions->count())
            <ul>
                @foreach($dailyMissions as $mission)
                    @php($template = $mission->instance->template)
                    <li>
                        <strong>{{ $template->title }}</strong>
                        <span class="meta">{{ $template->event_type }}</span>
                        <p>{{ $template->description }}</p>
                        <p>Progression: {{ $mission->progress_count }} / {{ $template->target_count }}</p>
                        <p class="meta">Etat: {{ $mission->completed_at ? 'completee' : 'en cours' }}</p>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="meta">Aucune mission daily.</p>
        @endif
    </section>

    <section class="section">
        <h2>Weekly</h2>
        @if(($weeklyMissions ?? null) && $weeklyMissions->count())
            <ul>
                @foreach($weeklyMissions as $mission)
                    @php($template = $mission->instance->template)
                    <li>
                        <strong>{{ $template->title }}</strong>
                        <span class="meta">{{ $template->event_type }}</span>
                        <p>{{ $template->description }}</p>
                        <p>Progression: {{ $mission->progress_count }} / {{ $template->target_count }}</p>
                        <p class="meta">Etat: {{ $mission->completed_at ? 'completee' : 'en cours' }}</p>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="meta">Aucune mission weekly.</p>
        @endif
    </section>

    <section class="section">
        <h2>Historique</h2>
        @if(($history ?? null) && $history->count())
            <ul>
                @foreach($history as $mission)
                    <li>
                        {{ optional($mission->updated_at)->format('Y-m-d H:i') }} -
                        {{ $mission->instance->template->title ?? 'Mission' }}
                        ({{ $mission->progress_count }})
                    </li>
                @endforeach
            </ul>
            <div class="actions">{{ $history->links() }}</div>
        @else
            <p class="meta">Historique vide.</p>
        @endif
    </section>
@endsection
