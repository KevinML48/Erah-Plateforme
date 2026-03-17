<?php

namespace App\Http\Requests\Web\Admin;

use App\Models\AdminOutboundEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PreviewAdminOutboundEmailRequest extends FormRequest
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
            'recipient_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'recipient_email' => ['nullable', 'email', 'max:190'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'subject' => ['required', 'string', 'max:190'],
            'body_html' => ['required', 'string', 'max:50000'],
            'category' => ['required', 'string', 'in:'.implode(',', AdminOutboundEmail::categories())],
            'template_key' => ['nullable', 'string', 'max:80'],
            'cc_admin' => ['nullable', 'boolean'],
            'submission_token' => ['required', 'string', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $recipientUserId = $this->input('recipient_user_id');
            $recipientEmail = trim((string) $this->input('recipient_email', ''));

            if (! $recipientUserId && $recipientEmail === '') {
                $validator->errors()->add('recipient_email', 'Selectionnez un utilisateur ou saisissez une adresse email externe.');
            }
        });
    }
}