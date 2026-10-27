<?php

namespace App\View\Composers;

use App\Models\Page;
use App\Models\Service;
use App\Services\PublicCache;
use Illuminate\View\View;

/**
 * Shares the CMS-managed menu entries with the public header and footer.
 */
class SiteNavigationComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'navServices' => PublicCache::remember('nav.services', fn () => Service::published()->ordered()->get(['id', 'name', 'slug', 'icon'])),
            'menuPages' => PublicCache::remember('nav.pages', fn () => Page::published()
                ->where('template', 'default')
                ->where('show_in_menu', true)
                ->orderBy('sort_order')
                ->get(['id', 'title', 'slug', 'template'])),
        ]);
    }
}
