<?php

namespace App\Http\Requests\Web\Admin\Help;

use App\Models\HelpGlossaryTerm;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpsertHelpGlossaryRequest extends FormRequest
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
        $termId = $this->route('term')?->id;

        return [
            'term' => ['required', 'string', 'min:2', 'max:140'],
            'slug' => ['nullable', 'string', 'min:2', 'max:180', Rule::unique('help_glossary_terms', 'slug')->ignore($termId)],
            'definition' => ['required', 'string', 'min:10', 'max:4000'],
            'short_answer' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', Rule::in(HelpGlossaryTerm::statuses())],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $term = trim((string) $this->input('term'));
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'term' => $term,
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($term),
            'short_answer' => filled($this->input('short_answer')) ? trim((string) $this->input('short_answer')) : null,
            'status' => strtolower(trim((string) $this->input('status', HelpGlossaryTerm::STATUS_DRAFT))),
            'is_featured' => $this->boolean('is_featured'),
            'sort_order' => (int) $this->input('sort_order', 0),
        ]);
    }
}
