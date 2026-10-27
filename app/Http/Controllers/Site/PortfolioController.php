<?php

namespace App\Http\Controllers\Site;

use App\Enums\ClientSegment;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Page;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Services\PublicCache;
use App\Support\SeoData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'categoria' => ['nullable', 'string', 'max:140'],
        ]);

        $page = PublicCache::remember('page.portfolio', fn () => Page::forTemplate('portfolio')?->load(['heroImage', 'ogImage']));
        $categories = PublicCache::remember('portfolio.categories', fn () => ProjectCategory::whereHas('projects', fn ($query) => $query->published())->orderBy('sort_order')->orderBy('name')->get());
        $activeCategory = $request->filled('categoria') ? $categories->firstWhere('slug', $request->input('categoria')) : null;

        $projects = Project::published()
            ->with(['coverImage', 'categories'])
            ->when($activeCategory, fn ($query) => $query->whereHas('categories', fn ($query) => $query->whereKey($activeCategory->id)))
            ->when($request->filled('q'), fn ($query) => $query->where(fn ($query) => $query
                ->where('title', 'like', '%'.$request->string('q').'%')
                ->orWhere('excerpt', 'like', '%'.$request->string('q').'%')))
            ->latestFirst()
            ->paginate(9)
            ->withQueryString();

        $title = $activeCategory ? "Portfólio: {$activeCategory->name}" : ($page?->title ?? 'Portfólio');
        $seo = ($page ? SeoData::for($page, $title, $activeCategory?->description ?? $page->subtitle, $page->heroImage) : new SeoData(title: $title))
            ->paginate($projects)
            ->withBreadcrumbs([
                ['name' => site_text('breadcrumb_home'), 'url' => route('home')],
                ['name' => site_text('nav_portfolio'), 'url' => route('portfolio.index')],
            ]);
        $seo->noindex = $request->filled('q');

        $clientGroups = PublicCache::remember('portfolio.clients', fn () => Client::visible()
            ->with('logo')
            ->get()
            ->groupBy(fn (Client $client) => $client->segment?->value ?? 'other')
            ->sortBy(function ($clients, string $segment): int {
                $position = array_search(ClientSegment::tryFrom($segment), ClientSegment::cases(), true);

                return $position === false ? PHP_INT_MAX : $position;
            }));

        return view('site.portfolio.index', compact('page', 'projects', 'categories', 'activeCategory', 'seo', 'clientGroups'));
    }

    public function show(string $slug): View
    {
        $project = Project::published()
            ->where('slug', $slug)
            ->with(['coverImage', 'gallery', 'categories', 'services', 'client.logo', 'ogImage'])
            ->firstOrFail();

        $related = Project::published()
            ->whereKeyNot($project->id)
            ->whereHas('categories', fn ($query) => $query->whereIn('project_categories.id', $project->categories->modelKeys()))
            ->with(['coverImage', 'categories'])
            ->latestFirst()
            ->limit(3)
            ->get();

        $seo = SeoData::for($project, $project->title, $project->excerpt ?? $project->description, $project->coverImage)
            ->withBreadcrumbs([
                ['name' => site_text('breadcrumb_home'), 'url' => route('home')],
                ['name' => site_text('nav_portfolio'), 'url' => route('portfolio.index')],
                ['name' => $project->title, 'url' => $project->url()],
            ])
            ->withSchema(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'CreativeWork',
                'name' => $project->title,
                'description' => $project->excerpt,
                'url' => $project->url(),
                'image' => $project->coverImage?->url(),
                'dateCreated' => $project->completed_at?->toDateString(),
                'creator' => ['@type' => 'Organization', 'name' => setting('company_name', config('app.name'))],
            ]));

        return view('site.portfolio.show', compact('project', 'related', 'seo'));
    }
}
