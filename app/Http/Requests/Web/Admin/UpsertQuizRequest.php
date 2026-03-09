<?php

namespace App\Http\Requests\Web\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $quizId = $this->route('quizId');

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:160', Rule::unique('quizzes', 'slug')->ignore($quizId)],
            'description' => ['nullable', 'string', 'max:2000'],
            'intro' => ['nullable', 'string', 'max:4000'],
            'pass_score' => ['required', 'integer', 'min:0'],
            'max_attempts_per_user' => ['nullable', 'integer', 'min:1', 'max:100'],
            'reward_points' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'xp_reward' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'mission_template_id' => ['nullable', 'integer', 'exists:mission_templates,id'],
            'questions_json' => ['required', 'string'],
        ];
    }
}
