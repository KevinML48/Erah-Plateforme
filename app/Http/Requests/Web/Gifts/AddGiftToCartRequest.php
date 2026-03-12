<?php

namespace App\Http\Requests\Web\Gifts;

use Illuminate\Foundation\Http\FormRequest;

class AddGiftToCartRequest extends FormRequest
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
            'quantity' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}

