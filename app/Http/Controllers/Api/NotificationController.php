<?php

namespace App\Http\Controllers\Api;

use App\Application\Actions\Notifications\MarkNotificationReadAction;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'unread' => ['nullable', 'boolean'],
        ]);

        $limit = (int) ($validated['limit'] ?? 20);
        $unread = filter_var($validated['unread'] ?? false, FILTER_VALIDATE_BOOL);

        $query = Notification::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at');

        if ($unread) {
            $query->whereNull('read_at');
        }

        $rows = $query->limit($limit)->get();

        return response()->json([
            'data' => $rows,
        ]);
    }

    public function read(
        Request $request,
        int $id,
        MarkNotificationReadAction $markNotificationReadAction
    ): JsonResponse {
        try {
            $notification = $markNotificationReadAction->execute($request->user(), $id);
        } catch (ModelNotFoundException) {
            return response()->json([
                'message' => 'Notification not found.',
            ], 404);
        }

        return response()->json([
            'data' => $notification,
        ]);
    }
}
