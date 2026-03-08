<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class PlaceMatchBetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'market_key' => ['nullable', 'string', 'max:40'],
            'selection_key' => ['required', 'string', 'max:20'],
            'stake_points' => [
                'required',
                'integer',
                'min:'.((int) config('betting.stake.min', 1)),
                'max:'.((int) config('betting.stake.max', 10000)),
            ],
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'market_key' => blank($this->input('market_key')) ? null : strtoupper(trim((string) $this->input('market_key'))),
            'selection_key' => strtolower(trim((string) $this->input('selection_key'))),
            'idempotency_key' => trim((string) $this->input('idempotency_key')),
        ]);
    }
}
