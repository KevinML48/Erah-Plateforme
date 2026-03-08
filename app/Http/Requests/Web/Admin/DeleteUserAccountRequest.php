<?php

namespace App\Http\Requests\Web\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUserAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'confirmation_name' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $targetUser = $this->route('user');

                    if (! $targetUser instanceof User) {
                        $fail('Utilisateur introuvable.');

                        return;
                    }

                    if (trim((string) $value) !== (string) $targetUser->name) {
                        $fail('Saisissez exactement le pseudo actuel pour confirmer la suppression.');
                    }
                },
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'confirmation_name' => trim((string) $this->input('confirmation_name')),
        ]);
    }
}
