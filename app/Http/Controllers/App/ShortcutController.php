<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\UpdateShortcutsRequest;
use App\Services\ShortcutService;
use Illuminate\Http\RedirectResponse;

class ShortcutController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->to(route('app.profile').'#profile-shortcuts');
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
            ->back()
            ->with('success', 'Raccourcis mis a jour.');
    }

    public function reset(ShortcutService $shortcutService): RedirectResponse
    {
        $shortcutService->resetForUser(auth()->user());

        return redirect()
            ->back()
            ->with('success', 'Raccourcis reinitialises avec les valeurs par defaut.');
    }
}
