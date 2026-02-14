<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\MarketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMarketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->isAdmin());
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', new Enum(MarketStatus::class)],
            'settle_rule' => ['nullable', 'array'],
        ];
    }
}
