<?php

namespace App\Http\Requests\Web\Console;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGiftConsoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $giftId = $this->route('giftId');
        return [
            'title' => ['required', 'string', 'min:2', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'cost_points' => ['required', 'integer', 'min:1', 'max:1000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'slug' => ['nullable', 'string', 'min:3', 'max:160', Rule::unique('gifts', 'slug')->ignore($giftId)],
            'short_description' => ['nullable', 'string', 'max:280'],
            'long_description' => ['nullable', 'string', 'max:12000'],
            'category' => ['nullable', 'string', 'max:40'],
            'type' => ['nullable', 'string', 'max:60'],
            'delivery_type' => ['nullable', 'string', 'max:40'],
            'delivery_details' => ['nullable', 'string', 'max:2000'],
            'eligibility_details' => ['nullable', 'string', 'max:2000'],
            'conditions' => ['nullable', 'string', 'max:4000'],
            'gallery_urls' => ['nullable', 'string', 'max:8000'],
            'meta_title' => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'requires_admin_validation' => ['nullable', 'boolean'],
            'supporter_only' => ['nullable', 'boolean'],
            'is_repeatable' => ['nullable', 'boolean'],
        ];
    }
}
