<?php

namespace App\Http\Requests\Api\Admin;

use App\Models\EsportMatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMatchRequest extends FormRequest
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
            'match_key' => ['required', 'string', 'min:4', 'max:80', 'regex:/^[a-z0-9._:-]+$/'],
            'event_type' => ['nullable', 'string', Rule::in(EsportMatch::eventTypes())],
            'game_key' => ['nullable', 'string', Rule::in(EsportMatch::supportedGames())],
            'event_name' => ['nullable', 'string', 'max:160'],
            'competition_name' => ['nullable', 'string', 'max:160'],
            'competition_stage' => ['nullable', 'string', 'max:120'],
            'competition_split' => ['nullable', 'string', 'max:120'],
            'best_of' => ['nullable', 'integer', Rule::in(EsportMatch::bestOfOptions())],
            'parent_match_id' => ['nullable', 'integer', 'exists:matches,id'],
            'home_team' => ['nullable', 'required_if:event_type,'.EsportMatch::EVENT_TYPE_HEAD_TO_HEAD, 'string', 'min:2', 'max:120', 'different:away_team'],
            'away_team' => ['nullable', 'required_if:event_type,'.EsportMatch::EVENT_TYPE_HEAD_TO_HEAD, 'string', 'min:2', 'max:120'],
            'starts_at' => ['required', 'date'],
            'locked_at' => ['nullable', 'date', 'before:starts_at'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'market_preset' => ['nullable', 'string', 'max:80'],
            'markets' => ['nullable', 'array', 'min:1'],
            'meta' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'match_key' => strtolower(trim((string) $this->input('match_key'))),
            'event_type' => blank($this->input('event_type')) ? EsportMatch::EVENT_TYPE_HEAD_TO_HEAD : strtolower(trim((string) $this->input('event_type'))),
            'game_key' => blank($this->input('game_key')) ? null : strtolower(trim((string) $this->input('game_key'))),
            'event_name' => blank($this->input('event_name')) ? null : trim((string) $this->input('event_name')),
            'competition_name' => blank($this->input('competition_name')) ? null : trim((string) $this->input('competition_name')),
            'competition_stage' => blank($this->input('competition_stage')) ? null : trim((string) $this->input('competition_stage')),
            'competition_split' => blank($this->input('competition_split')) ? null : trim((string) $this->input('competition_split')),
            'best_of' => blank($this->input('best_of')) ? null : (int) $this->input('best_of'),
            'parent_match_id' => blank($this->input('parent_match_id')) ? null : (int) $this->input('parent_match_id'),
            'home_team' => blank($this->input('home_team')) ? null : trim((string) $this->input('home_team')),
            'away_team' => blank($this->input('away_team')) ? null : trim((string) $this->input('away_team')),
            'market_preset' => blank($this->input('market_preset')) ? null : trim((string) $this->input('market_preset')),
        ]);
    }
}
