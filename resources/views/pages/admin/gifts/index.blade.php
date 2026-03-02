@extends('layouts.app')

@section('title', 'Admin gifts')

@section('content')
    <section class="section">
        <h1>Admin gifts</h1>
        <p class="meta">CRUD cadeaux + moderation redemptions.</p>
    </section>

    <section class="section">
        <h2>Creer cadeau</h2>
        <form method="POST" action="{{ route('admin.gifts.store') }}" class="grid grid-4">
            @csrf
            <div><label>Titre</label><input name="title" required></div>
            <div><label>Description</label><input name="description"></div>
            <div><label>Image URL</label><input name="image_url" type="url"></div>
            <div><label>Cost points</label><input name="cost_points" type="number" min="1" value="1000" required></div>
            <div><label>Stock</label><input name="stock" type="number" min="0" value="10" required></div>
            <div><label><input type="checkbox" name="is_active" value="1" checked> Actif</label></div>
            <div class="actions"><button type="submit">Creer</button></div>
        </form>
    </section>

    <section class="section">
        <h2>Liste cadeaux</h2>
        @if($gifts->count())
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr><th>ID</th><th>Titre</th><th>Cost</th><th>Stock</th><th>Actif</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    @foreach($gifts as $gift)
                        <tr>
                            <td>{{ $gift->id }}</td>
                            <td>{{ $gift->title }}</td>
                            <td>{{ $gift->cost_points }}</td>
                            <td>{{ $gift->stock }}</td>
                            <td>{{ $gift->is_active ? 'yes' : 'no' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.gifts.update', $gift->id) }}" class="inline-form">
                                    @csrf
                                    @method('PUT')
                                    <input name="title" value="{{ $gift->title }}" required>
                                    <input name="description" value="{{ $gift->description }}">
                                    <input name="image_url" value="{{ $gift->image_url }}">
                                    <input name="cost_points" type="number" min="1" value="{{ $gift->cost_points }}" required>
                                    <input name="stock" type="number" min="0" value="{{ $gift->stock }}" required>
                                    <label><input type="checkbox" name="is_active" value="1" {{ $gift->is_active ? 'checked' : '' }}> actif</label>
                                    <button type="submit">MAJ</button>
                                </form>
                                <form method="POST" action="{{ route('admin.gifts.destroy', $gift->id) }}" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="actions">{{ $gifts->links() }}</div>
        @else
            <p class="meta">Aucun cadeau.</p>
        @endif
    </section>

    <section class="section">
        <h2>Redemptions</h2>
        <div class="actions">
            <a class="button-link" href="{{ route('admin.gifts.index', ['status' => 'all']) }}">all</a>
            @foreach($statuses as $item)
                <a class="button-link" href="{{ route('admin.gifts.index', ['status' => $item]) }}">{{ $item }}</a>
            @endforeach
        </div>

        @if($redemptions->count())
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr><th>ID</th><th>User</th><th>Gift</th><th>Status</th><th>Cost</th><th>Requested</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    @foreach($redemptions as $redemption)
                        <tr>
                            <td>{{ $redemption->id }}</td>
                            <td>#{{ $redemption->user_id }} {{ $redemption->user->name ?? '' }}</td>
                            <td>#{{ $redemption->gift_id }} {{ $redemption->gift->title ?? '' }}</td>
                            <td><span class="badge">{{ $redemption->status }}</span></td>
                            <td>{{ $redemption->cost_points_snapshot }}</td>
                            <td>{{ optional($redemption->requested_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="actions">
                                    <form method="POST" action="{{ route('admin.redemptions.approve', $redemption->id) }}" class="inline-form">
                                        @csrf
                                        <button type="submit">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.redemptions.reject', $redemption->id) }}" class="inline-form">
                                        @csrf
                                        <input name="reason" placeholder="reason">
                                        <button type="submit">Reject</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.redemptions.ship', $redemption->id) }}" class="inline-form">
                                        @csrf
                                        <input name="tracking_code" placeholder="tracking">
                                        <button type="submit">Ship</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.redemptions.deliver', $redemption->id) }}" class="inline-form">
                                        @csrf
                                        <button type="submit">Deliver</button>
                                    </form>
                                </div>
                            </td>
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

