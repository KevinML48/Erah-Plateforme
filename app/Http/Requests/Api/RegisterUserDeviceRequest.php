<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterUserDeviceRequest extends FormRequest
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
            'platform' => ['required', 'string', Rule::in(['ios', 'android', 'web'])],
            'device_token' => ['required', 'string', 'min:8', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'meta' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'platform' => strtolower(trim((string) $this->input('platform'))),
            'device_token' => trim((string) $this->input('device_token')),
        ]);
    }
}
