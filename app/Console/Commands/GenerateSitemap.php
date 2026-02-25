<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Office;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap for search engines';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Add static pages
        $sitemap->add(Url::create(route('home'))
            ->setChangeFrequency('weekly')
            ->setPriority(1.0)
            ->setLastModificationDate(now()));

        $sitemap->add(Url::create(route('offices.index'))
            ->setChangeFrequency('daily')
            ->setPriority(0.9)
            ->setLastModificationDate(now()));

        $sitemap->add(Url::create(route('contact'))
            ->setChangeFrequency('monthly')
            ->setPriority(0.7)
            ->setLastModificationDate(now()));

        $sitemap->add(Url::create(route('privacy'))
            ->setChangeFrequency('monthly')
            ->setPriority(0.5)
            ->setLastModificationDate(now()));

        $sitemap->add(Url::create(route('terms'))
            ->setChangeFrequency('monthly')
            ->setPriority(0.5)
            ->setLastModificationDate(now()));

        // Add all active offices
        Office::where('is_active', true)
            ->select('id', 'slug', 'updated_at')
            ->chunk(100, function ($offices) use ($sitemap) {
                foreach ($offices as $office) {
                    $sitemap->add(Url::create(route('offices.show', $office))
                        ->setChangeFrequency('weekly')
                        ->setPriority(0.8)
                        ->setLastModificationDate($office->updated_at));
                }
            });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
}
