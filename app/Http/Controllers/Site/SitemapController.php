<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\Service;
use App\Services\PublicCache;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = PublicCache::remember('sitemap', function (): string {
            $urls = collect([
                ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'weekly'],
                ['loc' => route('about'), 'priority' => '0.8'],
                ['loc' => route('services.index'), 'priority' => '0.9'],
                ['loc' => route('portfolio.index'), 'priority' => '0.9', 'changefreq' => 'weekly'],
                ['loc' => route('blog.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
                ['loc' => route('contact'), 'priority' => '0.7'],
            ]);

            Service::published()->get(['slug', 'updated_at'])->each(fn (Service $service) => $urls->push(['loc' => $service->url(), 'lastmod' => $service->updated_at, 'priority' => '0.8']));
            Project::published()->get(['slug', 'updated_at'])->each(fn (Project $project) => $urls->push(['loc' => $project->url(), 'lastmod' => $project->updated_at, 'priority' => '0.7']));
            Post::published()->get(['slug', 'updated_at'])->each(fn (Post $post) => $urls->push(['loc' => $post->url(), 'lastmod' => $post->updated_at, 'priority' => '0.6']));
            PostCategory::whereHas('posts', fn ($query) => $query->published())->get(['slug'])->each(fn (PostCategory $category) => $urls->push(['loc' => route('blog.category', $category->slug), 'priority' => '0.5']));
            Page::published()->where('template', 'default')->get(['slug', 'template', 'updated_at'])->each(fn (Page $page) => $urls->push(['loc' => $page->url(), 'lastmod' => $page->updated_at, 'priority' => '0.5']));

            return view('site.sitemap', ['urls' => $urls])->render();
        }, 3600);

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function robots(): Response
    {
        $lines = app()->isProduction()
            ? ['User-agent: *', 'Disallow: /admin', 'Disallow: /*?q=', '', 'Sitemap: '.route('sitemap')]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
