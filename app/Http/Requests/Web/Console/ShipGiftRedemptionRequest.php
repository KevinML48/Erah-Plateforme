<?php

namespace App\Http\Requests\Web\Console;

use Illuminate\Foundation\Http\FormRequest;

class ShipGiftRedemptionRequest extends FormRequest
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
            'tracking_code' => ['required', 'string', 'min:3', 'max:255'],
            'tracking_carrier' => ['nullable', 'string', 'max:120'],
            'shipping_note' => ['nullable', 'string', 'max:1500'],
        ];
    }
}

