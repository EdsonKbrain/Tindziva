@php
    $socials = array_filter([
        'facebook' => ['url' => setting('social_facebook'), 'label' => 'Facebook'],
        'instagram' => ['url' => setting('social_instagram'), 'label' => 'Instagram'],
        'linkedin' => ['url' => setting('social_linkedin'), 'label' => 'LinkedIn'],
        'youtube' => ['url' => setting('social_youtube'), 'label' => 'YouTube'],
        'twitter' => ['url' => setting('social_x'), 'label' => 'X'],
        'music-2' => ['url' => setting('social_tiktok'), 'label' => 'TikTok'],
    ], fn ($social) => filled($social['url']));
@endphp

<footer class="relative overflow-hidden bg-brown-900 text-white/75">
    <div aria-hidden="true" class="bubble pointer-events-none absolute -top-40 -right-32 size-[28rem] bg-ochre-600/15"></div>
    <div aria-hidden="true" class="bubble pointer-events-none absolute -bottom-48 -left-40 size-[26rem] bg-brown-800"></div>

    <div class="container-site relative grid gap-12 py-16 lg:grid-cols-12 lg:py-20">
        <div class="lg:col-span-4">
            <x-site.logo variant="light" />
            @if (site_enabled('footer_show_tagline') && ($tagline = setting('company_tagline')))
                <p class="mt-5 font-serif text-2xl text-ochre-300 italic">{{ $tagline }}</p>
            @endif
            <p class="mt-4 max-w-sm leading-relaxed">
                {{ setting('footer_text', 'Comunicação estratégica, criatividade e conhecimento do mercado moçambicano ao serviço das marcas e instituições.') }}
            </p>
            @if ($socials)
                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach ($socials as $icon => $social)
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener" class="grid size-10 place-items-center rounded-full border border-white/15 transition hover:border-ochre-400 hover:bg-ochre-600 hover:text-white" aria-label="{{ $social['label'] }}">
                            @svg('lucide-'.$icon, 'size-4')
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid gap-10 sm:grid-cols-3 lg:col-span-5">
            <div>
                <h2 class="font-display text-sm font-semibold tracking-widest text-white uppercase">{{ site_text('footer_nav_title') }}</h2>
                <ul class="mt-5 space-y-3 text-[15px]">
                    @if (site_enabled('nav_show_about'))<li><a class="hover:text-white" href="{{ route('about') }}">{{ site_text('nav_about') }}</a></li>@endif
                    @if (site_enabled('nav_show_services'))<li><a class="hover:text-white" href="{{ route('services.index') }}">{{ site_text('nav_services') }}</a></li>@endif
                    @if (site_enabled('nav_show_portfolio'))<li><a class="hover:text-white" href="{{ route('portfolio.index') }}">{{ site_text('nav_portfolio') }}</a></li>@endif
                    @if (site_enabled('nav_show_blog'))<li><a class="hover:text-white" href="{{ route('blog.index') }}">{{ site_text('nav_blog') }}</a></li>@endif
                    <li><a class="hover:text-white" href="{{ route('contact') }}">{{ site_text('nav_contact') }}</a></li>
                    @foreach ($menuPages as $menuPage)
                        <li><a class="hover:text-white" href="{{ $menuPage->url() }}">{{ $menuPage->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            @if (setting('contact_address') || setting('contact_phone') || setting('contact_email'))
            <div class="sm:col-span-2">
                <h2 class="font-display text-sm font-semibold tracking-widest text-white uppercase">{{ site_text('footer_contact_title') }}</h2>
                <ul class="mt-5 space-y-4 text-[15px]">
                    @if ($address = setting('contact_address'))
                        <li class="flex gap-3"><x-lucide-map-pin class="mt-0.5 size-4 shrink-0 text-ochre-400" /> <span class="whitespace-pre-line">{{ $address }}</span></li>
                    @endif
                    @if ($phone = setting('contact_phone'))
                        <li class="flex gap-3"><x-lucide-phone class="mt-0.5 size-4 shrink-0 text-ochre-400" /> <a class="hover:text-white" href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a></li>
                    @endif
                    @if ($email = setting('contact_email'))
                        <li class="flex gap-3"><x-lucide-mail class="mt-0.5 size-4 shrink-0 text-ochre-400" /> <a class="break-all hover:text-white" href="mailto:{{ $email }}">{{ $email }}</a></li>
                    @endif
                </ul>
            </div>
            @endif
        </div>

        @if (setting('newsletter_enabled', '1'))
            <div class="lg:col-span-3" id="newsletter">
                <h2 class="font-display text-sm font-semibold tracking-widest text-white uppercase">{{ setting('newsletter_title', 'Newsletter') }}</h2>
                <p class="mt-5 text-[15px]">{{ setting('newsletter_text', 'Ideias, tendências e novidades da comunicação em Moçambique, directamente no seu e-mail.') }}</p>
                <x-site.newsletter-form class="mt-5" />
            </div>
        @endif
    </div>

    <div class="relative border-t border-white/10">
        <div class="container-site flex flex-col gap-3 py-6 text-sm sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ now()->year }} {{ setting('footer_copyright', setting('company_name', config('app.name')).'. Todos os direitos reservados.') }}</p>
            <p class="text-white/50">Desenvolvido por <a href="http://turingtech.co.mz/" target="_blank" rel="noopener" class="font-medium text-white/80 underline-offset-4 hover:text-white hover:underline">Turing Tecnologias</a></p>
        </div>
    </div>
</footer>
