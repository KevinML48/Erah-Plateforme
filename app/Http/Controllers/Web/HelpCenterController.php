<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\HelpCenterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HelpCenterController extends Controller
{
    public function __construct(private readonly HelpCenterService $helpCenterService)
    {
    }

    public function index(Request $request): Response
    {
        return $this->renderPage($request, 'public', 'pages.help.index');
    }

    public function category(string $slug): RedirectResponse
    {
        return redirect()->to(route('help.index', ['category' => $slug]).'#faq-center');
    }

    public function article(string $slug): RedirectResponse
    {
        return redirect()->to(route('help.index', ['article' => $slug]).'#faq-center');
    }

    public function console(Request $request): Response
    {
        return $this->renderPage($request, 'console', 'pages.help.index');
    }

    public function assistant(Request $request): Response
    {
        return $this->renderPage($request, 'public', 'pages.help.assistant');
    }

    public function consoleAssistant(Request $request): Response
    {
        return $this->renderPage($request, 'console', 'pages.help.assistant');
    }

    private function renderPage(Request $request, string $mode, string $view): Response
    {
        $page = $mode === 'console'
            ? $this->helpCenterService->consoleIndex(
                search: $request->string('search')->toString(),
                categorySlug: $request->string('category')->toString(),
                articleSlug: $request->string('article')->toString(),
                user: $request->user(),
            )
            : $this->helpCenterService->publicIndex(
                search: $request->string('search')->toString(),
                categorySlug: $request->string('category')->toString(),
                articleSlug: $request->string('article')->toString(),
                user: $request->user(),
            );

        return response()
            ->view($view, [
                'page' => $page,
                'mode' => $mode,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
