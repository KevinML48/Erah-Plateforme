<?php

namespace App\Http\Requests\Web\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDuelResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'winner_user_id' => ['required', 'integer', 'exists:users,id'],
            'challenger_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'challenged_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
