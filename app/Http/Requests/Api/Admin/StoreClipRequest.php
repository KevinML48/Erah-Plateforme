<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClipRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'slug' => ['nullable', 'string', 'min:3', 'max:191'],
            'description' => ['nullable', 'string', 'max:5000'],
            'video_url' => ['required', 'url', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'slug' => trim((string) $this->input('slug')),
            'description' => $this->filled('description') ? trim((string) $this->input('description')) : null,
            'video_url' => trim((string) $this->input('video_url')),
            'thumbnail_url' => $this->filled('thumbnail_url') ? trim((string) $this->input('thumbnail_url')) : null,
        ]);
    }
}
