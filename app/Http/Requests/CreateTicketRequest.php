<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'stake_points' => ['required', 'integer', 'min:1'],
            'selections' => ['required', 'array', 'min:1'],
            'selections.*' => ['required', 'integer', 'distinct'],
        ];
    }
}

