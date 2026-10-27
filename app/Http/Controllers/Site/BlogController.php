<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Services\PublicCache;
use App\Support\SeoData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request);
    }

    public function category(Request $request, string $slug): View
    {
        $category = PostCategory::where('slug', $slug)->firstOrFail();

        return $this->listing($request, category: $category);
    }

    public function tag(Request $request, string $slug): View
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        return $this->listing($request, tag: $tag);
    }

    public function show(string $slug): View
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->with(['author', 'category', 'tags', 'coverImage', 'ogImage'])
            ->firstOrFail();

        $seo = SeoData::for($post, $post->title, $post->excerpt ?? $post->content, $post->coverImage);
        $seo->type = 'article';
        $seo->publishedTime = $post->published_at->toIso8601String();
        $seo->modifiedTime = $post->updated_at->toIso8601String();
        $seo->withBreadcrumbs([
            ['name' => site_text('breadcrumb_home'), 'url' => route('home')],
            ['name' => site_text('nav_blog'), 'url' => route('blog.index')],
            ['name' => $post->title, 'url' => $post->url()],
        ])->withSchema(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $seo->description,
            'image' => $post->coverImage?->url(),
            'datePublished' => $seo->publishedTime,
            'dateModified' => $seo->modifiedTime,
            'mainEntityOfPage' => $post->url(),
            'author' => ['@type' => 'Person', 'name' => $post->author?->name ?? setting('company_name', config('app.name'))],
            'publisher' => [
                '@type' => 'Organization',
                'name' => setting('company_name', config('app.name')),
                'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo-tindziva.png')],
            ],
            'keywords' => $post->tags->pluck('name')->implode(', ') ?: null,
        ]));

        return view('site.blog.show', [
            'post' => $post,
            'related' => $this->related($post),
            'seo' => $seo,
            'isPreview' => false,
        ]);
    }

    private function listing(Request $request, ?PostCategory $category = null, ?Tag $tag = null): View
    {
        $request->validate(['q' => ['nullable', 'string', 'max:100']]);

        $isFiltered = $category || $tag || $request->filled('q');
        $page = PublicCache::remember('page.blog', fn () => Page::forTemplate('blog')?->load(['heroImage', 'ogImage']));

        $featured = ! $isFiltered && $request->integer('page', 1) === 1
            ? PublicCache::remember('blog.featured', fn () => Post::published()->where('is_featured', true)->with(['coverImage', 'category', 'author'])->latest('published_at')->limit(3)->get())
            : new EloquentCollection;

        $posts = Post::published()
            ->with(['coverImage', 'category', 'author'])
            ->when($category, fn (Builder $query) => $query->where('post_category_id', $category->id))
            ->when($tag, fn (Builder $query) => $query->whereHas('tags', fn (Builder $query) => $query->whereKey($tag->id)))
            ->when($request->filled('q'), fn (Builder $query) => $query->where(fn (Builder $query) => $query
                ->where('title', 'like', '%'.$request->string('q').'%')
                ->orWhere('excerpt', 'like', '%'.$request->string('q').'%')
                ->orWhere('content', 'like', '%'.$request->string('q').'%')))
            ->whereNotIn('id', $featured->modelKeys())
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $title = match (true) {
            $category !== null => "Blog: {$category->name}",
            $tag !== null => "Artigos sobre {$tag->name}",
            default => $page?->title ?? 'Blog',
        };

        $seo = ($page ? SeoData::for($page, $title, $category?->description ?? $page->subtitle, $page->heroImage) : new SeoData(title: $title))
            ->paginate($posts)
            ->withBreadcrumbs(array_values(array_filter([
                ['name' => site_text('breadcrumb_home'), 'url' => route('home')],
                ['name' => site_text('nav_blog'), 'url' => route('blog.index')],
                $category ? ['name' => $category->name, 'url' => route('blog.category', $category->slug)] : null,
                $tag ? ['name' => $tag->name, 'url' => route('blog.tag', $tag->slug)] : null,
            ])));
        $seo->noindex = $request->filled('q');

        return view('site.blog.index', [
            'page' => $page,
            'posts' => $posts,
            'featured' => $featured,
            'categories' => PublicCache::remember('blog.categories', fn () => PostCategory::whereHas('posts', fn (Builder $query) => $query->published())->orderBy('sort_order')->orderBy('name')->get()),
            'activeCategory' => $category,
            'activeTag' => $tag,
            'title' => $title,
            'seo' => $seo,
        ]);
    }

    /**
     * @return Collection<int, Post>
     */
    private function related(Post $post): Collection
    {
        $tagIds = $post->tags->modelKeys();

        return Post::published()
            ->whereKeyNot($post->id)
            ->where(fn (Builder $query) => $query
                ->when($post->post_category_id, fn (Builder $query) => $query->where('post_category_id', $post->post_category_id))
                ->when($tagIds, fn (Builder $query) => $query->orWhereHas('tags', fn (Builder $query) => $query->whereIn('tags.id', $tagIds))))
            ->with(['coverImage', 'category', 'author'])
            ->latest('published_at')
            ->limit(3)
            ->get();
    }
}
