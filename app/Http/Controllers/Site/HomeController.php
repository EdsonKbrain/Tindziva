<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Services\PublicCache;
use App\Support\SeoData;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $data = PublicCache::remember('home', fn (): array => [
            'page' => Page::forTemplate('home')?->load('ogImage'),
            'services' => Service::published()->with('image')->ordered()->limit(8)->get(),
            'projects' => Project::published()->where('is_featured', true)->with(['coverImage', 'categories'])->latestFirst()->limit(6)->get(),
            'clients' => Client::visible()->with('logo')->whereNotNull('logo_id')->limit(40)->get(),
            'posts' => Post::published()->with(['coverImage', 'category', 'author'])->latest('published_at')->limit(3)->get(),
        ]);

        $page = $data['page'];

        $seo = $page
            ? SeoData::for($page, (string) setting('seo_title', $page->title), $page->section('hero.text'))
            : new SeoData;
        $seo->canonical = route('home');

        return view('site.home', [...$data, 'seo' => $seo, 'heroImage' => $this->media($page?->section('hero.image')), 'aboutImage' => $this->media($page?->section('about.image'))]);
    }

    private function media(mixed $id): ?Media
    {
        return $id ? Media::find($id) : null;
    }
}
