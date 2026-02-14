<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipRedemptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('manage-redemptions'));
    }

    public function rules(): array
    {
        return [
            'tracking_code' => ['nullable', 'string', 'max:255'],
        ];
    }
}

