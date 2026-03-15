<?php

namespace App\Http\Requests\Web\Admin;

use App\Models\EsportMatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminMatchRequest extends FormRequest
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
            'event_type' => ['required', 'string', Rule::in(EsportMatch::eventTypes())],
            'event_name' => ['nullable', 'string', 'max:160'],
            'team_a_name' => ['nullable', 'required_if:event_type,'.EsportMatch::EVENT_TYPE_HEAD_TO_HEAD, 'string', 'min:2', 'max:120', 'different:team_b_name'],
            'team_b_name' => ['nullable', 'required_if:event_type,'.EsportMatch::EVENT_TYPE_HEAD_TO_HEAD, 'string', 'min:2', 'max:120'],
            'starts_at' => ['required', 'date'],
            'locked_at' => ['nullable', 'date', 'before:starts_at'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'game_key' => ['required', 'string', Rule::in(EsportMatch::supportedGames())],
            'compétition_name' => ['nullable', 'string', 'max:160'],
            'compétition_stage' => ['nullable', 'string', 'max:120'],
            'compétition_split' => ['nullable', 'string', 'max:120'],
            'best_of' => ['nullable', 'integer', Rule::in(EsportMatch::bestOfOptions())],
            'parent_match_id' => ['nullable', 'integer', 'exists:matches,id'],
            'market_preset' => ['nullable', 'string', 'max:80'],
            'markets' => ['nullable', 'array', 'min:1'],
            'markets.*.key' => ['required_with:markets', 'string', 'max:40'],
            'markets.*.title' => ['required_with:markets', 'string', 'max:120'],
            'markets.*.is_active' => ['nullable', 'boolean'],
            'markets.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'markets.*.selections' => ['required_with:markets', 'array', 'min:1'],
            'markets.*.selections.*.key' => ['required_with:markets', 'string', 'max:20'],
            'markets.*.selections.*.label' => ['required_with:markets', 'string', 'max:120'],
            'markets.*.selections.*.odds' => ['required_with:markets', 'numeric', 'min:1'],
            'markets.*.selections.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'event_type' => strtolower(trim((string) $this->input('event_type', EsportMatch::EVENT_TYPE_HEAD_TO_HEAD))),
            'event_name' => blank($this->input('event_name')) ? null : trim((string) $this->input('event_name')),
            'team_a_name' => blank($this->input('team_a_name')) ? null : trim((string) $this->input('team_a_name')),
            'team_b_name' => blank($this->input('team_b_name')) ? null : trim((string) $this->input('team_b_name')),
            'game_key' => blank($this->input('game_key')) ? null : strtolower(trim((string) $this->input('game_key'))),
            'compétition_name' => blank($this->input('compétition_name')) ? null : trim((string) $this->input('compétition_name')),
            'compétition_stage' => blank($this->input('compétition_stage')) ? null : trim((string) $this->input('compétition_stage')),
            'compétition_split' => blank($this->input('compétition_split')) ? null : trim((string) $this->input('compétition_split')),
            'parent_match_id' => blank($this->input('parent_match_id')) ? null : (int) $this->input('parent_match_id'),
            'best_of' => blank($this->input('best_of')) ? null : (int) $this->input('best_of'),
            'market_preset' => blank($this->input('market_preset')) ? null : trim((string) $this->input('market_preset')),
            'markets' => $this->normalizeMarkets((array) $this->input('markets', [])) ?: null,
        ]);
    }

    /**
     * @param array<int|string, mixed> $markets
     * @return array<int, array<string, mixed>>
     */
    private function normalizeMarkets(array $markets): array
    {
        $normalized = [];

        foreach ($markets as $marketIndex => $market) {
            if (! is_array($market)) {
                continue;
            }

            $row = [
                'key' => strtoupper(trim((string) ($market['key'] ?? ''))),
                'title' => trim((string) ($market['title'] ?? '')),
                'is_active' => filter_var($market['is_active'] ?? true, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true,
                'sort_order' => isset($market['sort_order']) && $market['sort_order'] !== '' ? (int) $market['sort_order'] : (int) $marketIndex,
                'selections' => [],
            ];

            foreach ((array) ($market['selections'] ?? []) as $selectionIndex => $selection) {
                if (! is_array($selection)) {
                    continue;
                }

                $row['selections'][] = [
                    'key' => strtolower(trim((string) ($selection['key'] ?? ''))),
                    'label' => trim((string) ($selection['label'] ?? '')),
                    'odds' => blank($selection['odds'] ?? null) ? null : (float) $selection['odds'],
                    'sort_order' => isset($selection['sort_order']) && $selection['sort_order'] !== ''
                        ? (int) $selection['sort_order']
                        : (int) $selectionIndex,
                ];
            }

            $normalized[] = $row;
        }

        return $normalized;
    }
}
