<?php

namespace App\Http\Requests\Web\Assistant;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssistantMessageRequest extends FormRequest
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
            'message' => ['required', 'string', 'min:2', 'max:4000'],
            'conversation_id' => ['nullable', 'integer'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'message' => trim((string) $this->input('message')),
        ]);
    }
}
