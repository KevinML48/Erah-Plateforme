<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Assistant\StoreAssistantFavoriteRequest;
use App\Models\AssistantFavorite;
use App\Services\AI\Exceptions\AssistantFavoritesUnavailableException;
use App\Services\AI\AssistantFavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AssistantFavoriteController extends Controller
{
    public function __construct(
        private readonly AssistantFavoriteService $assistantFavoriteService,
    ) {
    }

    public function store(StoreAssistantFavoriteRequest $request): JsonResponse
    {
        try {
            $favorite = $this->assistantFavoriteService->store($request->user(), $request->validated());
        } catch (AssistantFavoritesUnavailableException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        }

        return response()->json([
            'data' => [
                'favorite' => [
                    'id' => $favorite->id,
                    'question' => $favorite->question,
                    'profile_url' => route('profile.show').'#assistant-favorites',
                ],
                'created' => $favorite->wasRecentlyCreated,
            ],
        ]);
    }

    public function destroy(Request $request, AssistantFavorite $favorite): RedirectResponse|JsonResponse
    {
        try {
            $this->assistantFavoriteService->delete($request->user(), $favorite->id);
        } catch (AssistantFavoritesUnavailableException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $exception->getMessage(),
                ], 503);
            }

            return back()->with('error', $exception->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([], 204);
        }

        return back()->with('success', 'Reponse retiree des favoris.');
    }
}
