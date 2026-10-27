<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Service;
use App\Services\PublicCache;
use App\Support\SeoData;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $page = PublicCache::remember('page.services', fn () => Page::forTemplate('services')?->load(['heroImage', 'ogImage']));
        $services = PublicCache::remember('services.all', fn () => Service::published()->with(['image', 'category'])->ordered()->get());

        $seo = ($page ? SeoData::for($page, $page->title, $page->subtitle, $page->heroImage) : new SeoData(title: 'Serviços'))
            ->withBreadcrumbs([
                ['name' => site_text('breadcrumb_home'), 'url' => route('home')],
                ['name' => site_text('nav_services'), 'url' => route('services.index')],
            ]);

        return view('site.services.index', compact('page', 'services', 'seo'));
    }

    public function show(string $slug): View
    {
        $service = Service::published()->where('slug', $slug)->with(['image', 'gallery', 'category', 'ogImage'])->firstOrFail();

        $projects = $service->projects()->published()->with(['coverImage', 'categories'])->latestFirst()->limit(3)->get();
        $otherServices = Service::published()->whereKeyNot($service->id)->ordered()->limit(4)->get();

        $seo = SeoData::for($service, $service->name, $service->excerpt ?? $service->description, $service->image)
            ->withBreadcrumbs([
                ['name' => site_text('breadcrumb_home'), 'url' => route('home')],
                ['name' => site_text('nav_services'), 'url' => route('services.index')],
                ['name' => $service->name, 'url' => $service->url()],
            ])
            ->withSchema([
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => $service->name,
                'description' => $service->excerpt,
                'url' => $service->url(),
                'areaServed' => ['@type' => 'Country', 'name' => 'Moçambique'],
                'provider' => ['@type' => 'Organization', 'name' => setting('company_name', config('app.name')), 'url' => route('home')],
            ]);

        return view('site.services.show', compact('service', 'projects', 'otherServices', 'seo'));
    }
}
