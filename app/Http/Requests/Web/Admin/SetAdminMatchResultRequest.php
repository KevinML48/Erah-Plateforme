<?php

namespace App\Http\Requests\Web\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SetAdminMatchResultRequest extends FormRequest
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
            'result' => ['required', 'string', 'max:40'],
            'team_a_score' => ['nullable', 'integer', 'min:0', 'max:20'],
            'team_b_score' => ['nullable', 'integer', 'min:0', 'max:20'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'result' => strtolower(trim((string) $this->input('result'))),
            'team_a_score' => blank($this->input('team_a_score')) ? null : (int) $this->input('team_a_score'),
            'team_b_score' => blank($this->input('team_b_score')) ? null : (int) $this->input('team_b_score'),
        ]);
    }
}
