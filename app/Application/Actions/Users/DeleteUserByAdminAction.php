<?php

namespace App\Application\Actions\Users;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Models\Clip;
use App\Models\EsportMatch;
use App\Models\User;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class DeleteUserByAdminAction
{
    public function __construct(
        private readonly StoreAuditLogAction $storeAuditLogAction
    ) {
    }

    public function execute(User $target, User $actor, ?string $ip = null): void
    {
        if ($target->is($actor)) {
            throw ValidationException::withMessages([
                'confirmation_name' => 'Vous ne pouvez pas supprimer votre propre compte depuis ce profil public.',
            ]);
        }

        $avatarPath = (string) ($target->avatar_path ?? '');

        DB::transaction(function () use ($target, $actor, $ip): void {
            $lockedUser = User::query()
                ->whereKey($target->id)
                ->lockForUpdate()
                ->firstOrFail();

            Clip::query()->where('created_by', $lockedUser->id)->update(['created_by' => $actor->id]);
            Clip::query()->where('updated_by', $lockedUser->id)->update(['updated_by' => null]);

            EsportMatch::query()->where('created_by', $lockedUser->id)->update(['created_by' => $actor->id]);
            EsportMatch::query()->where('updated_by', $lockedUser->id)->update(['updated_by' => null]);

            if (Schema::hasTable('club_reviews')) {
                $lockedUser->clubReview()->delete();
            }

            $this->storeAuditLogAction->execute(
                action: 'users.deleted_by_admin',
                actor: $actor,
                target: $lockedUser,
                context: [
                    'ip' => $ip,
                    'reassigned_to_admin_id' => $actor->id,
                ],
            );

            $lockedUser->delete();
        });

        MediaStorage::delete($avatarPath);
    }
}
