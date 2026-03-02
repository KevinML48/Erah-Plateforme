<?php

namespace App\Domain\Notifications\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendSystemNotificationStubAction
{
    public function execute(User $user, string $category, string $message, array $context = []): void
    {
        Log::info('notification.stub', [
            'user_id' => $user->id,
            'category' => $category,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
