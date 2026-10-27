<x-site.layout :seo="new \App\Support\SeoData(title: site_text('newsletter_unsubscribed_title'), noindex: true)">
    <section class="container-site max-w-2xl py-24 text-center lg:py-32">
        <span class="mx-auto grid size-16 place-items-center rounded-full bg-ochre-50 text-ochre-600"><x-lucide-mail class="size-7" /></span>
        <h1 class="mt-8 text-4xl font-semibold">{{ site_text('newsletter_unsubscribed_title') }}</h1>
        <p class="mt-4 text-lg text-ink/70">{{ site_text('newsletter_unsubscribed_text') }}</p>
        <x-site.button :href="route('home')" class="mt-10">{{ site_text('newsletter_unsubscribed_button') }}</x-site.button>
    </section>
</x-site.layout>
