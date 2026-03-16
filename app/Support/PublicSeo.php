<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class PublicSeo
{
    /**
     * @return list<string>
     */
    public function sitemapPaths(): array
    {
        return collect([
            '/',
            '/aide',
            '/galerie-photos',
            '/contact',
            '/supporter',
            '/avis',
            '/app/clips',
            '/app/matchs',
            '/app/classement',
            '/app/statistics',
            '/app/duels/classement',
        ])->merge($this->marketingPages())
            ->unique()
            ->values()
            ->all();
    }

    public function renderSitemap(?string $baseUrl = null): string
    {
        $resolvedBaseUrl = $this->normalizeBaseUrl($baseUrl);
        $lastModified = now()->toDateString();

        $entries = collect($this->sitemapPaths())
            ->map(function (string $path) use ($resolvedBaseUrl, $lastModified): string {
                $loc = htmlspecialchars($resolvedBaseUrl.$path, ENT_XML1);

                return "  <url>\n    <loc>{$loc}</loc>\n    <lastmod>{$lastModified}</lastmod>\n  </url>";
            })
            ->implode("\n");

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$entries}
</urlset>
XML;
    }

    public function renderRobots(?string $baseUrl = null): string
    {
        $resolvedBaseUrl = $this->normalizeBaseUrl($baseUrl);

        return implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /api/',
            'Disallow: /dev/',
            'Disallow: /console/',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /verify-email',
            'Disallow: /confirm-password',
            '',
            'Sitemap: '.$resolvedBaseUrl.'/sitemap.xml',
            '',
        ]);
    }

    /**
     * @return Collection<int, string>
     */
    private function marketingPages(): Collection
    {
        return collect(File::files(resource_path('views/marketing')))
            ->map(fn (\SplFileInfo $file): string => '/'.$file->getBasename('.blade.php'))
            ->reject(fn (string $path): bool => in_array($path, [
                '/404',
                '/contact',
                '/faq',
                '/galerie-photos',
                '/galerie-video',
                '/index',
                '/merci',
            ], true));
    }

    private function normalizeBaseUrl(?string $baseUrl): string
    {
        $configuredUrl = trim((string) ($baseUrl ?? config('app.url')));

        if ($configuredUrl === '') {
            $configuredUrl = 'http://localhost';
        }

        return rtrim($configuredUrl, '/');
    }
}