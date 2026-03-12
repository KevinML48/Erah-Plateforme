<?php

namespace App\Http\Requests\Web\Admin;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactMessageStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(ContactMessage::statuses())],
        ];
    }
}

