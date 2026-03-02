<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateDuelRequest extends FormRequest
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
            'challenged_user_id' => ['required', 'integer', 'exists:users,id', 'different:auth_user_id'],
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
            'message' => ['nullable', 'string', 'max:1000'],
            'expires_in_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'idempotency_key' => trim((string) $this->input('idempotency_key')),
            'message' => $this->filled('message') ? trim((string) $this->input('message')) : null,
            'expires_in_minutes' => $this->filled('expires_in_minutes') ? (int) $this->input('expires_in_minutes') : null,
            'auth_user_id' => $this->user()?->id,
        ]);
    }
}
