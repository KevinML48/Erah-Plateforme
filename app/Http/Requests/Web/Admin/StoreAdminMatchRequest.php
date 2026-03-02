<?php

namespace App\Http\Requests\Web\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'team_a_name' => ['required', 'string', 'min:2', 'max:120', 'different:team_b_name'],
            'team_b_name' => ['required', 'string', 'min:2', 'max:120'],
            'starts_at' => ['required', 'date'],
            'locked_at' => ['nullable', 'date', 'before:starts_at'],
            'game_key' => ['nullable', 'string', 'max:40'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'team_a_name' => trim((string) $this->input('team_a_name')),
            'team_b_name' => trim((string) $this->input('team_b_name')),
            'game_key' => blank($this->input('game_key')) ? null : strtolower(trim((string) $this->input('game_key'))),
        ]);
    }
}
