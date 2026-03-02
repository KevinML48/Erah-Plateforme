<?php

namespace App\Application\Actions\Clips;

use App\Models\Clip;
use Illuminate\Support\Str;

class BuildUniqueClipSlugAction
{
    public function execute(string $rawSlugSource, ?int $ignoreClipId = null): string
    {
        $baseSlug = Str::slug($rawSlugSource);
        if ($baseSlug === '') {
            $baseSlug = 'clip';
        }

        $baseSlug = Str::limit($baseSlug, 170, '');
        $slug = $baseSlug;
        $counter = 2;

        while ($this->slugExists($slug, $ignoreClipId)) {
            $suffix = '-'.$counter;
            $slug = Str::limit($baseSlug, 191 - strlen($suffix), '').$suffix;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreClipId): bool
    {
        $query = Clip::query()->where('slug', $slug);
        if ($ignoreClipId) {
            $query->where('id', '!=', $ignoreClipId);
        }

        return $query->exists();
    }
}
