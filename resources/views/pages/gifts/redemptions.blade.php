@extends('layouts.app')

@section('title', 'Mes redemptions')

@section('content')
    <section class="section">
        <h1>Mes redemptions</h1>
        <p><a href="{{ route('gifts.index') }}">Retour cadeaux</a></p>

        @if(($redemptions ?? null) && $redemptions->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Cadeau</th>
                            <th>Statut</th>
                            <th>Cout</th>
                            <th>Tracking</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($redemptions as $redemption)
                        <tr>
                            <td>{{ optional($redemption->requested_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $redemption->gift->title ?? 'Gift' }}</td>
                            <td>{{ $redemption->status }}</td>
                            <td>{{ $redemption->cost_points_snapshot }}</td>
                            <td>{{ $redemption->tracking_code ?: '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="actions">{{ $redemptions->links() }}</div>
        @else
            <p class="meta">Aucune redemption.</p>
        @endif
    </section>
@endsection
