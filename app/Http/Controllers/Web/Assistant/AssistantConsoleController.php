<?php

namespace App\Http\Controllers\Web\Assistant;

use App\Http\Controllers\Controller;
use App\Services\AI\AssistantConsolePageService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssistantConsoleController extends Controller
{
    public function __construct(
        private readonly AssistantConsolePageService $assistantConsolePageService,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        Inertia::setRootView('help-center');

        return Inertia::render('Assistant/Show', [
            'page' => $this->assistantConsolePageService->build(
                user: $request->user(),
                conversationId: $request->integer('conversation') ?: null,
                articleSlug: $request->string('article')->toString(),
                prompt: $request->string('prompt')->toString() ?: null,
            ),
        ]);
    }
}
