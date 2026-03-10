<?php

namespace App\Http\Requests\Web\Admin\Help;

use App\Models\HelpArticle;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpsertHelpArticleRequest extends FormRequest
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
        $articleId = $this->route('article')?->id;

        return [
            'help_category_id' => ['required', 'integer', 'exists:help_categories,id'],
            'title' => ['required', 'string', 'min:3', 'max:180'],
            'slug' => ['nullable', 'string', 'min:3', 'max:200', Rule::unique('help_articles', 'slug')->ignore($articleId)],
            'summary' => ['nullable', 'string', 'max:4000'],
            'body' => ['required', 'string', 'min:20', 'max:20000'],
            'short_answer' => ['nullable', 'string', 'max:4000'],
            'keywords' => ['nullable', 'string', 'max:1000'],
            'tutorial_video_url' => ['nullable', 'url', 'max:500'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'string', Rule::in(HelpArticle::statuses())],
            'is_featured' => ['nullable', 'boolean'],
            'is_faq' => ['nullable', 'boolean'],
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
            'summary' => filled($this->input('summary')) ? trim((string) $this->input('summary')) : null,
            'short_answer' => filled($this->input('short_answer')) ? trim((string) $this->input('short_answer')) : null,
            'keywords' => filled($this->input('keywords')) ? trim((string) $this->input('keywords')) : null,
            'cta_label' => filled($this->input('cta_label')) ? trim((string) $this->input('cta_label')) : null,
            'cta_url' => filled($this->input('cta_url')) ? trim((string) $this->input('cta_url')) : null,
            'status' => strtolower(trim((string) $this->input('status', HelpArticle::STATUS_DRAFT))),
            'is_featured' => $this->boolean('is_featured'),
            'is_faq' => $this->boolean('is_faq'),
            'sort_order' => (int) $this->input('sort_order', 0),
        ]);
    }
}
