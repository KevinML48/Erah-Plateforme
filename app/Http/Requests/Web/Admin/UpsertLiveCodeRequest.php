<?php

namespace App\Http\Requests\Web\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertLiveCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $liveCodeId = $this->route('liveCodeId');

        return [
            'code' => ['nullable', 'string', 'max:60', Rule::unique('live_codes', 'code')->ignore($liveCodeId)],
            'label' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'hidden'])],
            'reward_points' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'bet_points' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'xp_reward' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'usage_limit' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'per_user_limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'expires_at' => ['nullable', 'date'],
            'mission_template_id' => ['nullable', 'integer', 'exists:mission_templates,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->filled('code') ? strtoupper(trim((string) $this->input('code'))) : null,
        ]);
    }
}
