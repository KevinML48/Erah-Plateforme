<?php

namespace App\Http\Requests\Api;

use App\Domain\Notifications\Enums\NotificationCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferencesRequest extends FormRequest
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
            'channels' => ['sometimes', 'array'],
            'channels.email_opt_in' => ['sometimes', 'boolean'],
            'channels.push_opt_in' => ['sometimes', 'boolean'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['array'],
            'categories.*.email_enabled' => ['sometimes', 'boolean'],
            'categories.*.push_enabled' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $categories = (array) $this->input('categories', []);
            $allowed = NotificationCategory::values();

            foreach (array_keys($categories) as $category) {
                if (! in_array((string) $category, $allowed, true)) {
                    $validator->errors()->add(
                        'categories.'.$category,
                        'Unknown notification category.'
                    );
                }
            }
        });
    }
}
