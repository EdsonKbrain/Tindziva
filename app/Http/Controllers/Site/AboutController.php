<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Page;
use App\Services\PublicCache;
use App\Support\SeoData;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $page = PublicCache::remember('page.about', fn () => Page::forTemplate('about')?->load(['heroImage', 'ogImage']));
        abort_if($page === null, 404);

        $mediaIds = array_filter([
            $page->section('history.image'),
            ...array_column($page->items('team.members'), 'photo'),
        ]);

        $seo = SeoData::for($page, $page->title, $page->subtitle ?? strip_tags((string) $page->section('history.text')), $page->heroImage)
            ->withBreadcrumbs([
                ['name' => site_text('breadcrumb_home'), 'url' => route('home')],
                ['name' => $page->title, 'url' => route('about')],
            ]);

        return view('site.about', [
            'page' => $page,
            'media' => Media::whereIn('id', $mediaIds)->get()->keyBy('id'),
            'seo' => $seo,
        ]);
    }
}
