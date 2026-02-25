<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Static pages
        $staticPages = [
            [
                'url' => route('home'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'url' => route('offices.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'url' => route('contact'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'url' => route('privacy'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'url' => route('terms'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
        ];

        // Active offices
        $offices = Office::where('is_active', true)
            ->select('id', 'slug', 'updated_at')
            ->get()
            ->map(fn (Office $office) => [
                'url' => route('offices.show', $office),
                'lastmod' => $office->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ])
            ->toArray();

        $pages = array_merge($staticPages, $offices);

        $xml = view('sitemap', ['pages' => $pages])->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function offices()
    {
        $today = now()->toDateString();

        $offices = Office::where('is_active', true)
            ->select('id', 'slug', 'updated_at')
            ->get()
            ->map(fn (Office $office) => [
                'url' => route('offices.show', $office),
                'lastmod' => $office->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ])
            ->toArray();

        $xml = view('sitemap', ['pages' => $offices])->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
