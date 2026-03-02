<?php

namespace App\Http\Requests\Web\Console;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'title' => ['required', 'string', 'min:2', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'cost_points' => ['required', 'integer', 'min:1', 'max:1000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

