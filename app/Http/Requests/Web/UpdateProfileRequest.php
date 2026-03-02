<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'twitter_url' => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\/(www\.)?(twitter\.com|x\.com)\//i'],
            'instagram_url' => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\/(www\.)?instagram\.com\//i'],
            'tiktok_url' => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\/(www\.)?tiktok\.com\//i'],
            'discord_url' => ['nullable', 'url', 'max:255', 'regex:/^https?:\/\/(www\.)?(discord\.gg|discord(app)?\.com)\//i'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'twitter_url.regex' => 'Le lien Twitter/X doit etre sur twitter.com ou x.com.',
            'instagram_url.regex' => 'Le lien Instagram doit etre sur instagram.com.',
            'tiktok_url.regex' => 'Le lien TikTok doit etre sur tiktok.com.',
            'discord_url.regex' => 'Le lien Discord doit etre sur discord.gg ou discord.com.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $fields = ['name', 'bio', 'twitter_url', 'instagram_url', 'tiktok_url', 'discord_url'];
        $data = [];

        foreach ($fields as $field) {
            $value = $this->input($field);

            if (! is_string($value)) {
                continue;
            }

            $trimmed = trim($value);
            $data[$field] = $trimmed === '' ? null : $trimmed;
        }

        $this->merge($data);
    }
}

