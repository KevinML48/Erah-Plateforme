<?php

namespace App\Http\Requests\Web\Admin\Help;

use App\Models\HelpCategory;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpsertHelpCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'slug' => ['nullable', 'string', 'min:3', 'max:180', Rule::unique('help_categories', 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string', 'max:4000'],
            'intro' => ['nullable', 'string', 'max:4000'],
            'icon' => ['nullable', 'string', 'max:60'],
            'landing_bucket' => ['required', 'string', Rule::in(['getting_started', 'understanding_platform', 'technical'])],
            'tutorial_video_url' => ['nullable', 'url', 'max:500'],
            'status' => ['required', 'string', Rule::in(HelpCategory::statuses())],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $title = trim((string) $this->input('title'));
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'title' => $title,
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($title),
            'status' => strtolower(trim((string) $this->input('status', HelpCategory::STATUS_DRAFT))),
            'sort_order' => (int) $this->input('sort_order', 0),
        ]);
    }
}
