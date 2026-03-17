<?php

namespace App\Support;

class AdminEmailTemplateCatalog
{
    /**
     * @return array<int, array<string, string>>
     */
    public static function all(): array
    {
        return array_values(config('admin-email-templates.templates', []));
    }

    /**
     * @return array<string, string>|null
     */
    public static function find(?string $key): ?array
    {
        if (! is_string($key) || trim($key) === '') {
            return null;
        }

        $template = config('admin-email-templates.templates.'.$key);

        return is_array($template) ? $template : null;
    }
}