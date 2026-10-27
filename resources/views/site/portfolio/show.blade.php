<x-site.layout :seo="$seo">
    <section class="relative overflow-hidden bg-cream">
        <div class="container-site relative pt-12 pb-10 lg:pt-16">
            <nav aria-label="Navegação estrutural">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm text-ink/60">
                    <li><a href="{{ route('home') }}" class="hover:text-ochre-700">{{ site_text('breadcrumb_home') }}</a></li>
                    <li><x-lucide-chevron-right class="size-3.5" /></li>
                    <li><a href="{{ route('portfolio.index') }}" class="hover:text-ochre-700">{{ site_text('nav_portfolio') }}</a></li>
                    <li><x-lucide-chevron-right class="size-3.5" /></li>
                    <li aria-current="page" class="text-brown-800">{{ $project->title }}</li>
                </ol>
            </nav>
            <div class="mt-8 grid gap-8 lg:grid-cols-12 lg:items-end">
                <div class="lg:col-span-8">
                    @if ($project->categories->isNotEmpty())
                        <p class="eyebrow">{{ $project->categories->pluck('name')->join(' · ') }}</p>
                    @endif
                    <h1 class="mt-4 text-4xl font-semibold sm:text-5xl lg:text-6xl">{{ $project->title }}</h1>
                    @if ($project->excerpt)
                        <p class="mt-6 max-w-2xl text-lg text-ink/70 sm:text-xl">{{ $project->excerpt }}</p>
                    @endif
                </div>
                <dl class="grid grid-cols-2 gap-6 text-sm lg:col-span-4">
                    @if ($client = $project->publicClient())
                        <div><dt class="text-ink/50">{{ site_text('project_client') }}</dt><dd class="mt-1 font-semibold text-brown-800">{{ $client->name }}</dd></div>
                    @endif
                    @if ($project->completed_at)
                        <div><dt class="text-ink/50">{{ site_text('project_year') }}</dt><dd class="mt-1 font-semibold text-brown-800">{{ $project->completed_at->year }}</dd></div>
                    @endif
                    @if ($project->location)
                        <div><dt class="text-ink/50">{{ site_text('project_location') }}</dt><dd class="mt-1 font-semibold text-brown-800">{{ $project->location }}</dd></div>
                    @endif
                    @if ($project->services->isNotEmpty())
                        <div><dt class="text-ink/50">{{ site_text('project_services') }}</dt><dd class="mt-1 font-semibold text-brown-800">{{ $project->services->pluck('name')->join(', ') }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>
        <div class="container-site relative pb-0">
            <x-site.picture :media="$project->coverImage" :label="$project->title" eager sizes="(min-width: 1280px) 1216px, 100vw" class="aspect-[16/9] w-full translate-y-12 rounded-[2rem] shadow-2xl shadow-brown-900/15 lg:aspect-[21/9]" />
        </div>
    </section>

    <section class="container-site pt-28 pb-20 lg:pb-24">
        <div class="grid gap-14 lg:grid-cols-12">
            <div class="prose-tindziva reveal lg:col-span-8">
                @richtext($project->description)
            </div>
            <aside class="space-y-8 lg:col-span-3 lg:col-start-10">
                @if ($credits = $project->publicCredits())
                    <div class="reveal">
                        <h2 class="font-display text-sm font-semibold tracking-widest text-ink/50 uppercase">{{ site_text('project_credits') }}</h2>
                        <dl class="mt-4 space-y-3">
                            @foreach ($credits as $credit)
                                <div>
                                    @if ($credit['role'])<dt class="text-sm text-ink/60">{{ $credit['role'] }}</dt>@endif
                                    <dd class="font-medium text-brown-800">{{ $credit['name'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif
                @if (($client = $project->publicClient()) && $client->logo)
                    <div class="reveal rounded-3xl border border-brown-100 p-6">
                        <img src="{{ $client->logo->url() }}" alt="{{ $client->name }}" loading="lazy" class="mx-auto max-h-16 w-auto object-contain">
                    </div>
                @endif
                <x-site.share-links :url="$project->url()" :title="$project->title" class="reveal" />
            </aside>
        </div>

        @if ($embeds = $project->videoEmbeds())
            <div class="mt-20 grid gap-6 {{ count($embeds) > 1 ? 'md:grid-cols-2' : '' }}">
                @foreach ($embeds as $embed)
                    <div class="reveal aspect-video overflow-hidden rounded-3xl bg-brown-900">
                        <iframe src="{{ $embed }}" title="Vídeo do projecto {{ $project->title }}" loading="lazy" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="size-full"></iframe>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($project->gallery->isNotEmpty())
            <div class="mt-20" x-data="lightbox(@js($project->gallery->filter->isImage()->values()->map->toPickerArray()))">
                <h2 class="sr-only">Galeria</h2>
                <div class="columns-1 gap-5 sm:columns-2 lg:columns-3">
                    @foreach ($project->gallery as $item)
                        @if ($item->isImage())
                            <button type="button" @click="show({{ $project->gallery->filter->isImage()->values()->search(fn ($image) => $image->is($item)) }})" class="reveal group mb-5 block w-full overflow-hidden rounded-3xl" aria-label="Ampliar imagem">
                                <x-site.picture :media="$item" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" class="w-full transition duration-700 group-hover:scale-105" />
                            </button>
                        @elseif ($item->isVideo())
                            <video controls preload="metadata" class="reveal mb-5 w-full rounded-3xl bg-brown-900"><source src="{{ $item->url() }}" type="{{ $item->mime_type }}"></video>
                        @endif
                    @endforeach
                </div>
                <x-site.lightbox />
            </div>
        @endif
    </section>

    @if ($related->isNotEmpty())
        <section class="bg-cream py-20 lg:py-24">
            <div class="container-site">
                <x-site.section-heading :eyebrow="site_text('project_related_eyebrow')" :title="e(site_text('project_related_title'))" />
                <div class="mt-12 grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $relatedProject)
                        <x-site.project-card :project="$relatedProject" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.cta />
</x-site.layout>
