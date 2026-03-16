<?php

namespace App\Http\Requests\Web\Admin;

use App\Models\GalleryVideo;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GalleryVideoUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === User::ROLE_ADMIN;
    }

    public function rules(): array
    {
        $videoId = $this->route('videoId');

        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable',
                'string',
                'max:180',
                Rule::unique('gallery_videos', 'slug')->ignore($videoId),
            ],
            'excerpt' => ['nullable', 'string', 'max:320'],
            'description' => ['nullable', 'string', 'max:4000'],
            'platform' => ['nullable', Rule::in([
                GalleryVideo::PLATFORM_YOUTUBE,
                GalleryVideo::PLATFORM_TWITCH,
                GalleryVideo::PLATFORM_OTHER,
            ])],
            'video_url' => ['required', 'url', 'max:2048'],
            'embed_url' => ['nullable', 'url', 'max:2048'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096'],
            'preview_video_url' => ['nullable', 'url', 'max:2048'],
            'preview_video_file' => ['nullable', 'file', 'mimetypes:video/mp4,application/mp4', 'max:102400'],
            'preview_video_webm_url' => ['nullable', 'url', 'max:2048'],
            'category_key' => ['nullable', 'string', 'max:64'],
            'category_label' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in([
                GalleryVideo::STATUS_DRAFT,
                GalleryVideo::STATUS_PUBLISHED,
                GalleryVideo::STATUS_ARCHIVED,
            ])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'slug' => $this->filled('slug') ? trim((string) $this->input('slug')) : null,
            'excerpt' => $this->filled('excerpt') ? trim((string) $this->input('excerpt')) : null,
            'description' => $this->filled('description') ? trim((string) $this->input('description')) : null,
            'platform' => $this->filled('platform') ? trim((string) $this->input('platform')) : null,
            'video_url' => trim((string) $this->input('video_url')),
            'embed_url' => $this->filled('embed_url') ? trim((string) $this->input('embed_url')) : null,
            'thumbnail_url' => $this->filled('thumbnail_url') ? trim((string) $this->input('thumbnail_url')) : null,
            'preview_video_url' => $this->filled('preview_video_url') ? trim((string) $this->input('preview_video_url')) : null,
            'preview_video_webm_url' => $this->filled('preview_video_webm_url') ? trim((string) $this->input('preview_video_webm_url')) : null,
            'category_key' => $this->filled('category_key') ? trim((string) $this->input('category_key')) : null,
            'category_label' => $this->filled('category_label') ? trim((string) $this->input('category_label')) : null,
            'status' => $this->filled('status') ? (string) $this->input('status') : null,
            'sort_order' => $this->filled('sort_order') ? (int) $this->input('sort_order') : null,
            'is_featured' => $this->has('is_featured') ? $this->boolean('is_featured') : null,
        ]);
    }
}