<?php

namespace App\Http\Requests\Web\Console;

use Illuminate\Foundation\Http\FormRequest;

class GrantRankingPointsRequest extends FormRequest
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
            'kind' => ['required', 'string', 'in:xp,rank'],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000'],
            'source_type' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9._:-]+$/'],
            'source_id' => ['required', 'string', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
        ];
    }
}

