<?php

namespace App\Http\Requests\Web\Admin;

use App\Models\EsportMatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminMatchStatusRequest extends FormRequest
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
            'status' => ['required', 'string', Rule::in(EsportMatch::statuses())],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => strtolower(trim((string) $this->input('status'))),
        ]);
    }
}
