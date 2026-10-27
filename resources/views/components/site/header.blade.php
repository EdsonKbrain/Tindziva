@php
    $links = array_values(array_filter([
        ['label' => site_text('nav_home'), 'route' => 'home', 'active' => 'home'],
        site_enabled('nav_show_about') ? ['label' => site_text('nav_about'), 'route' => 'about', 'active' => 'about'] : null,
        site_enabled('nav_show_services') ? ['label' => site_text('nav_services'), 'route' => 'services.index', 'active' => 'services.*', 'children' => site_enabled('nav_services_dropdown') ? $navServices : collect()] : null,
        site_enabled('nav_show_portfolio') ? ['label' => site_text('nav_portfolio'), 'route' => 'portfolio.index', 'active' => 'portfolio.*'] : null,
        site_enabled('nav_show_blog') ? ['label' => site_text('nav_blog'), 'route' => 'blog.index', 'active' => 'blog.*'] : null,
        ['label' => site_text('nav_contact'), 'route' => 'contact', 'active' => 'contact'],
    ]));
    $showCta = site_enabled('header_cta_show');
@endphp

<header x-data="siteHeader" class="sticky top-0 z-50 transition-all duration-300" :class="scrolled ? 'bg-white/90 shadow-[0_1px_0_rgba(73,32,13,0.08)] backdrop-blur-lg' : 'bg-white'">
    <div class="container-site flex h-20 items-center justify-between gap-6">
        <x-site.logo />

        <nav class="hidden items-center gap-1 lg:flex" aria-label="Navegação principal">
            @foreach ($links as $link)
                @if (! empty($link['children']) && $link['children']->isNotEmpty())
                    <div class="group relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape="open = false">
                        <a href="{{ route($link['route']) }}" @class([
                            'inline-flex items-center gap-1 rounded-full px-4 py-2 text-[15px] font-medium transition',
                            'text-ochre-700' => request()->routeIs($link['active']),
                            'text-brown-800 hover:text-ochre-700' => ! request()->routeIs($link['active']),
                        ]) @focus="open = true" aria-haspopup="true" :aria-expanded="open">
                            {{ $link['label'] }}
                            <x-lucide-chevron-down class="size-4 transition" ::class="open && 'rotate-180'" />
                        </a>
                        <div x-cloak x-show="open" x-transition.opacity.duration.200ms class="absolute top-full left-1/2 w-80 -translate-x-1/2 pt-3">
                            <div class="rounded-2xl border border-brown-100 bg-white p-2 shadow-xl shadow-brown-900/10">
                                @foreach ($link['children'] as $service)
                                    <a href="{{ $service->url() }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-brown-800 transition hover:bg-ochre-50">
                                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-ochre-50 text-ochre-600">
                                            @svg('lucide-'.($service->icon ?: 'sparkles'), 'size-4')
                                        </span>
                                        <span class="font-medium">{{ $service->name }}</span>
                                    </a>
                                @endforeach
                                <a href="{{ route('services.index') }}" class="mt-1 flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-semibold text-ochre-700 hover:bg-ochre-50">
                                    {{ site_text('nav_services_all') }} <x-lucide-arrow-right class="size-4" />
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route($link['route']) }}" @class([
                        'rounded-full px-4 py-2 text-[15px] font-medium transition',
                        'text-ochre-700' => request()->routeIs($link['active']),
                        'text-brown-800 hover:text-ochre-700' => ! request()->routeIs($link['active']),
                    ]) @if (request()->routeIs($link['active'])) aria-current="page" @endif>{{ $link['label'] }}</a>
                @endif
            @endforeach
            @foreach ($menuPages as $menuPage)
                <a href="{{ $menuPage->url() }}" class="rounded-full px-4 py-2 text-[15px] font-medium text-brown-800 transition hover:text-ochre-700">{{ $menuPage->title }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3">
            @if ($showCta)
                <div class="hidden sm:block">
                    <x-site.button :href="site_text('header_cta_url')" size="sm">{{ site_text('header_cta_label') }}</x-site.button>
                </div>
            @endif
            <button type="button" class="grid size-11 place-items-center rounded-full text-brown-800 hover:bg-brown-50 lg:hidden" @click="open = true" aria-label="Abrir menu" :aria-expanded="open">
                <x-lucide-menu class="size-6" />
            </button>
        </div>
    </div>

    {{-- Mobile navigation --}}
    <div x-cloak x-show="open" class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true" aria-label="Menu">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-brown-950/50" @click="open = false"></div>
        <div x-show="open" x-trap.inert.noscroll="open" @keydown.escape.window="open = false"
             x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col overflow-y-auto bg-brown-800 px-6 pt-5 pb-8 text-white">
            <div class="flex items-center justify-between">
                <x-site.logo variant="light" />
                <button type="button" class="grid size-11 place-items-center rounded-full hover:bg-white/10" @click="open = false" aria-label="Fechar menu">
                    <x-lucide-x class="size-6" />
                </button>
            </div>
            <nav class="mt-10 flex flex-col gap-1" aria-label="Navegação móvel">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}" @class([
                        'rounded-xl px-4 py-3 font-display text-2xl font-medium transition',
                        'bg-white/10 text-ochre-300' => request()->routeIs($link['active']),
                        'hover:bg-white/5' => ! request()->routeIs($link['active']),
                    ])>{{ $link['label'] }}</a>
                @endforeach
                @foreach ($menuPages as $menuPage)
                    <a href="{{ $menuPage->url() }}" class="rounded-xl px-4 py-3 font-display text-2xl font-medium hover:bg-white/5">{{ $menuPage->title }}</a>
                @endforeach
            </nav>
            <div class="mt-auto space-y-3 pt-10 text-sm text-white/70">
                @if ($showCta)
                    <x-site.button :href="site_text('header_cta_url')" variant="light" class="w-full justify-center">{{ site_text('header_cta_label') }}</x-site.button>
                @endif
                @if ($email = setting('contact_email'))
                    <a href="mailto:{{ $email }}" class="flex items-center gap-2 pt-4 hover:text-white"><x-lucide-mail class="size-4" /> {{ $email }}</a>
                @endif
                @if ($phone = setting('contact_phone'))
                    <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="flex items-center gap-2 hover:text-white"><x-lucide-phone class="size-4" /> {{ $phone }}</a>
                @endif
            </div>
        </div>
    </div>
</header>
