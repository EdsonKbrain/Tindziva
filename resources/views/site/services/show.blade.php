<x-site.layout :seo="$seo">
    <x-site.page-hero :eyebrow="$service->category?->name ?? site_text('service_eyebrow')" :title="e($service->name)" :subtitle="$service->excerpt"
        :breadcrumbs="[site_text('breadcrumb_home') => route('home'), site_text('nav_services') => route('services.index'), $service->name => $service->url()]" :image="$service->image">
        <div class="mt-9 flex flex-wrap gap-3">
            <x-site.button href="#proposta" variant="light">{{ site_text('service_quote_button') }}</x-site.button>
            @if ($projects->isNotEmpty())
                <x-site.button href="#projectos" variant="ghost-light" icon="arrow-down">{{ site_text('service_projects_button') }}</x-site.button>
            @endif
        </div>
    </x-site.page-hero>

    <section class="container-site py-20 lg:py-24">
        <div class="grid gap-14 lg:grid-cols-12">
            <div class="prose-tindziva reveal lg:col-span-7">
                @richtext($service->description)
            </div>

            @if ($service->benefits)
                <aside class="lg:col-span-4 lg:col-start-9">
                    <div class="reveal rounded-[2rem] bg-brown-800 p-8 text-white lg:sticky lg:top-28">
                        <h2 class="text-xl font-semibold text-white">{{ site_text('service_benefits_title') }}</h2>
                        <ul class="mt-6 space-y-4">
                            @foreach ($service->benefits as $benefit)
                                <li class="flex gap-3">
                                    <span class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-ochre-600"><x-lucide-check class="size-3.5" /></span>
                                    <span class="text-white/85">{{ $benefit }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            @endif
        </div>

        @if ($service->gallery->isNotEmpty())
            <div class="mt-20 grid grid-cols-2 gap-4 lg:grid-cols-3" x-data="lightbox(@js($service->gallery->map->toPickerArray()))">
                @foreach ($service->gallery as $image)
                    <button type="button" @click="show({{ $loop->index }})" class="reveal group overflow-hidden rounded-3xl {{ $loop->first ? 'col-span-2 row-span-2' : '' }}" aria-label="Ampliar imagem {{ $loop->iteration }}">
                        <x-site.picture :media="$image" sizes="(min-width: 1024px) 33vw, 50vw" class="aspect-square size-full transition duration-700 group-hover:scale-105" />
                    </button>
                @endforeach
                <x-site.lightbox />
            </div>
        @endif
    </section>

    @if ($projects->isNotEmpty())
        <section id="projectos" class="bg-cream py-20 lg:py-24">
            <div class="container-site">
                <x-site.section-heading :eyebrow="site_text('service_related_eyebrow')" :title="e(site_text('service_related_title'))" />
                <div class="mt-12 grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-site.project-card :project="$project" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="proposta" class="container-site scroll-mt-24 py-20 lg:py-24">
        <div class="grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="eyebrow">{{ site_text('service_quote_eyebrow') }}</p>
                <h2 class="mt-4 text-3xl font-semibold sm:text-4xl">{{ site_text('service_quote_title', ['service' => mb_strtolower($service->name)]) }}</h2>
                <p class="mt-5 text-lg text-ink/70">{{ site_text('service_quote_text') }}</p>
                @if ($otherServices->isNotEmpty())
                    <div class="mt-10">
                        <p class="text-sm font-semibold text-brown-800">{{ site_text('service_others_title') }}</p>
                        <ul class="mt-3 space-y-2">
                            @foreach ($otherServices as $other)
                                <li><a href="{{ $other->url() }}" class="inline-flex items-center gap-2 text-ink/70 hover:text-ochre-700"><x-lucide-arrow-right class="size-4" /> {{ $other->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="rounded-[2rem] border border-brown-100 bg-white p-6 shadow-xl shadow-brown-900/5 sm:p-10 lg:col-span-8">
                <x-site.quote-form :selected="$service" />
            </div>
        </div>
    </section>
</x-site.layout>
