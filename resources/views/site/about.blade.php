@use('App\Support\Text')

<x-site.layout :seo="$seo">
    <x-site.page-hero :eyebrow="$page->section('intro.eyebrow', site_text('nav_about'))" :title="Text::accent($page->section('history.title', $page->title))" :subtitle="$page->subtitle"
        :breadcrumbs="[site_text('breadcrumb_home') => route('home'), $page->title => route('about')]" :image="$page->heroImage" />

    {{-- History --}}
    @if ($page->shows('history'))
    <section class="container-site py-20 lg:py-28">
        <div class="grid gap-14 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-28">
                    <p class="eyebrow reveal">{{ $page->section('history.eyebrow', 'A nossa história') }}</p>
                    <x-site.picture :media="$media->get($page->section('history.image'))" sizes="(min-width: 1024px) 40vw, 100vw" class="reveal mt-8 aspect-[4/5] w-full rounded-[2rem]" />
                </div>
            </div>
            <div class="prose-tindziva reveal lg:col-span-6 lg:col-start-7">
                @richtext($page->section('history.text'))
            </div>
        </div>
    </section>
    @endif

    {{-- Mission, vision, values --}}
    @if ($page->shows('purpose'))
    <section class="bg-cream py-20 lg:py-28">
        <div class="container-site">
            <div class="grid gap-6 md:grid-cols-2">
                @foreach (['mission' => [$page->section('purpose.mission_label', 'Missão'), 'target'], 'vision' => [$page->section('purpose.vision_label', 'Visão'), 'compass']] as $key => [$label, $icon])
                    @if ($text = $page->section("purpose.{$key}"))
                        <div class="reveal relative overflow-hidden rounded-[2rem] bg-white p-10 lg:p-12">
                            <div aria-hidden="true" class="bubble absolute -top-12 -right-12 size-40 bg-ochre-50"></div>
                            <span class="relative grid size-14 place-items-center rounded-2xl bg-brown-800 text-white">@svg('lucide-'.$icon, 'size-6')</span>
                            <h2 class="relative mt-8 text-2xl font-semibold">{{ $label }}</h2>
                            <p class="relative mt-4 text-lg leading-relaxed text-ink/75">{{ $text }}</p>
                        </div>
                    @endif
                @endforeach
            </div>

            @if ($values = $page->items('purpose.values'))
                <div class="mt-20">
                    <x-site.section-heading :eyebrow="$page->section('purpose.values_eyebrow', 'Valores')" :title="Text::accent($page->section('purpose.values_title', 'Aquilo em que *acreditamos*'))" align="center" />
                    <div @class(['mt-12 grid gap-5 sm:grid-cols-2', 'lg:grid-cols-3' => count($values) === 3, 'lg:grid-cols-4' => count($values) >= 4])>
                        @foreach ($values as $value)
                            <div class="reveal rounded-3xl border border-brown-100 bg-white p-8">
                                <p class="font-serif text-5xl text-ochre-600 italic">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                <h3 class="mt-4 text-xl font-semibold">{{ $value['title'] ?? '' }}</h3>
                                <p class="mt-2 leading-relaxed text-ink/70">{{ $value['text'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- Approach --}}
    @if ($page->shows('approach') && ($steps = $page->items('approach.steps')))
        <section class="container-site py-20 lg:py-28">
            <x-site.section-heading :eyebrow="$page->section('approach.eyebrow', 'Abordagem')" :title="Text::accent($page->section('approach.title', 'Como *trabalhamos*'))" :text="$page->section('approach.text')" />
            <ol class="mt-14 grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $step)
                    <li class="reveal relative border-t-2 border-brown-100 pt-8">
                        <span class="absolute -top-[9px] left-0 size-4 rounded-full border-4 border-white bg-ochre-600"></span>
                        <p class="font-display text-sm font-semibold tracking-widest text-ochre-600 uppercase">{{ $page->section('approach.step_label', 'Etapa') }} {{ $loop->iteration }}</p>
                        <h3 class="mt-3 text-xl font-semibold">{{ $step['title'] ?? '' }}</h3>
                        <p class="mt-3 leading-relaxed text-ink/70">{{ $step['text'] ?? '' }}</p>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    {{-- Differentiators --}}
    @if ($page->shows('differentiators') && ($items = $page->items('differentiators.items')))
        <section class="relative overflow-hidden bg-brown-800 py-20 lg:py-28">
            <div aria-hidden="true" class="bubble absolute -top-40 -left-40 size-[30rem] bg-brown-700/70"></div>
            <div class="container-site relative">
                <x-site.section-heading tone="light" :eyebrow="$page->section('differentiators.eyebrow', 'Diferenciais')" :title="Text::accent($page->section('differentiators.title', 'O que nos *distingue*'))" />
                <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($items as $item)
                        <div class="reveal rounded-3xl bg-white/5 p-8 ring-1 ring-white/10">
                            <span class="grid size-12 place-items-center rounded-2xl bg-ochre-600 text-white">@svg('lucide-'.($item['icon'] ?? 'sparkles'), 'size-6')</span>
                            <h3 class="mt-6 text-xl font-semibold text-white">{{ $item['title'] ?? '' }}</h3>
                            <p class="mt-3 leading-relaxed text-white/70">{{ $item['text'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Team: only rendered when approved information exists --}}
    @if ($page->shows('team') && ($members = $page->items('team.members')))
        <section class="container-site py-20 lg:py-28">
            <x-site.section-heading :eyebrow="$page->section('team.eyebrow', 'Equipa')" :title="Text::accent($page->section('team.title', 'As pessoas por detrás das *ideias*'))" :text="$page->section('team.text')" />
            <div class="mt-14 grid grid-cols-2 gap-x-6 gap-y-12 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($members as $member)
                    <figure class="reveal">
                        <x-site.picture :media="$media->get($member['photo'] ?? null)" :alt="$member['name'] ?? ''" sizes="(min-width: 1024px) 25vw, 50vw" class="aspect-[4/5] w-full rounded-3xl" />
                        <figcaption class="mt-4">
                            <p class="font-display text-lg font-semibold text-brown-800">{{ $member['name'] ?? '' }}</p>
                            <p class="text-sm text-ink/60">{{ $member['role'] ?? '' }}</p>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    @if ($page->shows('cta'))
        <x-site.cta :title="$page->section('cta.title')" :text="$page->section('cta.text')" :button-label="$page->section('cta.button_label')" :button-url="$page->section('cta.button_url')" />
    @endif
</x-site.layout>
