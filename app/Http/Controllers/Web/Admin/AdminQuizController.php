<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\UpsertQuizRequest;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminQuizController extends Controller
{
    public function store(UpsertQuizRequest $request): RedirectResponse
    {
        $questions = $this->decodeQuestions($request->validated('questions_json'));
        if ($questions === []) {
            return back()->with('error', 'Questions JSON invalide.');
        }

        $quiz = Quiz::query()->create($this->payload($request, null));
        $this->syncQuestions($quiz, $questions);

        return back()->with('success', 'Quiz cree.');
    }

    public function update(UpsertQuizRequest $request, int $quizId): RedirectResponse
    {
        $questions = $this->decodeQuestions($request->validated('questions_json'));
        if ($questions === []) {
            return back()->with('error', 'Questions JSON invalide.');
        }

        $quiz = Quiz::query()->findOrFail($quizId);
        $quiz->fill($this->payload($request, $quizId))->save();
        $this->syncQuestions($quiz, $questions);

        return back()->with('success', 'Quiz mis a jour.');
    }

    public function destroy(int $quizId): RedirectResponse
    {
        $quiz = Quiz::query()->findOrFail($quizId);
        $quiz->delete();

        return back()->with('success', 'Quiz supprime.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(UpsertQuizRequest $request, ?int $quizId): array
    {
        $payload = [
            'title' => $request->validated('title'),
            'slug' => $request->validated('slug') ?: Str::slug($request->validated('title')).($quizId ? '-'.$quizId : ''),
            'description' => $request->validated('description'),
            'intro' => $request->validated('intro'),
            'pass_score' => (int) $request->validated('pass_score'),
            'max_attempts_per_user' => $request->validated('max_attempts_per_user'),
            'reward_points' => (int) ($request->validated('reward_points') ?? 0),
            'xp_reward' => (int) ($request->validated('xp_reward') ?? 0),
            'is_active' => $request->boolean('is_active'),
            'starts_at' => $request->validated('starts_at'),
            'ends_at' => $request->validated('ends_at'),
            'mission_template_id' => $request->validated('mission_template_id'),
            'updated_by' => $request->user()->id,
        ];

        if ($quizId === null) {
            $payload['created_by'] = $request->user()->id;
        }

        return $payload;
    }

    /**
     * @param array<int, array<string, mixed>> $questions
     */
    private function syncQuestions(Quiz $quiz, array $questions): void
    {
        DB::transaction(function () use ($quiz, $questions) {
            $quiz->questions()->delete();

            foreach ($questions as $questionIndex => $questionPayload) {
                $question = $quiz->questions()->create([
                    'prompt' => $questionPayload['prompt'],
                    'explanation' => $questionPayload['explanation'] ?? null,
                    'sort_order' => $questionIndex + 1,
                    'points' => (int) ($questionPayload['points'] ?? 1),
                    'is_active' => true,
                ]);

                foreach ((array) $questionPayload['answers'] as $answerIndex => $answerPayload) {
                    $question->answers()->create([
                        'label' => $answerPayload['label'],
                        'is_correct' => (bool) ($answerPayload['is_correct'] ?? false),
                        'sort_order' => $answerIndex + 1,
                    ]);
                }
            }
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function decodeQuestions(string $questionsJson): array
    {
        $decoded = json_decode($questionsJson, true);

        return is_array($decoded) ? array_values($decoded) : [];
    }
}
