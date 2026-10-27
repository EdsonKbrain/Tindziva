@use('App\Support\Text')

<x-site.layout :seo="$seo">
    <x-site.page-hero :eyebrow="$page?->section('intro.eyebrow', site_text('nav_blog')) ?? site_text('nav_blog')" :image="$page?->heroImage" :title="$activeCategory || $activeTag ? e($title) : Text::accent($page?->title ?? 'Ideias que *inspiram*')"
        :subtitle="$activeCategory?->description ?? (! $activeTag ? $page?->subtitle : null)"
        :breadcrumbs="array_filter([site_text('breadcrumb_home') => route('home'), site_text('nav_blog') => route('blog.index')] + ($activeCategory ? [$activeCategory->name => route('blog.category', $activeCategory->slug)] : []) + ($activeTag ? [$activeTag->name => route('blog.tag', $activeTag->slug)] : []))" />

    <section class="container-site py-16 lg:py-20">
        @if ($featured->isNotEmpty())
            <div class="mb-20 space-y-16 border-b border-brown-100 pb-20">
                <x-site.post-card :post="$featured->first()" featured />
                @if ($featured->count() > 1)
                    <div class="grid gap-x-8 gap-y-12 md:grid-cols-2">
                        @foreach ($featured->skip(1) as $post)
                            <x-site.post-card :post="$post" />
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <nav aria-label="Categorias do blog" class="-mx-4 overflow-x-auto px-4 lg:mx-0 lg:px-0">
                <ul class="flex gap-2 whitespace-nowrap">
                    <li><a href="{{ route('blog.index') }}" @class(['inline-flex rounded-full px-5 py-2.5 text-sm font-medium transition', 'bg-brown-800 text-white' => ! $activeCategory, 'bg-cream text-brown-800 hover:bg-brown-100' => $activeCategory])>{{ site_text('filter_all') }}</a></li>
                    @foreach ($categories as $category)
                        <li><a href="{{ route('blog.category', $category->slug) }}" @class(['inline-flex rounded-full px-5 py-2.5 text-sm font-medium transition', 'bg-brown-800 text-white' => $activeCategory?->is($category), 'bg-cream text-brown-800 hover:bg-brown-100' => ! $activeCategory?->is($category)])>{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </nav>
            <form method="GET" action="{{ url()->current() }}" role="search" class="relative w-full lg:w-80">
                <label for="blog-search" class="sr-only">{{ site_text('blog_search') }}</label>
                <x-lucide-search class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink/40" />
                <input id="blog-search" type="search" name="q" value="{{ request('q') }}" placeholder="{{ site_text('blog_search') }}" class="form-input-site rounded-full pl-12">
            </form>
        </div>

        @if (request('q'))
            <p class="mt-8 text-ink/70">{{ trans_choice(site_text('search_results'), $posts->total()) }} {{ site_text('search_for') }} «<strong class="text-brown-800">{{ request('q') }}</strong>». <a href="{{ url()->current() }}" class="text-ochre-700 underline">{{ site_text('search_clear') }}</a></p>
        @endif

        <div class="mt-12 grid gap-x-8 gap-y-14 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                <x-site.post-card :post="$post" />
            @empty
                @if ($featured->isEmpty())
                    <div class="rounded-3xl bg-cream p-12 text-center md:col-span-2 lg:col-span-3">
                        <x-lucide-newspaper class="mx-auto size-10 text-brown-300" />
                        <p class="mt-4 text-lg text-ink/70">{{ site_text('blog_empty') }}</p>
                    </div>
                @endif
            @endforelse
        </div>

        <div class="mt-16">{{ $posts->links() }}</div>
    </section>
</x-site.layout>
