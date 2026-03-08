<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Users\DeleteUserByAdminAction;
use App\Application\Actions\Users\ModeratePublicProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\DeleteUserAccountRequest;
use App\Http\Requests\Web\Admin\ModerateUserPublicProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class PublicProfileModerationController extends Controller
{
    public function update(
        ModerateUserPublicProfileRequest $request,
        User $user,
        ModeratePublicProfileAction $moderatePublicProfileAction
    ): RedirectResponse {
        $moderatePublicProfileAction->execute(
            target: $user,
            actor: $request->user(),
            data: $request->validated(),
            ip: $request->ip(),
        );

        return redirect()
            ->route('users.public', $user)
            ->with('success', 'Profil membre modere.');
    }

    public function destroy(
        DeleteUserAccountRequest $request,
        User $user,
        DeleteUserByAdminAction $deleteUserByAdminAction
    ): RedirectResponse {
        $deletedName = $user->name;

        $deleteUserByAdminAction->execute(
            target: $user,
            actor: $request->user(),
            ip: $request->ip(),
        );

        return redirect()
            ->route('users.index', ['q' => $deletedName])
            ->with('success', 'Compte membre supprime.');
    }
}
