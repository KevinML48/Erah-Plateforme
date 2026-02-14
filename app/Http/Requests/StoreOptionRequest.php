<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->isAdmin());
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:100'],
            'odds_decimal' => ['nullable', 'numeric', 'min:1', 'max:999.99'],
            'popularity_weight' => ['nullable', 'numeric', 'min:0.5', 'max:2'],
        ];
    }
}

