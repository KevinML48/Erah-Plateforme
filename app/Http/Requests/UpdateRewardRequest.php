<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Reward;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRewardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('manage-rewards'));
    }

    public function rules(): array
    {
        /** @var Reward|null $reward */
        $reward = $this->route('reward');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'alpha_dash', Rule::unique('rewards', 'slug')->ignore($reward?->id)],
            'description' => ['nullable', 'string'],
            'points_cost' => ['sometimes', 'integer', 'min:1', 'max:1000000'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'is_active' => ['sometimes', 'boolean'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}

