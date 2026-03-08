<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpsertClubReviewRequest extends FormRequest
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
            'content' => ['required', 'string', 'min:20', 'max:1200'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $content = $this->input('content');

        $this->merge([
            'content' => is_string($content)
                ? preg_replace("/\n{3,}/", "\n\n", trim(str_replace(["\r\n", "\r"], "\n", $content)))
                : null,
        ]);
    }
}
