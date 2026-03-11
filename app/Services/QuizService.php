<?php

namespace App\Services;

use App\Application\Actions\Notifications\NotifyAction;
use App\Domain\Notifications\Enums\NotificationCategory;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class QuizService
{
    public function __construct(
        private readonly RewardGrantService $rewardGrantService,
        private readonly MissionEngine $missionEngine,
        private readonly AchievementService $achievementService,
        private readonly NotifyAction $notifyAction
    ) {
    }

    /**
     * @param array<int|string, mixed> $answers
     */
    public function attempt(User $user, Quiz $quiz, array $answers): QuizAttempt
    {
        return DB::transaction(function () use ($user, $quiz, $answers) {
            $quiz = Quiz::query()
                ->with(['questions.answers'])
                ->whereKey($quiz->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $quiz->is_active) {
                throw new RuntimeException('Quiz indisponible.');
            }

            if ($quiz->starts_at && now()->lt($quiz->starts_at)) {
                throw new RuntimeException('Quiz pas encore ouvert.');
            }

            if ($quiz->ends_at && now()->gt($quiz->ends_at)) {
                throw new RuntimeException('Quiz expire.');
            }

            if ($quiz->max_attempts_per_user !== null) {
                $attemptsCount = QuizAttempt::query()
                    ->where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->count();

                if ($attemptsCount >= $quiz->max_attempts_per_user) {
                    throw new RuntimeException('Limite de tentatives atteinte.');
                }
            }

            $score = 0;
            $maxScore = 0;
            $normalizedAnswers = [];

            foreach ($quiz->questions as $question) {
                if (! $question->is_active) {
                    continue;
                }

                $maxScore += (int) $question->points;
                $selected = $answers[$question->id] ?? null;
                $normalizedAnswers[(string) $question->id] = $selected;

                if ($this->isCorrectAnswer($question, $selected)) {
                    $score += (int) $question->points;
                }
            }

            $attempt = QuizAttempt::query()->create([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'score' => $score,
                'max_score' => $maxScore,
                'passed' => $score >= (int) $quiz->pass_score,
                'answers' => $normalizedAnswers,
                'started_at' => now(),
                'finished_at' => now(),
                'reward_granted_at' => null,
            ]);

            $this->missionEngine->recordEvent($user, 'quiz.attempt', 1, [
                'event_key' => 'quiz.attempt.'.$attempt->id,
                'subject_type' => QuizAttempt::class,
                'subject_id' => (string) $attempt->id,
            ]);

            if ($attempt->passed) {
                $this->rewardGrantService->grant(
                    user: $user,
                    domain: 'quiz',
                    action: 'pass',
                    dedupeKey: 'quiz.pass.'.$user->id.'.'.$quiz->id,
                    rewards: [
                        'xp' => (int) $quiz->xp_reward,
                        'points' => (int) $quiz->reward_points,
                    ],
                    subjectType: Quiz::class,
                    subjectId: (string) $quiz->id,
                );

                $attempt->reward_granted_at = now();
                $attempt->save();
                $this->missionEngine->recordEvent($user, 'quiz.pass', 1, [
                    'event_key' => 'quiz.pass.'.$attempt->id,
                    'subject_type' => QuizAttempt::class,
                    'subject_id' => (string) $attempt->id,
                ]);
            }

            $this->achievementService->sync($user);
            $this->notifyAction->execute(
                user: $user,
                category: NotificationCategory::QUIZ->value,
                title: $attempt->passed ? 'Quiz valide' : 'Quiz termine',
                message: $attempt->passed
                    ? 'Vous validez le quiz "'.$quiz->title.'" avec '.$attempt->score.' point(s).'
                    : 'Quiz "'.$quiz->title.'" termine: '.$attempt->score.' / '.$attempt->max_score.'.',
                data: [
                    'quiz_id' => $quiz->id,
                    'attempt_id' => $attempt->id,
                    'passed' => $attempt->passed,
                    'score' => $attempt->score,
                    'max_score' => $attempt->max_score,
                ],
            );

            return $attempt->fresh();
        });
    }

    private function isCorrectAnswer(QuizQuestion $question, mixed $selected): bool
    {
        if ($question->question_type === QuizQuestion::TYPE_SHORT_TEXT) {
            $expected = $this->normalizeTextAnswer($question->accepted_answer);
            $candidate = $this->normalizeTextAnswer(is_scalar($selected) ? (string) $selected : null);

            return $expected !== null && $candidate !== null && $expected === $candidate;
        }

        $correct = $question->answers->firstWhere('is_correct', true);

        return $correct !== null && (int) $correct->id === (int) $selected;
    }

    private function normalizeTextAnswer(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = mb_strtolower(trim($value));

        return $normalized === '' ? null : preg_replace('/\s+/', ' ', $normalized);
    }
}
