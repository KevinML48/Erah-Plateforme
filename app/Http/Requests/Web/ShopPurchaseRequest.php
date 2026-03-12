<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class ShopPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'string', 'min:12', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'idempotency_key' => trim((string) $this->input('idempotency_key')),
        ]);
    }
}

