<?php

namespace App\Http\Requests\Web\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GrantWalletRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000'],
            'reason' => ['required', 'string', 'min:3', 'max:191'],
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'reason' => trim((string) $this->input('reason')),
            'idempotency_key' => trim((string) $this->input('idempotency_key')),
        ]);
    }
}
