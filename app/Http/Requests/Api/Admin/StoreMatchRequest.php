<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'match_key' => ['required', 'string', 'min:4', 'max:80', 'regex:/^[a-z0-9._:-]+$/'],
            'home_team' => ['required', 'string', 'min:2', 'max:120', 'different:away_team'],
            'away_team' => ['required', 'string', 'min:2', 'max:120'],
            'starts_at' => ['required', 'date'],
            'meta' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'match_key' => strtolower(trim((string) $this->input('match_key'))),
            'home_team' => trim((string) $this->input('home_team')),
            'away_team' => trim((string) $this->input('away_team')),
        ]);
    }
}
