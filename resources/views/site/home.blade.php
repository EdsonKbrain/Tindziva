@use('App\Support\Text')
@php
    $section = fn (string $key, $default = null) => $page?->section($key, $default) ?? $default;
    $shows = fn (string $key): bool => $page?->shows($key) ?? true;
@endphp

<x-site.layout :seo="$seo">
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-cream">
        <div aria-hidden="true" class="bubble pointer-events-none absolute -top-32 -left-40 size-[30rem] bg-ochre-100/70"></div>

        <div class="container-site relative grid items-center gap-14 pt-12 pb-20 lg:grid-cols-12 lg:gap-8 lg:pt-20 lg:pb-28">
            <div class="lg:col-span-6">
                <p class="eyebrow reveal">{{ $section('hero.eyebrow', 'Comunicação & Ideias · Moçambique') }}</p>
                <h1 class="reveal mt-6 text-[2.6rem] leading-[1.05] font-semibold sm:text-6xl lg:text-[4.25rem]">
                    {{ Text::accent($section('hero.title', 'Transformamos ideias em comunicação que gera *impacto*.')) }}
                </h1>
                <p class="reveal mt-7 max-w-xl text-lg leading-relaxed text-ink/70 sm:text-xl">
                    {{ $section('hero.text') }}
                </p>
                <div class="reveal mt-10 flex flex-wrap gap-3">
                    <x-site.button :href="$section('hero.primary_url', route('contact').'#proposta')" size="lg">{{ $section('hero.primary_label', 'Pedir proposta') }}</x-site.button>
                    <x-site.button :href="$section('hero.secondary_url', route('portfolio.index'))" variant="outline" size="lg" icon="arrow-up-right">{{ $section('hero.secondary_label', 'Ver portfólio') }}</x-site.button>
                </div>
            </div>

            <div class="relative lg:col-span-6">
                <div class="reveal relative mx-auto aspect-square w-full max-w-[34rem]">
                    <div aria-hidden="true" class="bubble absolute inset-[6%] translate-x-[6%] translate-y-[5%] bg-brown-800"></div>
                    <div class="bubble absolute inset-[6%] overflow-hidden bg-brown-100 shadow-2xl shadow-brown-900/20">
                        <x-site.picture :media="$heroImage" eager sizes="(min-width: 1024px) 34rem, 90vw" class="size-full" />
                    </div>
                    <div aria-hidden="true" class="absolute top-[6%] right-[2%] flex animate-float items-center gap-1.5 rounded-[1.75rem] rounded-br-md bg-ochre-600 px-6 py-5 shadow-xl shadow-ochre-900/30">
                        <span class="size-2.5 rounded-full bg-brown-900"></span>
                        <span class="size-2.5 rounded-full bg-brown-900"></span>
                        <span class="size-2.5 rounded-full bg-brown-900"></span>
                    </div>
                    @if ($stat = ($page?->items('about.stats')[0] ?? null))
                        <div class="absolute bottom-[4%] left-0 rounded-3xl rounded-bl-md bg-white px-6 py-5 shadow-xl shadow-brown-900/10 sm:left-[-4%]" style="animation-delay: -3s">
                            <p class="font-display text-4xl font-semibold text-brown-800">{{ $stat['value'] }}</p>
                            <p class="mt-1 max-w-[10rem] text-sm text-ink/70">{{ $stat['label'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Institutional presentation --}}
    @if ($shows('about'))
    <section class="container-site py-20 lg:py-28">
        <div class="grid items-center gap-14 lg:grid-cols-12">
            <div class="reveal order-2 lg:order-1 lg:col-span-5">
                <x-site.picture :media="$aboutImage" sizes="(min-width: 1024px) 40vw, 100vw" class="aspect-[4/5] w-full rounded-[2rem]" />
            </div>
            <div class="order-1 lg:order-2 lg:col-span-6 lg:col-start-7">
                <x-site.section-heading :eyebrow="$section('about.eyebrow', 'Quem somos')" :title="Text::accent($section('about.title', 'Comunicação com *propósito*'))" />
                <div class="prose-tindziva reveal mt-6 prose-p:text-lg">
                    @richtext($section('about.text'))
                </div>
                @if ($stats = $page?->items('about.stats'))
                    <dl class="reveal mt-10 grid grid-cols-2 gap-6 border-t border-brown-100 pt-8 sm:grid-cols-3">
                        @foreach ($stats as $stat)
                            <div>
                                <dt class="sr-only">{{ $stat['label'] }}</dt>
                                <dd class="font-display text-4xl font-semibold text-ochre-600">{{ $stat['value'] }}</dd>
                                <dd class="mt-1 text-sm text-ink/70">{{ $stat['label'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
                <div class="reveal mt-10">
                    <x-site.button :href="$section('about.button_url', route('about'))" variant="dark">{{ $section('about.button_label', 'Conheça a Tindziva') }}</x-site.button>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Services --}}
    @if ($shows('services') && $services->isNotEmpty())
        <section class="bg-cream py-20 lg:py-28">
            <div class="container-site">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <x-site.section-heading :eyebrow="$section('services.eyebrow', 'Serviços')" :title="Text::accent($section('services.title', 'Soluções integradas de *comunicação*'))" :text="$section('services.text')" />
                    <x-site.button :href="route('services.index')" variant="link" class="reveal shrink-0">{{ $section('services.link_label', 'Todos os serviços') }}</x-site.button>
                </div>
                <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($services as $service)
                        <x-site.service-card :service="$service" :index="$loop->iteration" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Selected portfolio --}}
    @if ($shows('portfolio') && $projects->isNotEmpty())
        <section class="container-site py-20 lg:py-28">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <x-site.section-heading :eyebrow="$section('portfolio.eyebrow', 'Portfólio')" :title="Text::accent($section('portfolio.title', 'Projectos que *falam* por nós'))" :text="$section('portfolio.text')" />
                <x-site.button :href="route('portfolio.index')" variant="link" class="reveal shrink-0">{{ $section('portfolio.link_label', 'Ver todo o portfólio') }}</x-site.button>
            </div>
            <div class="mt-14 grid gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects->take($projects->count() > 3 ? intdiv($projects->count(), 3) * 3 : 3) as $project)
                    <x-site.project-card :project="$project" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Clients & partners --}}
    @if ($shows('clients') && $clients->isNotEmpty())
        <section class="overflow-hidden border-y border-brown-100 py-16 lg:py-20">
            <div class="container-site flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <h2 class="reveal max-w-2xl text-2xl font-semibold sm:text-3xl">{{ \App\Support\Text::accent($section('clients.title', 'Mais do que clientes, amigos que apostam a alma dos seus negócios em nós')) }}</h2>
                <x-site.button :href="route('portfolio.index').'#clientes'" variant="link" class="reveal shrink-0">{{ $section('clients.link_label', 'Ver todos os clientes') }}</x-site.button>
            </div>
            <div class="marquee reveal mt-10 motion-reduce:[mask-image:none] motion-reduce:px-4">
                <ul class="marquee-track flex w-max gap-4 motion-reduce:w-auto motion-reduce:flex-wrap motion-reduce:justify-center">
                    @foreach ([$clients, $clients] as $copy)
                        @foreach ($copy as $client)
                            <li @if ($loop->parent->index === 1) aria-hidden="true" @endif @class(['w-44 shrink-0 sm:w-52', 'motion-reduce:hidden' => $loop->parent->index === 1])>
                                <x-site.client-logo :client="$client" />
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Differentiators --}}
    @if ($shows('differentiators') && ($differentiators = $page?->items('differentiators.items')))
        <section class="relative overflow-hidden bg-brown-800 py-20 text-white lg:py-28">
            <div aria-hidden="true" class="bubble absolute -right-40 -bottom-40 size-[34rem] bg-brown-700/70"></div>
            <div class="container-site relative">
                <x-site.section-heading tone="light" :eyebrow="$section('differentiators.eyebrow', 'Porquê a Tindziva')" :title="Text::accent($section('differentiators.title', 'O que nos *distingue*'))" />
                <div class="mt-14 grid gap-px overflow-hidden rounded-3xl bg-white/10 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($differentiators as $item)
                        <div class="reveal bg-brown-800 p-8 transition hover:bg-brown-700/60" style="transition-delay: {{ $loop->index * 80 }}ms">
                            <span class="grid size-12 place-items-center rounded-2xl bg-ochre-600 text-white">
                                @svg('lucide-'.($item['icon'] ?? 'sparkles'), 'size-6')
                            </span>
                            <h3 class="mt-6 text-xl font-semibold text-white">{{ $item['title'] ?? '' }}</h3>
                            <p class="mt-3 leading-relaxed text-white/70">{{ $item['text'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Latest articles --}}
    @if ($shows('blog') && $posts->isNotEmpty())
        <section class="container-site py-20 lg:py-28">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <x-site.section-heading :eyebrow="$section('blog.eyebrow', 'Blog')" :title="Text::accent($section('blog.title', 'Ideias e *perspectivas*'))" :text="$section('blog.text')" />
                <x-site.button :href="route('blog.index')" variant="link" class="reveal shrink-0">{{ $section('blog.link_label', 'Ler o blog') }}</x-site.button>
            </div>
            <div class="mt-14 grid gap-x-8 gap-y-14 md:grid-cols-3">
                @foreach ($posts as $post)
                    <x-site.post-card :post="$post" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($shows('cta'))
        <x-site.cta :title="$section('cta.title')" :text="$section('cta.text')" :button-label="$section('cta.button_label')" :button-url="$section('cta.button_url')" />
    @endif
</x-site.layout>
