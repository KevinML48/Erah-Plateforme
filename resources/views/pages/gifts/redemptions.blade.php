@extends('layouts.app')

@section('title', 'Mes demandes cadeaux')

@section('content')
    <div class="page-shell">
        <section class="section page-hero">
            <span class="section-kicker">Cadeaux</span>
            <h1 class="page-title">Mes demandes cadeaux</h1>
            <p class="page-description">Suivez ici vos demandes, leur statut et les informations de suivi quand elles existent deja.</p>

            <div class="actions">
                <a href="{{ route('gifts.index') }}" class="tt-btn tt-btn-outline">
                    <span data-hover="Retour au catalogue">Retour au catalogue</span>
                </a>
            </div>
        </section>

        <section class="section">
            @if(($redemptions ?? null) && $redemptions->count())
                <div class="table-wrap" data-responsive="cards">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Cadeau</th>
                                <th>Statut</th>
                                <th>Cout</th>
                                <th>Suivi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($redemptions as $redemption)
                            <tr>
                                <td data-label="Date">{{ optional($redemption->requested_at)->format('Y-m-d H:i') }}</td>
                                <td data-label="Cadeau">{{ $redemption->gift->title ?? 'Cadeau' }}</td>
                                <td data-label="Statut">{{ \Illuminate\Support\Str::headline((string) $redemption->status) }}</td>
                                <td data-label="Cout">{{ $redemption->cost_points_snapshot }} pts</td>
                                <td data-label="Suivi">{{ $redemption->tracking_code ?: '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="actions">{{ $redemptions->links() }}</div>
            @else
                <div class="app-empty-state">
                    <p>Aucune demande cadeau pour le moment. Des que vous debloquez un article, il apparait ici avec son suivi.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
