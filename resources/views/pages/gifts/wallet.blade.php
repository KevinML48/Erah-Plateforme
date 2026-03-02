@extends('layouts.app')

@section('title', 'Reward wallet')

@section('content')
    <section class="section">
        <h1>Reward wallet</h1>
        <p>Solde actuel: <strong>{{ (int) ($wallet->balance ?? 0) }}</strong> reward_points</p>
    </section>

    <section class="section">
        <h2>Transactions reward</h2>
        @if(($transactions ?? null) && $transactions->count())
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Solde apres</th>
                        <th>Ref</th>
                        <th>Unique key</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $tx)
                        <tr>
                            <td>{{ optional($tx->created_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ $tx->type }}</td>
                            <td>{{ $tx->amount }}</td>
                            <td>{{ $tx->balance_after }}</td>
                            <td>{{ $tx->ref_type }}#{{ $tx->ref_id }}</td>
                            <td class="meta">{{ $tx->unique_key }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="actions">{{ $transactions->links() }}</div>
        @else
            <p class="meta">Aucune transaction reward.</p>
        @endif
    </section>
@endsection
