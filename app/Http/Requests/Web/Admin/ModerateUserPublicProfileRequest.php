<?php

namespace App\Http\Requests\Web\Admin;

use App\Http\Requests\Web\UpdateProfileRequest;
use App\Models\ClubReview;
use App\Models\User;
use Illuminate\Validation\Rule;

class ModerateUserPublicProfileRequest extends UpdateProfileRequest
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
        return array_merge(parent::rules(), [
            'remove_avatar' => ['nullable', 'boolean'],
            'clear_social_links' => ['nullable', 'boolean'],
            'review_status' => ['nullable', 'string', Rule::in(ClubReview::statuses())],
            'delete_review' => ['nullable', 'boolean'],
        ]);
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([
            'remove_avatar' => $this->boolean('remove_avatar'),
            'clear_social_links' => $this->boolean('clear_social_links'),
            'delete_review' => $this->boolean('delete_review'),
            'review_status' => filled($this->input('review_status'))
                ? strtolower(trim((string) $this->input('review_status')))
                : null,
        ]);
    }
}
