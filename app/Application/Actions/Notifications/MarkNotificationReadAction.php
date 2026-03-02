<?php

namespace App\Application\Actions\Notifications;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class MarkNotificationReadAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $user, int $notificationId): Notification
    {
        return DB::transaction(function () use ($user, $notificationId) {
            $notification = Notification::query()
                ->where('id', $notificationId)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (! $notification) {
                throw (new ModelNotFoundException())->setModel(Notification::class, [$notificationId]);
            }

            if (! $notification->read_at) {
                $notification->read_at = now();
                $notification->save();

                $this->storeAuditLogAction->execute(
                    action: 'notifications.read',
                    actor: $user,
                    target: $notification,
                    context: [],
                );
            }

            return $notification;
        });
    }
}
