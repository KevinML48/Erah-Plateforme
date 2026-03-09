<?php

namespace App\Http\Requests\Web\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertPlatformEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $eventId = $this->route('eventId');

        return [
            'key' => ['required', 'string', 'max:120', Rule::unique('events', 'key')->ignore($eventId)],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', 'string', Rule::in(['double_xp', 'bonus_duel', 'bonus_clips'])],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'hidden'])],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'config' => ['nullable', 'string'],
        ];
    }
}
