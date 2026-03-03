<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\UpdateShortcutsRequest;
use App\Services\ShortcutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShortcutController extends Controller
{
    public function index(ShortcutService $shortcutService): View
    {
        $user = auth()->user();
        $current = $shortcutService->getForUser($user);
        $available = $shortcutService->getAvailableForUser($user);

        return view('pages.shortcuts.index', [
            'current' => $current,
            'available' => $available,
            'maxShortcuts' => $shortcutService->maxShortcuts(),
            'minShortcuts' => $shortcutService->minShortcuts(),
        ]);
    }

    public function update(
        UpdateShortcutsRequest $request,
        ShortcutService $shortcutService
    ): RedirectResponse {
        $shortcutService->saveForUser(
            user: $request->user(),
            keys: $request->orderedShortcutKeys(),
        );

        return redirect()
            ->route('app.shortcuts.index')
            ->with('success', 'Raccourcis mis a jour.');
    }

    public function reset(ShortcutService $shortcutService): RedirectResponse
    {
        $shortcutService->resetForUser(auth()->user());

        return redirect()
            ->route('app.shortcuts.index')
            ->with('success', 'Raccourcis reinitialises avec les valeurs par defaut.');
    }
}

