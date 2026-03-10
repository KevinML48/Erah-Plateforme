<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class AskHelpAssistantRequest extends FormRequest
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
            'message' => ['required', 'string', 'min:4', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'message' => trim((string) $this->input('message')),
        ]);
    }
}
