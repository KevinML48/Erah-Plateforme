<?php

namespace App\Http\Requests\Web\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class GalleryPhotoUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:4000'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'filter_key' => ['nullable', 'string', 'max:64'],
            'filter_label' => ['nullable', 'string', 'max:120'],
            'category_label' => ['nullable', 'string', 'max:120'],
            'cursor_label' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:99999'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'media_file' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'file',
                'max:51200',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif,image/avif,video/mp4,video/webm',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->filled('title') ? trim((string) $this->input('title')) : null,
            'description' => $this->filled('description') ? trim((string) $this->input('description')) : null,
            'alt_text' => $this->filled('alt_text') ? trim((string) $this->input('alt_text')) : null,
            'filter_key' => $this->filled('filter_key') ? trim((string) $this->input('filter_key')) : null,
            'filter_label' => $this->filled('filter_label') ? trim((string) $this->input('filter_label')) : null,
            'category_label' => $this->filled('category_label') ? trim((string) $this->input('category_label')) : null,
            'cursor_label' => $this->filled('cursor_label') ? trim((string) $this->input('cursor_label')) : null,
            'sort_order' => $this->input('sort_order', 0),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
