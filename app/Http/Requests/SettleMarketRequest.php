<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettleMarketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->isAdmin());
    }

    public function rules(): array
    {
        return [
            'winner_option_id' => ['nullable', 'integer', 'exists:market_options,id'],
        ];
    }
}

