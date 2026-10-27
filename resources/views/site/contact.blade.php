@use('App\Support\Text')

<x-site.layout :seo="$seo">
    <x-site.page-hero :eyebrow="$page?->section('intro.eyebrow', site_text('nav_contact')) ?? site_text('nav_contact')" :title="Text::accent($page?->title ?? 'Vamos *conversar*')" :subtitle="$page?->subtitle"
        :breadcrumbs="[site_text('breadcrumb_home') => route('home'), site_text('nav_contact') => route('contact')]" :image="$page?->heroImage" />

    <section class="container-site py-16 lg:py-24">
        <div class="grid gap-12 lg:grid-cols-12">
            <aside class="space-y-5 lg:col-span-4">
                @foreach ([
                    ['icon' => 'mail', 'label' => site_text('contact_label_email'), 'value' => setting('contact_email'), 'href' => setting('contact_email') ? 'mailto:'.setting('contact_email') : null],
                    ['icon' => 'phone', 'label' => site_text('contact_label_phone'), 'value' => setting('contact_phone'), 'href' => setting('contact_phone') ? 'tel:'.preg_replace('/\s+/', '', setting('contact_phone')) : null],
                    ['icon' => 'phone', 'label' => site_text('contact_label_phone_secondary'), 'value' => setting('contact_phone_secondary'), 'href' => setting('contact_phone_secondary') ? 'tel:'.preg_replace('/\s+/', '', setting('contact_phone_secondary')) : null],
                    ['icon' => 'message-circle', 'label' => site_text('contact_label_whatsapp'), 'value' => setting('whatsapp_number') ? '+'.setting('whatsapp_number') : null, 'href' => setting('whatsapp_number') ? 'https://wa.me/'.setting('whatsapp_number') : null],
                    ['icon' => 'map-pin', 'label' => site_text('contact_label_address'), 'value' => setting('contact_address'), 'href' => null],
                    ['icon' => 'clock', 'label' => site_text('contact_label_hours'), 'value' => $page?->section('details.hours'), 'href' => null],
                ] as $item)
                    @if ($item['value'])
                        <div class="reveal flex gap-4 rounded-3xl bg-cream p-6">
                            <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-white text-ochre-600">@svg('lucide-'.$item['icon'], 'size-5')</span>
                            <div class="min-w-0">
                                <p class="text-sm text-ink/60">{{ $item['label'] }}</p>
                                @if ($item['href'])
                                    <a href="{{ $item['href'] }}" class="mt-0.5 block font-display text-lg font-semibold break-words text-brown-800 hover:text-ochre-700" @if (str_starts_with($item['href'], 'http')) target="_blank" rel="noopener" @endif>{{ $item['value'] }}</a>
                                @else
                                    <p class="mt-0.5 font-display text-lg font-semibold whitespace-pre-line text-brown-800">{{ $item['value'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                @php
                    $socials = array_filter(['facebook' => setting('social_facebook'), 'instagram' => setting('social_instagram'), 'linkedin' => setting('social_linkedin'), 'youtube' => setting('social_youtube'), 'twitter' => setting('social_x'), 'music-2' => setting('social_tiktok')]);
                @endphp
                @if ($socials)
                    <div class="reveal rounded-3xl bg-brown-800 p-6 text-white">
                        <p class="font-display font-semibold">{{ site_text('contact_follow_us') }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($socials as $icon => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="grid size-11 place-items-center rounded-full bg-white/10 transition hover:bg-ochre-600" aria-label="{{ ucfirst($icon === 'music-2' ? 'TikTok' : ($icon === 'twitter' ? 'X' : $icon)) }}">@svg('lucide-'.$icon, 'size-5')</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>

            <div class="lg:col-span-8" x-data="{ tab: window.location.hash === '#contacto' || @js($errors->contact->any() || session()->has('contact_success')) ? 'contact' : 'quote' }">
                <div class="rounded-[2rem] border border-brown-100 bg-white p-6 shadow-xl shadow-brown-900/5 sm:p-10">
                    <div class="flex gap-2 rounded-full bg-cream p-1.5" role="tablist" aria-label="Tipo de pedido">
                        <button type="button" role="tab" :aria-selected="tab === 'quote'" @click="tab = 'quote'" class="flex-1 rounded-full px-4 py-3 font-display text-sm font-semibold transition" :class="tab === 'quote' ? 'bg-brown-800 text-white shadow' : 'text-brown-800 hover:bg-white'">
                            {{ $page?->section('details.quote_title', site_text('header_cta_label')) }}
                        </button>
                        <button type="button" role="tab" :aria-selected="tab === 'contact'" @click="tab = 'contact'" class="flex-1 rounded-full px-4 py-3 font-display text-sm font-semibold transition" :class="tab === 'contact' ? 'bg-brown-800 text-white shadow' : 'text-brown-800 hover:bg-white'">
                            {{ $page?->section('details.form_title', site_text('form_contact_submit')) }}
                        </button>
                    </div>
                    <div id="proposta" class="scroll-mt-28 pt-8" x-show="tab === 'quote'" role="tabpanel">
                        <x-site.quote-form :services="$services" />
                    </div>
                    <div id="contacto" class="scroll-mt-28 pt-8" x-show="tab === 'contact'" x-cloak role="tabpanel">
                        <x-site.contact-form />
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($map = setting('map_embed_url'))
        <section class="container-site pb-20 lg:pb-28">
            <div class="overflow-hidden rounded-[2rem] bg-brown-100">
                <iframe src="{{ $map }}" title="Localização da {{ setting('company_name', 'Tindziva') }}" class="h-[28rem] w-full grayscale-[30%]" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
        </section>
    @endif
</x-site.layout>
