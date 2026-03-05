<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Application\Actions\Notifications\EnsureNotificationSettingsAction;
use App\Application\Actions\Notifications\MarkNotificationReadAction;
use App\Application\Actions\Notifications\UpdateNotificationPreferencesAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NotificationsPageController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $allowedCategories = NotificationCategory::values();
        $allowedStates = ['all', 'unread', 'read'];

        $category = (string) $request->query('category', 'all');
        if (! in_array($category, $allowedCategories, true)) {
            $category = 'all';
        }

        $legacyOnlyUnread = $request->boolean('unread');
        $state = (string) $request->query('state', $legacyOnlyUnread ? 'unread' : 'all');
        if (! in_array($state, $allowedStates, true)) {
            $state = 'all';
        }

        $baseQuery = Notification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id');

        $query = clone $baseQuery;

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        if ($state === 'unread') {
            $query->whereNull('read_at');
        } elseif ($state === 'read') {
            $query->whereNotNull('read_at');
        }

        $totalCount = (clone $baseQuery)->count();
        $unreadCount = (clone $baseQuery)->whereNull('read_at')->count();
        $filteredCount = (clone $query)->count();

        $categoryCounts = Notification::query()
            ->where('user_id', $user->id)
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('pages.notifications.index', [
            'notifications' => $query->paginate(20)->withQueryString(),
            'onlyUnread' => $state === 'unread',
            'filters' => [
                'state' => $state,
                'category' => $category,
            ],
            'summary' => [
                'total' => (int) $totalCount,
                'unread' => (int) $unreadCount,
                'read' => (int) max(0, $totalCount - $unreadCount),
                'filtered' => (int) $filteredCount,
            ],
            'categoryCounts' => $categoryCounts,
            'stateCounts' => [
                'all' => (int) $totalCount,
                'unread' => (int) $unreadCount,
                'read' => (int) max(0, $totalCount - $unreadCount),
            ],
        ]);
    }

    public function read(int $notificationId, MarkNotificationReadAction $markNotificationReadAction): RedirectResponse
    {
        $markNotificationReadAction->execute(auth()->user(), $notificationId);

        return back()->with('success', 'Notification marquee comme lue.');
    }

    public function readAll(StoreAuditLogAction $storeAuditLogAction): RedirectResponse
    {
        $user = auth()->user();
        DB::transaction(function () use ($user, $storeAuditLogAction) {
            Notification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $storeAuditLogAction->execute(
                action: 'notifications.read_all',
                actor: $user,
                target: $user,
                context: [],
            );
        });

        return back()->with('success', 'Toutes les notifications sont lues.');
    }

    public function preferences(EnsureNotificationSettingsAction $ensureNotificationSettingsAction): View
    {
        $user = auth()->user();
        $ensureNotificationSettingsAction->execute($user);
        $user->load(['notificationChannels', 'notificationPreferences']);

        $preferences = $user->notificationPreferences
            ->keyBy('category');

        return view('pages.notifications.preferences', [
            'channels' => $user->notificationChannels,
            'preferences' => $preferences,
            'hasActiveDevice' => $user->devices()->where('is_active', true)->exists(),
        ]);
    }

    public function updatePreferences(
        Request $request,
        UpdateNotificationPreferencesAction $updateNotificationPreferencesAction
    ): RedirectResponse {
        $categories = NotificationCategory::values();

        $request->validate([
            'email_opt_in' => ['nullable', 'boolean'],
            'push_opt_in' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'channels' => [
                'email_opt_in' => $request->boolean('email_opt_in'),
                'push_opt_in' => $request->boolean('push_opt_in'),
            ],
            'categories' => [],
        ];

        foreach ($categories as $category) {
            $payload['categories'][$category] = [
                'email_enabled' => $request->boolean($category.'_email'),
                'push_enabled' => $request->boolean($category.'_push'),
            ];
        }

        $updateNotificationPreferencesAction->execute(auth()->user(), $payload);

        $routeName = request()->routeIs('app.*')
            ? 'app.notifications.preferences'
            : 'notifications.preferences';

        return redirect()->route($routeName)
            ->with('success', 'Preferences enregistrees.');
    }
}
