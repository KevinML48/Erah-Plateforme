<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Notifications\EnsureNotificationSettingsAction;
use App\Application\Actions\Notifications\UpdateNotificationPreferencesAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(EnsureNotificationSettingsAction $ensureNotificationSettingsAction): View
    {
        /** @var User $user */
        $user = Auth::user();

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

        /** @var User $user */
        $user = Auth::user();

        $updateNotificationPreferencesAction->execute($user, [
            'channels' => [
                'email_opt_in' => $request->boolean('email_opt_in'),
                'push_opt_in' => $request->boolean('push_opt_in'),
            ],
            'categories' => [],
        ]);

        $request->session()->put('onboarding_done', true);

        return redirect()->to(url('/'))
            ->with('success', 'Preferences enregistrees. Votre espace est pret.');
    }
}
