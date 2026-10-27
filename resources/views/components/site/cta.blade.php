@props(['title' => null, 'text' => null, 'buttonLabel' => null, 'buttonUrl' => null])

<section class="container-site py-16 lg:py-24">
    <div class="reveal relative overflow-hidden rounded-[2.5rem] bg-brown-800 px-6 py-14 text-center sm:px-12 lg:py-20">
        <div aria-hidden="true" class="bubble absolute -top-24 -left-20 size-80 bg-brown-700"></div>
        <div aria-hidden="true" class="bubble absolute -right-16 -bottom-28 size-96 bg-ochre-600/80"></div>
        <div aria-hidden="true" class="absolute top-10 right-12 hidden gap-2 sm:flex">
            <span class="size-3 rounded-full bg-white/70"></span><span class="size-3 rounded-full bg-white/70"></span><span class="size-3 rounded-full bg-white/70"></span>
        </div>
        <div class="relative mx-auto max-w-2xl">
            <h2 class="text-3xl font-semibold text-white sm:text-4xl lg:text-5xl">{{ $title ?? site_text('cta_title') }}</h2>
            <p class="mt-5 text-lg text-white/75">{{ $text ?? site_text('cta_text') }}</p>
            <div class="mt-9 flex flex-wrap justify-center gap-3">
                <x-site.button :href="$buttonUrl ?? site_text('cta_button_url')" variant="light" size="lg">{{ $buttonLabel ?? site_text('cta_button_label') }}</x-site.button>
                @if (site_enabled('cta_show_whatsapp') && ($whatsapp = setting('whatsapp_number')))
                    <x-site.button href="https://wa.me/{{ $whatsapp }}" variant="ghost-light" size="lg" icon="message-circle" target="_blank" rel="noopener">{{ site_text('cta_whatsapp_label') }}</x-site.button>
                @endif
            </div>
        </div>
    </div>
</section>
