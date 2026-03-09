<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\UpsertPlatformEventRequest;
use App\Models\PlatformEvent;
use Illuminate\Http\RedirectResponse;

class AdminPlatformEventController extends Controller
{
    public function store(UpsertPlatformEventRequest $request): RedirectResponse
    {
        PlatformEvent::query()->create($this->payload($request));

        return back()->with('success', 'Evenement dynamique cree.');
    }

    public function update(UpsertPlatformEventRequest $request, int $eventId): RedirectResponse
    {
        $event = PlatformEvent::query()->findOrFail($eventId);
        $event->fill($this->payload($request))->save();

        return back()->with('success', 'Evenement dynamique mis a jour.');
    }

    public function destroy(int $eventId): RedirectResponse
    {
        $event = PlatformEvent::query()->findOrFail($eventId);
        $event->delete();

        return back()->with('success', 'Evenement dynamique supprime.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(UpsertPlatformEventRequest $request): array
    {
        $config = null;
        $rawConfig = $request->validated('config');
        if ($rawConfig !== null && trim($rawConfig) !== '') {
            $decoded = json_decode($rawConfig, true);
            if (is_array($decoded)) {
                $config = $decoded;
            }
        }

        return [
            'key' => $request->validated('key'),
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'type' => $request->validated('type'),
            'status' => $request->validated('status'),
            'is_active' => $request->boolean('is_active'),
            'starts_at' => $request->validated('starts_at'),
            'ends_at' => $request->validated('ends_at'),
            'config' => $config,
        ];
    }
}
