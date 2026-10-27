<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\SeoData;
use Illuminate\View\View;

/**
 * Custom pages created in the CMS.
 */
class PageController extends Controller
{
    public function __invoke(string $slug): View
    {
        $page = Page::published()
            ->where('template', 'default')
            ->where('slug', $slug)
            ->with(['heroImage', 'ogImage'])
            ->firstOrFail();

        $seo = SeoData::for($page, $page->title, $page->subtitle ?? $page->content, $page->heroImage)
            ->withBreadcrumbs([
                ['name' => site_text('breadcrumb_home'), 'url' => route('home')],
                ['name' => $page->title, 'url' => $page->url()],
            ]);

        return view('site.page', compact('page', 'seo'));
    }
}
