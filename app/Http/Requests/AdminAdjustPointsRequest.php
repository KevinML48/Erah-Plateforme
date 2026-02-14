<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAdjustPointsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-points') ?? false;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'amount' => ['required', 'integer', 'not_in:0', 'between:-100000,100000'],
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }
}

