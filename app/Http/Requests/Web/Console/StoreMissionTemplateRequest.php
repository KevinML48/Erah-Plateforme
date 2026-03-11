<?php

namespace App\Http\Requests\Web\Console;

use App\Models\MissionTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMissionTemplateRequest extends FormRequest
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
        return [
            'key' => ['required', 'string', 'min:3', 'max:120', 'regex:/^[a-z0-9._-]+$/', Rule::unique('mission_templates', 'key')],
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'short_description' => ['nullable', 'string', 'max:280'],
            'description' => ['nullable', 'string', 'max:5000'],
            'long_description' => ['nullable', 'string', 'max:12000'],
            'category' => ['nullable', 'string', 'max:60'],
            'type' => ['nullable', 'string', 'max:40'],
            'event_type' => ['required', 'string', 'max:64'],
            'target_count' => ['required', 'integer', 'min:1', 'max:100000'],
            'scope' => ['required', 'string', Rule::in(MissionTemplate::scopes())],
            'difficulty' => ['nullable', 'string', 'max:40'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'rewards_xp' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'rewards_points' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'constraints_json' => ['nullable', 'string'],
            'prerequisites_json' => ['nullable', 'string'],
            'ui_meta_json' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:120'],
            'badge_label' => ['nullable', 'string', 'max:80'],
            'is_discovery' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_repeatable' => ['nullable', 'boolean'],
            'requires_claim' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
