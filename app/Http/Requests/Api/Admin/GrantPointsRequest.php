<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\PointsTransaction;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class GrantPointsRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'kind' => ['required', 'string', Rule::in([PointsTransaction::KIND_XP, PointsTransaction::KIND_RANK])],
            'points' => ['required', 'integer', 'min:1', 'max:1000000'],
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
            'reason' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'kind' => strtolower((string) $this->input('kind')),
            'idempotency_key' => trim((string) $this->input('idempotency_key')),
        ]);
    }
}
