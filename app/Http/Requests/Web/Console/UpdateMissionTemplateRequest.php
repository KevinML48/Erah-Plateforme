<?php

namespace App\Http\Requests\Web\Console;

use App\Models\MissionTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMissionTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $templateId = (int) $this->route('templateId');

        return [
            'key' => [
                'required',
                'string',
                'min:3',
                'max:120',
                'regex:/^[a-z0-9._-]+$/',
                Rule::unique('mission_templates', 'key')->ignore($templateId),
            ],
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'event_type' => ['required', 'string', 'max:64'],
            'target_count' => ['required', 'integer', 'min:1', 'max:100000'],
            'scope' => ['required', 'string', Rule::in(MissionTemplate::scopes())],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'rewards_xp' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'rewards_rank_points' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'rewards_reward_points' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'rewards_bet_points' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'constraints_json' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

