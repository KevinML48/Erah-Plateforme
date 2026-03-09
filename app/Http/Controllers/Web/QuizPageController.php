<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreQuizAttemptRequest;
use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuizPageController extends Controller
{
    public function index(): View
    {
        $quizzes = Quiz::query()
            ->active()
            ->withCount(['questions', 'attempts'])
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('pages.quizzes.index', [
            'quizzes' => $quizzes,
        ]);
    }

    public function show(string $slug): View
    {
        $quiz = Quiz::query()
            ->active()
            ->where('slug', $slug)
            ->with([
                'questions.answers',
                'attempts' => fn ($query) => $query
                    ->where('user_id', auth()->id())
                    ->latest('finished_at')
                    ->limit(5),
            ])
            ->firstOrFail();

        return view('pages.quizzes.show', [
            'quiz' => $quiz,
            'recentAttempts' => $quiz->attempts,
        ]);
    }

    public function attempt(StoreQuizAttemptRequest $request, string $slug, QuizService $quizService): RedirectResponse
    {
        $quiz = Quiz::query()->where('slug', $slug)->firstOrFail();

        try {
            $attempt = $quizService->attempt(
                user: $request->user(),
                quiz: $quiz,
                answers: $request->validated('answers'),
            );
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with(
            'success',
            $attempt->passed
                ? 'Quiz valide avec '.$attempt->score.' point(s).'
                : 'Quiz termine: '.$attempt->score.' / '.$attempt->max_score.'.'
        );
    }
}
