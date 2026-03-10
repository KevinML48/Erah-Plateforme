<?php

namespace App\Http\Requests\Web\Admin\Help;

use App\Models\HelpTourStep;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertHelpTourStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $stepId = $this->route('tourStep')?->id;

        return [
            'step_number' => ['required', 'integer', 'min:1', 'max:6', Rule::unique('help_tour_steps', 'step_number')->ignore($stepId)],
            'title' => ['required', 'string', 'min:3', 'max:180'],
            'summary' => ['required', 'string', 'min:10', 'max:2000'],
            'body' => ['required', 'string', 'min:20', 'max:8000'],
            'visual_title' => ['nullable', 'string', 'max:180'],
            'visual_body' => ['nullable', 'string', 'max:2000'],
            'cta_label' => ['required', 'string', 'max:120'],
            'cta_url' => ['required', 'string', 'max:500'],
            'tutorial_video_url' => ['nullable', 'url', 'max:500'],
            'status' => ['required', 'string', Rule::in(HelpTourStep::statuses())],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'visual_title' => filled($this->input('visual_title')) ? trim((string) $this->input('visual_title')) : null,
            'visual_body' => filled($this->input('visual_body')) ? trim((string) $this->input('visual_body')) : null,
            'status' => strtolower(trim((string) $this->input('status', HelpTourStep::STATUS_DRAFT))),
            'sort_order' => (int) $this->input('sort_order', $this->input('step_number', 0)),
        ]);
    }
}
