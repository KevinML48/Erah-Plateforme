<?php

namespace App\Http\Requests\Web\Admin;

use App\Models\EsportMatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetAdminMatchResultRequest extends FormRequest
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
            'result' => ['required', 'string', Rule::in(EsportMatch::settlementResults())],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'result' => strtolower(trim((string) $this->input('result'))),
        ]);
    }
}
