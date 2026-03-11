<?php

namespace App\Http\Requests\Web\Assistant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateAssistantConversationRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:3', 'max:160'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => Str::of(strip_tags((string) $this->input('title')))
                ->replaceMatches('/\s+/', ' ')
                ->trim()
                ->toString(),
        ]);
    }
}
