@extends('layouts.app')

@section('title', 'Admin missions')

@section('content')
    <section class="section">
        <h1>Admin missions</h1>
        <div class="actions">
            <form method="POST" action="{{ route('missions.generate.daily') }}" class="inline-form">
                @csrf
                <button type="submit">Force generate daily</button>
            </form>
            <form method="POST" action="{{ route('missions.generate.weekly') }}" class="inline-form">
                @csrf
                <button type="submit">Force generate weekly</button>
            </form>
        </div>
    </section>

    <section class="section">
        <h2>Creer template mission</h2>
        <form method="POST" action="{{ route('admin.missions.store') }}" class="grid grid-4">
            @csrf
            <div><label>key</label><input name="key" required></div>
            <div><label>title</label><input name="title" required></div>
            <div><label>description</label><input name="description"></div>
            <div><label>event_type</label><input name="event_type" required></div>
            <div><label>target_count</label><input type="number" name="target_count" min="1" value="1" required></div>
            <div>
                <label>scope</label>
                <select name="scope" required>
                    @foreach($scopes as $scope)
                        <option value="{{ $scope }}">{{ $scope }}</option>
                    @endforeach
                </select>
            </div>
            <div><label>start_at</label><input type="datetime-local" name="start_at"></div>
            <div><label>end_at</label><input type="datetime-local" name="end_at"></div>
            <div><label>rewards_xp</label><input type="number" name="rewards_xp" min="0" value="0"></div>
            <div><label>rewards_rank_points</label><input type="number" name="rewards_rank_points" min="0" value="0"></div>
            <div><label>rewards_reward_points</label><input type="number" name="rewards_reward_points" min="0" value="0"></div>
            <div><label>rewards_bet_points</label><input type="number" name="rewards_bet_points" min="0" value="0"></div>
            <div class="full-width"><label>constraints_json</label><textarea name="constraints_json" placeholder='{"min_stake":100}'></textarea></div>
            <div><label><input type="checkbox" name="is_active" value="1" checked> Actif</label></div>
            <div class="actions"><button type="submit">Creer template</button></div>
        </form>
    </section>

    <section class="section">
        <h2>Templates existants</h2>
        @if($templates->count())
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr><th>ID</th><th>Key</th><th>Title</th><th>Type</th><th>Scope</th><th>Target</th><th>Active</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    @foreach($templates as $template)
                        <tr>
                            <td>{{ $template->id }}</td>
                            <td>{{ $template->key }}</td>
                            <td>{{ $template->title }}</td>
                            <td>{{ $template->event_type }}</td>
                            <td>{{ $template->scope }}</td>
                            <td>{{ $template->target_count }}</td>
                            <td>{{ $template->is_active ? 'yes' : 'no' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.missions.update', $template->id) }}" class="inline-form">
                                    @csrf
                                    @method('PUT')
                                    <input name="key" value="{{ $template->key }}" required>
                                    <input name="title" value="{{ $template->title }}" required>
                                    <input name="description" value="{{ $template->description }}">
                                    <input name="event_type" value="{{ $template->event_type }}" required>
                                    <input type="number" name="target_count" min="1" value="{{ $template->target_count }}" required>
                                    <select name="scope" required>
                                        @foreach($scopes as $scope)
                                            <option value="{{ $scope }}" {{ $template->scope === $scope ? 'selected' : '' }}>{{ $scope }}</option>
                                        @endforeach
                                    </select>
                                    <input type="datetime-local" name="start_at" value="{{ optional($template->start_at)->format('Y-m-d\\TH:i') }}">
                                    <input type="datetime-local" name="end_at" value="{{ optional($template->end_at)->format('Y-m-d\\TH:i') }}">
                                    <input type="number" name="rewards_xp" min="0" value="{{ (int) (($template->rewards['xp'] ?? 0)) }}">
                                    <input type="number" name="rewards_rank_points" min="0" value="{{ (int) (($template->rewards['rank_points'] ?? 0)) }}">
                                    <input type="number" name="rewards_reward_points" min="0" value="{{ (int) (($template->rewards['reward_points'] ?? 0)) }}">
                                    <input type="number" name="rewards_bet_points" min="0" value="{{ (int) (($template->rewards['bet_points'] ?? 0)) }}">
                                    <input name="constraints_json" value="{{ $template->constraints ? json_encode($template->constraints, JSON_UNESCAPED_SLASHES) : '' }}">
                                    <label><input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}> actif</label>
                                    <button type="submit">MAJ</button>
                                </form>
                                <form method="POST" action="{{ route('admin.missions.destroy', $template->id) }}" class="inline-form">
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
            <div class="actions">{{ $templates->links() }}</div>
        @else
            <p class="meta">Aucun template mission.</p>
        @endif
    </section>
@endsection

