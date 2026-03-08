<?php

namespace App\Http\Requests\Api;

use App\Models\Bet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceBetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'match_id' => ['required', 'integer', 'exists:matches,id'],
            'market_key' => ['nullable', 'string', 'max:40'],
            'selection_key' => ['required_without:prediction', 'nullable', 'string', 'max:20'],
            'prediction' => ['required_without:selection_key', 'nullable', 'string', Rule::in(Bet::predictions())],
            'stake_points' => ['required', 'integer', 'min:1', 'max:1000000'],
            'idempotency_key' => ['required', 'string', 'min:8', 'max:120', 'regex:/^[A-Za-z0-9._:-]+$/'],
            'meta' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'market_key' => blank($this->input('market_key')) ? null : strtoupper(trim((string) $this->input('market_key'))),
            'selection_key' => blank($this->input('selection_key')) ? null : strtolower(trim((string) $this->input('selection_key'))),
            'prediction' => blank($this->input('prediction')) ? null : strtolower(trim((string) $this->input('prediction'))),
            'idempotency_key' => trim((string) $this->input('idempotency_key')),
        ]);
    }
}
