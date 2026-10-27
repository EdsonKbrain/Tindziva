@use('App\Support\Text')

<x-site.layout :seo="$seo">
    <x-site.page-hero :eyebrow="$page?->section('intro.eyebrow', site_text('nav_portfolio')) ?? site_text('nav_portfolio')" :title="Text::accent($page?->title ?? 'Trabalho que *comunica*')" :subtitle="$activeCategory?->description ?? $page?->subtitle"
        :breadcrumbs="[site_text('breadcrumb_home') => route('home'), site_text('nav_portfolio') => route('portfolio.index')]" :image="$page?->heroImage" />

    <section class="container-site py-16 lg:py-20">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <nav aria-label="Filtrar por categoria" class="-mx-4 overflow-x-auto px-4 lg:mx-0 lg:px-0">
                <ul class="flex gap-2 whitespace-nowrap">
                    <li>
                        <a href="{{ route('portfolio.index', array_filter(['q' => request('q')])) }}" @class([
                            'inline-flex rounded-full px-5 py-2.5 text-sm font-medium transition',
                            'bg-brown-800 text-white' => ! $activeCategory,
                            'bg-cream text-brown-800 hover:bg-brown-100' => $activeCategory,
                        ])>{{ site_text('filter_all') }}</a>
                    </li>
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('portfolio.index', array_filter(['categoria' => $category->slug, 'q' => request('q')])) }}" @class([
                                'inline-flex rounded-full px-5 py-2.5 text-sm font-medium transition',
                                'bg-brown-800 text-white' => $activeCategory?->is($category),
                                'bg-cream text-brown-800 hover:bg-brown-100' => ! $activeCategory?->is($category),
                            ]) @if ($activeCategory?->is($category)) aria-current="true" @endif>{{ $category->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            <form method="GET" action="{{ route('portfolio.index') }}" role="search" class="relative w-full lg:w-80">
                @if ($activeCategory)
                    <input type="hidden" name="categoria" value="{{ $activeCategory->slug }}">
                @endif
                <label for="portfolio-search" class="sr-only">{{ site_text('portfolio_search') }}</label>
                <x-lucide-search class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink/40" />
                <input id="portfolio-search" type="search" name="q" value="{{ request('q') }}" placeholder="{{ site_text('portfolio_search') }}" class="form-input-site rounded-full pl-12">
            </form>
        </div>

        @if (request('q'))
            <p class="mt-8 text-ink/70">{{ trans_choice(site_text('search_results'), $projects->total()) }} {{ site_text('search_for') }} «<strong class="text-brown-800">{{ request('q') }}</strong>».
                <a href="{{ route('portfolio.index', array_filter(['categoria' => $activeCategory?->slug])) }}" class="text-ochre-700 underline">{{ site_text('search_clear') }}</a></p>
        @endif

        <div class="mt-12 grid gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($projects as $project)
                <x-site.project-card :project="$project" />
            @empty
                <div class="rounded-3xl bg-cream p-12 text-center sm:col-span-2 lg:col-span-3">
                    <x-lucide-images class="mx-auto size-10 text-brown-300" />
                    <p class="mt-4 text-lg text-ink/70">{{ site_text('portfolio_empty') }}</p>
                </div>
            @endforelse
        </div>

        <div class="mt-16">{{ $projects->links() }}</div>
    </section>

    @if (site_enabled('clients_section_show') && $clientGroups->isNotEmpty() && ! request('q') && ! $activeCategory && $projects->onFirstPage())
        <x-site.clients-by-segment :groups="$clientGroups" />
    @endif

    @if ($page?->shows('cta') ?? true)
        <x-site.cta :title="$page?->section('cta.title')" :text="$page?->section('cta.text')" :button-label="$page?->section('cta.button_label')" :button-url="$page?->section('cta.button_url')" />
    @endif
</x-site.layout>
