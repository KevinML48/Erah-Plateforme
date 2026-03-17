<?php

namespace App\Http\Requests\Web\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendAdminOutboundEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('admin-emails.send');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'confirm_token' => ['required', 'string', 'max:100'],
        ];
    }
}