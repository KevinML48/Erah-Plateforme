<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Notifications\EnsureNotificationSettingsAction;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __invoke(EnsureNotificationSettingsAction $ensureNotificationSettingsAction): View
    {
        $user = auth()->user();
        $ensureNotificationSettingsAction->execute($user);
        $user->load(['notificationChannels', 'notificationPreferences']);

        return view('pages.settings.index', [
            'user' => $user,
        ]);
    }
}
