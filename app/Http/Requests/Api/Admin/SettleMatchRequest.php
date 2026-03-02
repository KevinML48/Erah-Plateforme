<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\EsportMatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'result' => ['required', 'string', Rule::in(EsportMatch::settlementResults())],
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'result' => strtolower(trim((string) $this->input('result'))),
            'idempotency_key' => trim((string) $this->input('idempotency_key')),
        ]);
    }
}
