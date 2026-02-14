<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\MatchResult;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CompleteMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->can('manage-match'));
    }

    public function rules(): array
    {
        return [
            'result' => ['required', new Enum(MatchResult::class)],
        ];
    }
}
