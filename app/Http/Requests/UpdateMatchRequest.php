<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\MatchStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('manage-match'));
    }

    public function rules(): array
    {
        return [
            'game_id' => ['sometimes', 'nullable', 'integer', 'exists:games,id'],
            'game' => ['sometimes', 'string', 'max:100'],
            'title' => ['sometimes', 'string', 'max:255'],
            'format' => ['sometimes', 'nullable', 'string', 'max:10'],
            'starts_at' => ['sometimes', 'date'],
            'lock_at' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', new Enum(MatchStatus::class)],
            'points_reward' => ['sometimes', 'integer', 'min:0', 'max:100000'],
        ];
    }
}
