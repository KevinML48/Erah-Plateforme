<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Clips\CreateClipAction;
use App\Application\Actions\Clips\DeleteClipAction;
use App\Application\Actions\Clips\PublishClipAction;
use App\Application\Actions\Clips\UnpublishClipAction;
use App\Application\Actions\Clips\UpdateClipAction;
use App\Http\Controllers\Controller;
use App\Models\Clip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClipsAdminController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $clips = Clip::query()
            ->when($status === 'published', fn ($query) => $query->where('is_published', true))
            ->when($status === 'draft', fn ($query) => $query->where('is_published', false))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin.clips.index', [
            'clips' => $clips,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.clips.form', [
            'clip' => null,
            'action' => route('admin.clips.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request, CreateClipAction $createClipAction): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'slug' => ['nullable', 'string', 'min:3', 'max:191'],
            'description' => ['nullable', 'string', 'max:5000'],
            'video_url' => ['required', 'url', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $clip = $createClipAction->execute(auth()->user(), $validated);

        return redirect()->route('admin.clips.edit', $clip->id)
            ->with('success', 'Clip cree.');
    }

    public function edit(int $clipId): View
    {
        $clip = Clip::query()->findOrFail($clipId);

        return view('pages.admin.clips.form', [
            'clip' => $clip,
            'action' => route('admin.clips.update', $clip->id),
            'method' => 'PUT',
        ]);
    }

    public function update(
        Request $request,
        int $clipId,
        UpdateClipAction $updateClipAction
    ): RedirectResponse {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'slug' => ['nullable', 'string', 'min:3', 'max:191'],
            'description' => ['nullable', 'string', 'max:5000'],
            'video_url' => ['required', 'url', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $clip = Clip::query()->findOrFail($clipId);
        $updateClipAction->execute(auth()->user(), $clip, $validated);

        return back()->with('success', 'Clip mis a jour.');
    }

    public function publish(int $clipId, PublishClipAction $publishClipAction): RedirectResponse
    {
        $clip = Clip::query()->findOrFail($clipId);
        $publishClipAction->execute(auth()->user(), $clip);

        return back()->with('success', 'Clip publie.');
    }

    public function unpublish(int $clipId, UnpublishClipAction $unpublishClipAction): RedirectResponse
    {
        $clip = Clip::query()->findOrFail($clipId);
        $unpublishClipAction->execute(auth()->user(), $clip);

        return back()->with('success', 'Clip depublie.');
    }

    public function destroy(int $clipId, DeleteClipAction $deleteClipAction): RedirectResponse
    {
        $clip = Clip::query()->findOrFail($clipId);
        $deleteClipAction->execute(auth()->user(), $clip);

        return redirect()->route('admin.clips.index')
            ->with('success', 'Clip supprime.');
    }
}
