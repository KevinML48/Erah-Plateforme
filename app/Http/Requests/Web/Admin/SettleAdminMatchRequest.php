<?php

namespace App\Http\Requests\Web\Admin;

use App\Models\EsportMatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettleAdminMatchRequest extends FormRequest
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
