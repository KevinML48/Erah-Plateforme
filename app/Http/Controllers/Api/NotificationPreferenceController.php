<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Notifications\EnsureNotificationSettingsAction;
use App\Application\Actions\Notifications\UpdateNotificationPreferencesAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateNotificationPreferencesRequest;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function show(Request $request, EnsureNotificationSettingsAction $ensureNotificationSettingsAction): JsonResponse
    {
        $user = $request->user();
        $ensureNotificationSettingsAction->execute($user);

        return response()->json($this->buildResponse($user));
    }

    public function update(
        UpdateNotificationPreferencesRequest $request,
        UpdateNotificationPreferencesAction $updateNotificationPreferencesAction
    ): JsonResponse {
        $updateNotificationPreferencesAction->execute($request->user(), $request->validated());

        return response()->json($this->buildResponse($request->user()));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildResponse(User $user): array
    {
        $channels = UserNotificationChannel::query()->where('user_id', $user->id)->firstOrFail();
        $préférences = NotificationPréférence::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('category');

        $categories = [];
        foreach (NotificationCategory::values() as $category) {
            $pref = $préférences->get($category);
            $categories[$category] = [
                'email_enabled' => $pref?->email_enabled ?? true,
                'push_enabled' => $pref?->push_enabled ?? true,
            ];
        }

        return [
            'channels' => [
                'email_opt_in' => $channels->email_opt_in,
                'push_opt_in' => $channels->push_opt_in,
            ],
            'categories' => $categories,
        ];
    }
}
