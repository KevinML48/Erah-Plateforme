@extends('layouts.app')

@section('title', 'Duels')

@section('content')
    <section class="section">
        <h1>Duels</h1>
        <div class="actions">
            <a class="button-link" href="{{ route('duels.index', ['status' => 'pending']) }}">Pending</a>
            <a class="button-link" href="{{ route('duels.index', ['status' => 'active']) }}">Active</a>
            <a class="button-link" href="{{ route('duels.index', ['status' => 'finished']) }}">Finished</a>
            <a class="button-link" href="{{ route('duels.create') }}">Creer un duel</a>
        </div>
    </section>

    <section class="section">
        @if(($duels ?? null) && $duels->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Challenger</th>
                            <th>Challenged</th>
                            <th>Status</th>
                            <th>Expire</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($duels as $duel)
                        <tr>
                            <td>{{ $duel->id }}</td>
                            <td>{{ $duel->challenger->name ?? 'N/A' }}</td>
                            <td>{{ $duel->challenged->name ?? 'N/A' }}</td>
                            <td>{{ $duel->status }}</td>
                            <td>{{ optional($duel->expires_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($duel->status === \App\Models\Duel::STATUS_PENDING && $duel->challenged_user_id === auth()->id())
                                    <div class="actions">
                                        <form method="POST" action="{{ route('duels.accept', $duel->id) }}">
                                            @csrf
                                            <button type="submit">Accepter</button>
                                        </form>
                                        <form method="POST" action="{{ route('duels.refuse', $duel->id) }}">
                                            @csrf
                                            <button type="submit">Refuser</button>
                                        </form>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="actions">{{ $duels->links() }}</div>
        @else
            <p class="meta">Aucun duel pour ce filtre.</p>
        @endif
    </section>
@endsection
