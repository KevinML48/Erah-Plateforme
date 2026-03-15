<?php

namespace App\Http\Requests\Web\Assistant;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssistantFavoriteRequest extends FormRequest
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
            'question' => ['required', 'string', 'min:4', 'max:500'],
            'answer' => ['required', 'string', 'min:2', 'max:12000'],
            'details' => ['nullable', 'array', 'max:6'],
            'details.*' => ['required', 'string', 'max:4000'],
            'sources' => ['nullable', 'array', 'max:6'],
            'sources.*.type' => ['nullable', 'string', 'max:40'],
            'sources.*.title' => ['required', 'string', 'max:255'],
            'sources.*.url' => ['nullable', 'string', 'max:1000'],
            'sources.*.category' => ['nullable', 'string', 'max:255'],
            'next_steps' => ['nullable', 'array', 'max:8'],
            'next_steps.*' => ['required', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'question' => trim((string) $this->input('question')),
            'answer' => trim((string) $this->input('answer')),
            'details' => collect($this->input('details', []))
                ->map(fn (mixed $item): string => trim((string) $item))
                ->filter()
                ->values()
                ->all(),
            'sources' => collect($this->input('sources', []))
                ->map(function (mixed $source): array {
                    return [
                        'type' => trim((string) data_get($source, 'type')),
                        'title' => trim((string) data_get($source, 'title')),
                        'url' => trim((string) data_get($source, 'url')),
                        'category' => trim((string) data_get($source, 'category')),
                    ];
                })
                ->filter(fn (array $source): bool => $source['title'] !== '')
                ->values()
                ->all(),
            'next_steps' => collect($this->input('next_steps', []))
                ->map(fn (mixed $item): string => trim((string) $item))
                ->filter()
                ->values()
                ->all(),
        ]);
    }
}
