<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\DailyMissionCapExceededException;
use App\Models\User;
use App\Models\UserEvent;
use Throwable;

class EventTrackingService
{
    public function __construct(
        private readonly MissionEngineService $missionEngineService
    ) {
    }

    public function track(User $user, string $eventKey, mixed $value = null): UserEvent
    {
        $encodedValue = $this->encodeValue($value);

        // Generic anti-spam dedupe for identical events in short window.
        $recentDuplicate = UserEvent::query()
            ->where('user_id', $user->id)
            ->where('event_key', $eventKey)
            ->where('event_value', $encodedValue)
            ->where('occurred_at', '>=', now()->subSeconds(5))
            ->exists();

        if ($recentDuplicate) {
            return UserEvent::query()
                ->where('user_id', $user->id)
                ->where('event_key', $eventKey)
                ->where('event_value', $encodedValue)
                ->latest('id')
                ->firstOrFail();
        }

        $event = UserEvent::query()->create([
            'user_id' => $user->id,
            'event_key' => $eventKey,
            'event_value' => $encodedValue,
            'occurred_at' => now(),
        ]);

        try {
            $this->missionEngineService->onEvent($user, $event);
        } catch (DailyMissionCapExceededException $exception) {
            report($exception);
        } catch (Throwable $exception) {
            // Tracking must never break user flows.
            report($exception);
        }

        return $event;
    }

    public function trackPageView(User $user, string $routeOrKey): UserEvent
    {
        return $this->track($user, 'page_viewed', $routeOrKey);
    }

    public function trackAction(User $user, string $key, mixed $value = null): UserEvent
    {
        return $this->track($user, $key, $value);
    }

    private function encodeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
