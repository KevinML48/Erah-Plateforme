<?php

namespace App\Http\Requests\Web;

use App\Models\MatchSelection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'selection_key' => ['required', 'string', Rule::in([
                MatchSelection::KEY_TEAM_A,
                MatchSelection::KEY_TEAM_B,
                MatchSelection::KEY_DRAW,
            ])],
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
            'selection_key' => strtolower(trim((string) $this->input('selection_key'))),
            'idempotency_key' => trim((string) $this->input('idempotency_key')),
        ]);
    }
}
