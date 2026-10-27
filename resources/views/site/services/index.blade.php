@use('App\Support\Text')

<x-site.layout :seo="$seo">
    <x-site.page-hero :eyebrow="$page?->section('intro.eyebrow', site_text('nav_services')) ?? site_text('nav_services')" :title="Text::accent($page?->title ?? 'Os nossos *serviços*')" :subtitle="$page?->subtitle"
        :breadcrumbs="[site_text('breadcrumb_home') => route('home'), site_text('nav_services') => route('services.index')]" :image="$page?->heroImage" />

    <section class="container-site py-20 lg:py-24">
        @if ($page?->content)
            <div class="prose-tindziva reveal mb-16 max-w-3xl">@richtext($page->content)</div>
        @endif

        <div class="space-y-6">
            @forelse ($services as $service)
                <a href="{{ $service->url() }}" class="reveal group grid items-center gap-8 overflow-hidden rounded-[2rem] border border-brown-100 bg-white p-6 transition duration-500 hover:border-transparent hover:shadow-2xl hover:shadow-brown-900/10 sm:p-8 lg:grid-cols-12">
                    <div class="overflow-hidden rounded-3xl lg:col-span-4">
                        <x-site.picture :media="$service->image" :label="$service->name" sizes="(min-width: 1024px) 30vw, 100vw" class="aspect-[4/3] w-full transition duration-700 group-hover:scale-105" />
                    </div>
                    <div class="lg:col-span-7 lg:col-start-6">
                        <div class="flex items-center gap-4">
                            <span class="grid size-12 place-items-center rounded-2xl bg-ochre-50 text-ochre-600">@svg('lucide-'.($service->icon ?: 'sparkles'), 'size-6')</span>
                            <span class="font-display text-sm font-semibold text-brown-200">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h2 class="mt-5 text-2xl font-semibold transition group-hover:text-ochre-700 sm:text-3xl">{{ $service->name }}</h2>
                        <p class="mt-3 text-lg leading-relaxed text-ink/70">{{ $service->excerpt }}</p>
                        @if ($service->benefits)
                            <ul class="mt-5 flex flex-wrap gap-2">
                                @foreach (array_slice($service->benefits, 0, 3) as $benefit)
                                    <li class="rounded-full bg-cream px-3 py-1 text-sm text-brown-700">{{ $benefit }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <span class="mt-6 inline-flex items-center gap-2 font-display text-sm font-semibold text-ochre-700">
                            {{ site_text('services_list_more') }} <x-lucide-arrow-right class="size-4 transition group-hover:translate-x-1" />
                        </span>
                    </div>
                </a>
            @empty
                <p class="rounded-3xl bg-cream p-10 text-center text-ink/70">{{ site_text('services_empty') }}</p>
            @endforelse
        </div>
    </section>

    @if ($page?->shows('cta') ?? true)
        <x-site.cta :title="$page?->section('cta.title')" :text="$page?->section('cta.text')" :button-label="$page?->section('cta.button_label')" :button-url="$page?->section('cta.button_url')" />
    @endif
</x-site.layout>
