<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettleMatchRequest extends FormRequest
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
            'result' => ['required', 'string', 'max:40'],
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
            'team_a_score' => ['nullable', 'integer', 'min:0', 'max:20'],
            'team_b_score' => ['nullable', 'integer', 'min:0', 'max:20'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'result' => strtolower(trim((string) $this->input('result'))),
            'idempotency_key' => trim((string) $this->input('idempotency_key')),
            'team_a_score' => blank($this->input('team_a_score')) ? null : (int) $this->input('team_a_score'),
            'team_b_score' => blank($this->input('team_b_score')) ? null : (int) $this->input('team_b_score'),
        ]);
    }
}
