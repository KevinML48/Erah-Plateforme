<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserProfileCosmetic;
use App\Services\ProfileCosmeticService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class ProfileCosmeticController extends Controller
{
    public function equip(
        Request $request,
        UserProfileCosmetic $cosmetic,
        ProfileCosmeticService $profileCosmeticService
    ): RedirectResponse {
        abort_unless((int) $cosmetic->user_id === (int) $request->user()->id, 403);

        try {
            $profileCosmeticService->equip($request->user(), $cosmetic);
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Element de profil equipe.');
    }
}
