<?php

namespace App\Http\Requests\Web\Console;

use Illuminate\Foundation\Http\FormRequest;

class GrantRewardWalletRequest extends FormRequest
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
            'reason' => ['required', 'string', 'max:255'],
            'idempotency_key' => ['required', 'string', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
        ];
    }
}

