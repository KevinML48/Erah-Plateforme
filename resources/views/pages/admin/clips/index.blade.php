@extends('layouts.app')

@section('title', 'Admin clips')

@section('content')
    <section class="section">
        <h1>Admin clips</h1>
        <div class="actions">
            <a class="button-link" href="{{ route('admin.clips.create') }}">Nouveau clip</a>
            <a class="button-link" href="{{ route('admin.clips.index', ['status' => 'all']) }}">Tous</a>
            <a class="button-link" href="{{ route('admin.clips.index', ['status' => 'published']) }}">Publies</a>
            <a class="button-link" href="{{ route('admin.clips.index', ['status' => 'draft']) }}">Brouillons</a>
        </div>
    </section>

    <section class="section">
        @if(($clips ?? null) && $clips->count())
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Slug</th>
                        <th>Etat</th>
                        <th>Stats</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($clips as $clip)
                        <tr>
                            <td>{{ $clip->id }}</td>
                            <td>{{ $clip->title }}</td>
                            <td>{{ $clip->slug }}</td>
                            <td>{{ $clip->is_published ? 'published' : 'draft' }}</td>
                            <td>likes {{ $clip->likes_count }} / comments {{ $clip->comments_count }} / favorites {{ $clip->favorites_count }}</td>
                            <td>
                                <div class="actions">
                                    <a class="button-link" href="{{ route('admin.clips.edit', $clip->id) }}">Edit</a>
                                    @if($clip->is_published)
                                        <form method="POST" action="{{ route('admin.clips.unpublish', $clip->id) }}">
                                            @csrf
                                            <button type="submit">Unpublish</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.clips.publish', $clip->id) }}">
                                            @csrf
                                            <button type="submit">Publish</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.clips.destroy', $clip->id) }}" onsubmit="return confirm('Supprimer ce clip ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="actions">{{ $clips->links() }}</div>
        @else
            <p class="meta">Aucun clip.</p>
        @endif
    </section>
@endsection
