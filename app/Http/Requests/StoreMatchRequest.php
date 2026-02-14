<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\MatchResult;
use App\Enums\MatchStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('manage-match'));
    }

    public function rules(): array
    {
        return [
            'game_id' => ['nullable', 'integer', 'exists:games,id'],
            'game' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'format' => ['nullable', 'string', 'max:10'],
            'starts_at' => ['required', 'date'],
            'lock_at' => ['nullable', 'date'],
            'status' => ['nullable', new Enum(MatchStatus::class)],
            'result' => ['nullable', new Enum(MatchResult::class)],
            'points_reward' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (!$this->filled('game') && !$this->filled('game_id')) {
                $validator->errors()->add('game', 'Le champ game ou game_id est requis.');
            }
        });
    }
}
