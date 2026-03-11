<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Notifications\EnsureNotificationSettingsAction;
use App\Application\Actions\Notifications\UpdateNotificationPreferencesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(EnsureNotificationSettingsAction $ensureNotificationSettingsAction): View
    {
        $user = auth()->user();
        $ensureNotificationSettingsAction->execute($user);

        $user->load(['notificationChannels', 'notificationPreferences']);

        return view('pages.onboarding', [
            'channels' => [
                'email_opt_in' => (bool) $user->notificationChannels?->email_opt_in,
                'push_opt_in' => (bool) $user->notificationChannels?->push_opt_in,
            ],
        ]);
    }

    public function store(
        Request $request,
        UpdateNotificationPreferencesAction $updateNotificationPreferencesAction
    ): RedirectResponse {
        $request->validate([
            'email_opt_in' => ['nullable', 'boolean'],
            'push_opt_in' => ['nullable', 'boolean'],
        ]);

        $updateNotificationPreferencesAction->execute(auth()->user(), [
            'channels' => [
                'email_opt_in' => $request->boolean('email_opt_in'),
                'push_opt_in' => $request->boolean('push_opt_in'),
            ],
            'categories' => [],
        ]);

        $request->session()->put('onboarding_done', true);

        return redirect()->route('dashboard')
            ->with('success', 'Preferences enregistrees. Votre espace est pret.');
    }
}
