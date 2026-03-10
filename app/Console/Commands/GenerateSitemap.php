<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'seo:generate-sitemap {--url=}';

    protected $description = 'Generate the public sitemap.xml file.';

    public function handle(): int
    {
        $baseUrl = rtrim((string) ($this->option('url') ?: config('app.url')), '/');
        $urls = collect([
            '/',
            '/faq',
            '/galerie-photos',
            '/contact',
            '/supporter',
            '/avis',
            '/app/clips',
            '/app/matchs',
            '/app/classement',
            '/app/statistics',
            '/app/duels/classement',
            '/console/clips',
            '/console/matches',
            '/console/leaderboards',
            '/console/statistics',
            '/console/duels/classement',
        ])->merge($this->marketingPages())->unique()->values();

        $xml = $this->renderXml($baseUrl, $urls->all());
        File::put(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap generated: '.public_path('sitemap.xml'));

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function marketingPages(): array
    {
        return collect(File::files(resource_path('views/marketing')))
            ->map(fn (\SplFileInfo $file): string => '/'.$file->getBasename('.blade.php'))
            ->reject(fn (string $path): bool => in_array($path, [
                '/404',
                '/index',
                '/faq',
                '/contact',
                '/galerie-photos',
            ], true))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function renderXml(string $baseUrl, array $paths): string
    {
        $entries = collect($paths)->map(function (string $path) use ($baseUrl): string {
            $loc = htmlspecialchars($baseUrl.$path, ENT_XML1);

            return "  <url>\n    <loc>{$loc}</loc>\n  </url>";
        })->implode("\n");

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$entries}
</urlset>
XML;
    }
}
