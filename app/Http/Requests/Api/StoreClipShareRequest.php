<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClipShareRequest extends FormRequest
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
            'channel' => [
                'nullable',
                'string',
                Rule::in(['link', 'discord', 'x', 'telegram', 'whatsapp', 'other']),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'channel' => strtolower(trim((string) $this->input('channel', 'link'))),
        ]);
    }
}
