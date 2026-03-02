<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClipRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:160'],
            'slug' => ['sometimes', 'nullable', 'string', 'min:3', 'max:191'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'video_url' => ['sometimes', 'required', 'url', 'max:2048'],
            'thumbnail_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $payload = [];
        foreach (['title', 'slug', 'description', 'video_url', 'thumbnail_url'] as $field) {
            if ($this->has($field)) {
                $value = $this->input($field);
                $payload[$field] = is_string($value) ? trim($value) : $value;
            }
        }

        $this->merge($payload);
    }
}
