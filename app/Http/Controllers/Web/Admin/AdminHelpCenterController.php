<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\Help\UpsertHelpArticleRequest;
use App\Http\Requests\Web\Admin\Help\UpsertHelpCategoryRequest;
use App\Http\Requests\Web\Admin\Help\UpsertHelpGlossaryRequest;
use App\Http\Requests\Web\Admin\Help\UpsertHelpTourStepRequest;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use App\Models\HelpGlossaryTerm;
use App\Models\HelpTourStep;
use App\Services\HelpCenterService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminHelpCenterController extends Controller
{
    public function __construct(private readonly HelpCenterService $helpCenterService)
    {
    }

    public function index(): Response
    {
        Inertia::setRootView('help-center');

        return Inertia::render('Help/Admin/Index', [
            'page' => $this->helpCenterService->adminIndex(),
        ]);
    }

    public function storeCategory(UpsertHelpCategoryRequest $request): RedirectResponse
    {
        HelpCategory::query()->create($request->validated());
        $this->helpCenterService->invalidate();

        return back()->with('success', "Categorie d'aide creee.");
    }

    public function updateCategory(UpsertHelpCategoryRequest $request, HelpCategory $category): RedirectResponse
    {
        $category->fill($request->validated())->save();
        $this->helpCenterService->invalidate();

        return back()->with('success', "Categorie d'aide mise a jour.");
    }

    public function destroyCategory(HelpCategory $category): RedirectResponse
    {
        $category->delete();
        $this->helpCenterService->invalidate();

        return back()->with('success', "Categorie d'aide supprimee.");
    }

    public function storeArticle(UpsertHelpArticleRequest $request): RedirectResponse
    {
        HelpArticle::query()->create($this->articlePayload($request->validated()));
        $this->helpCenterService->invalidate();

        return back()->with('success', "Article d'aide cree.");
    }

    public function updateArticle(UpsertHelpArticleRequest $request, HelpArticle $article): RedirectResponse
    {
        $article->fill($this->articlePayload($request->validated(), $article))->save();
        $this->helpCenterService->invalidate();

        return back()->with('success', "Article d'aide mis a jour.");
    }

    public function destroyArticle(HelpArticle $article): RedirectResponse
    {
        $article->delete();
        $this->helpCenterService->invalidate();

        return back()->with('success', "Article d'aide supprime.");
    }

    public function storeGlossary(UpsertHelpGlossaryRequest $request): RedirectResponse
    {
        HelpGlossaryTerm::query()->create($request->validated());
        $this->helpCenterService->invalidate();

        return back()->with('success', 'Terme du glossaire cree.');
    }

    public function updateGlossary(UpsertHelpGlossaryRequest $request, HelpGlossaryTerm $term): RedirectResponse
    {
        $term->fill($request->validated())->save();
        $this->helpCenterService->invalidate();

        return back()->with('success', 'Terme du glossaire mis a jour.');
    }

    public function destroyGlossary(HelpGlossaryTerm $term): RedirectResponse
    {
        $term->delete();
        $this->helpCenterService->invalidate();

        return back()->with('success', 'Terme du glossaire supprime.');
    }

    public function storeTourStep(UpsertHelpTourStepRequest $request): RedirectResponse
    {
        HelpTourStep::query()->create($request->validated());
        $this->helpCenterService->invalidate();

        return back()->with('success', 'Etape de visite creee.');
    }

    public function updateTourStep(UpsertHelpTourStepRequest $request, HelpTourStep $tourStep): RedirectResponse
    {
        $tourStep->fill($request->validated())->save();
        $this->helpCenterService->invalidate();

        return back()->with('success', 'Etape de visite mise a jour.');
    }

    public function destroyTourStep(HelpTourStep $tourStep): RedirectResponse
    {
        $tourStep->delete();
        $this->helpCenterService->invalidate();

        return back()->with('success', 'Etape de visite supprimee.');
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function articlePayload(array $validated, ?HelpArticle $existing = null): array
    {
        $keywords = collect(explode(',', (string) ($validated['keywords'] ?? '')))
            ->map(fn (string $keyword) => trim($keyword))
            ->filter()
            ->values()
            ->all();

        $isPublished = ($validated['status'] ?? HelpArticle::STATUS_DRAFT) === HelpArticle::STATUS_PUBLISHED;

        return [
            ...$validated,
            'keywords' => $keywords,
            'published_at' => $isPublished
                ? ($existing?->published_at ?? now())
                : null,
        ];
    }
}
