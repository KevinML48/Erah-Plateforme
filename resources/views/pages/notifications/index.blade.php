@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <section class="section">
        <h1>Notifications</h1>
        <div class="actions">
            <a class="button-link" href="{{ route('notifications.index') }}">Toutes</a>
            <a class="button-link" href="{{ route('notifications.index', ['unread' => 1]) }}">Non lues</a>
            <a class="button-link" href="{{ route('notifications.preferences') }}">Preferences</a>

            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit">Tout marquer lu</button>
            </form>
        </div>
    </section>

    <section class="section">
        @if(($notifications ?? null) && $notifications->count())
            <ul>
                @foreach($notifications as $notification)
                    <li>
                        <strong>{{ $notification->title }}</strong>
                        <span class="badge">{{ $notification->category }}</span>
                        @if(!$notification->read_at)
                            <span class="badge">non lu</span>
                        @endif
                        <p>{{ $notification->body }}</p>
                        <p class="meta">{{ optional($notification->created_at)->format('Y-m-d H:i') }}</p>

                        @if(!$notification->read_at)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit">Marquer lu</button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="actions">{{ $notifications->links() }}</div>
        @else
            <p class="meta">Aucune notification.</p>
        @endif
    </section>
@endsection
