@use('App\Support\Text')

<x-site.layout :seo="$seo">
    <x-site.page-hero :eyebrow="$page->section('intro.eyebrow')" :title="Text::accent($page->title)" :subtitle="$page->subtitle" :image="$page->heroImage"
        :breadcrumbs="[site_text('breadcrumb_home') => route('home'), $page->title => $page->url()]" />

    <section class="container-site max-w-3xl py-16 lg:py-24">
        <div class="prose-tindziva">
            @richtext($page->content)
        </div>
    </section>
</x-site.layout>
