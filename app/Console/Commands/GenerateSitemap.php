<?php

namespace App\Console\Commands;

use App\Support\PublicSeo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'seo:generate-sitemap {--url=} {--path=}';

    protected $description = 'Generate the public sitemap.xml file.';

    public function handle(PublicSeo $publicSeo): int
    {
        $targetPath = (string) ($this->option('path') ?: storage_path('app/seo/sitemap.xml'));
        $directory = dirname($targetPath);

        if (! is_dir($directory)) {
            File::ensureDirectoryExists($directory);
        }

        File::put($targetPath, $publicSeo->renderSitemap((string) $this->option('url')));

        $this->info('Sitemap generated: '.$targetPath);

        return self::SUCCESS;
    }
}
