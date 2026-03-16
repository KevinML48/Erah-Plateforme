<?php

namespace App\Http\Requests\Marketing;

use App\Models\ContactMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'category' => ['nullable', Rule::in(ContactMessage::categories())],
            'subject' => ['required', 'string', 'min:3', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['nullable', 'string', 'max:0'],
            'submission_token' => ['required', 'uuid'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $authenticatedEmail = $this->user()?->email;

        $this->merge([
            'name' => trim(strip_tags((string) $this->input('name'))),
            'email' => strtolower(trim((string) ($authenticatedEmail ?: $this->input('email')))),
            'category' => trim((string) $this->input('category')),
            'subject' => trim(strip_tags((string) $this->input('subject'))),
            'message' => trim(strip_tags((string) $this->input('message'))),
            'website' => trim((string) $this->input('website', '')),
            'submission_token' => trim((string) $this->input('submission_token')),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L email est obligatoire.',
            'email.email' => 'L email doit etre valide.',
            'category.in' => 'La categorie selectionnee est invalide.',
            'subject.required' => 'Le sujet est obligatoire.',
            'message.required' => 'Le message est obligatoire.',
            'message.min' => 'Le message doit contenir au moins 10 caracteres.',
            'website.max' => 'Soumission invalide.',
            'submission_token.required' => 'Le formulaire a expire. Rechargez la page puis reessayez.',
            'submission_token.uuid' => 'Le formulaire a expire. Rechargez la page puis reessayez.',
        ];
    }
}
