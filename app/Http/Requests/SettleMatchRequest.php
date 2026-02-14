<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettleMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->isAdmin());
    }

    public function rules(): array
    {
        return [
            'markets' => ['required', 'array', 'min:1'],
            'markets.*.market_id' => ['required', 'integer', 'exists:markets,id'],
            'markets.*.winner_option_id' => ['nullable', 'integer', 'exists:market_options,id'],
        ];
    }
}

