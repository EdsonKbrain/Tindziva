<x-site.layout :seo="$seo">
    @if ($isPreview)
        <div class="sticky top-20 z-40 bg-amber-100 text-amber-900">
            <div class="container-site flex flex-wrap items-center justify-between gap-3 py-3 text-sm">
                <p class="flex items-center gap-2 font-medium"><x-lucide-eye class="size-4" /> Pré-visualização — estado: {{ $post->status->label() }}{{ $post->published_at ? ' · '.$post->published_at->format('d/m/Y H:i') : '' }}</p>
                <a href="{{ route('admin.posts.edit', $post) }}" class="font-semibold underline">Voltar ao editor</a>
            </div>
        </div>
    @endif

    <article>
        <header class="relative overflow-hidden bg-cream">
            <div aria-hidden="true" class="bubble pointer-events-none absolute -top-24 -right-24 size-96 bg-ochre-100/70"></div>
            <div class="container-site relative max-w-4xl pt-12 pb-12 text-center lg:pt-20">
                <nav aria-label="Navegação estrutural" class="flex justify-center">
                    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-ink/60">
                        <li><a href="{{ route('home') }}" class="hover:text-ochre-700">{{ site_text('breadcrumb_home') }}</a></li>
                        <li><x-lucide-chevron-right class="size-3.5" /></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-ochre-700">{{ site_text('nav_blog') }}</a></li>
                        @if ($post->category)
                            <li><x-lucide-chevron-right class="size-3.5" /></li>
                            <li><a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-ochre-700">{{ $post->category->name }}</a></li>
                        @endif
                    </ol>
                </nav>
                <h1 class="mt-8 text-4xl font-semibold sm:text-5xl lg:text-[3.5rem] lg:leading-[1.08]">{{ $post->title }}</h1>
                @if ($post->excerpt)
                    <p class="mx-auto mt-6 max-w-2xl text-lg text-ink/70 sm:text-xl">{{ $post->excerpt }}</p>
                @endif
                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-ink/70">
                    @if ($post->author)
                        <span class="flex items-center gap-2">
                            <span class="grid size-9 place-items-center rounded-full bg-brown-800 font-display text-xs font-semibold text-white">{{ $post->author->initials() }}</span>
                            <span class="font-medium text-brown-800">{{ $post->author->name }}</span>
                        </span>
                    @endif
                    @if ($post->published_at)
                        <span class="flex items-center gap-1.5"><x-lucide-calendar class="size-4" /> <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->translatedFormat('j \d\e F \d\e Y') }}</time></span>
                    @endif
                    <span class="flex items-center gap-1.5"><x-lucide-clock class="size-4" /> {{ site_text('reading_time', ['minutes' => $post->reading_time]) }}</span>
                </div>
            </div>
        </header>

        @if ($post->coverImage)
            <div class="container-site max-w-5xl">
                <x-site.picture :media="$post->coverImage" eager sizes="(min-width: 1024px) 64rem, 100vw" class="-mt-2 aspect-[16/9] w-full rounded-[2rem] shadow-xl shadow-brown-900/10" />
                @if ($post->coverImage->caption)
                    <p class="mt-3 text-center text-sm text-ink/60">{{ $post->coverImage->caption }}</p>
                @endif
            </div>
        @endif

        <div class="container-site max-w-3xl py-14 lg:py-20">
            <div class="prose-tindziva">
                @richtext($post->content)
            </div>

            <footer class="mt-14 flex flex-col gap-8 border-t border-brown-100 pt-8 sm:flex-row sm:items-center sm:justify-between">
                @if ($post->tags->isNotEmpty())
                    <ul class="flex flex-wrap gap-2" aria-label="Etiquetas">
                        @foreach ($post->tags as $tag)
                            <li><a href="{{ route('blog.tag', $tag->slug) }}" class="inline-flex rounded-full bg-cream px-4 py-1.5 text-sm text-brown-700 transition hover:bg-ochre-600 hover:text-white">#{{ $tag->name }}</a></li>
                        @endforeach
                    </ul>
                @endif
                <x-site.share-links :url="$post->url()" :title="$post->title" />
            </footer>

            @if (site_enabled('post_show_author_box') && $post->author?->bio)
                <div class="mt-12 flex gap-5 rounded-3xl bg-cream p-6 sm:p-8">
                    <span class="grid size-14 shrink-0 place-items-center rounded-full bg-brown-800 font-display font-semibold text-white">{{ $post->author->initials() }}</span>
                    <div>
                        <p class="font-display text-lg font-semibold text-brown-800">{{ $post->author->name }}</p>
                        @if ($post->author->job_title)<p class="text-sm text-ochre-700">{{ $post->author->job_title }}</p>@endif
                        <p class="mt-2 text-ink/70">{{ $post->author->bio }}</p>
                    </div>
                </div>
            @endif
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="bg-cream py-20 lg:py-24">
            <div class="container-site">
                <x-site.section-heading :eyebrow="site_text('post_related_eyebrow')" :title="e(site_text('post_related_title'))" />
                <div class="mt-12 grid gap-x-8 gap-y-12 md:grid-cols-3">
                    @foreach ($related as $relatedPost)
                        <x-site.post-card :post="$relatedPost" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.cta />
</x-site.layout>
