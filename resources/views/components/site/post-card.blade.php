@props(['post', 'featured' => false])

<article {{ $attributes->class(['reveal group flex h-full flex-col', 'lg:flex-row lg:items-center lg:gap-10' => $featured]) }}>
    <a href="{{ $post->url() }}" @class(['relative block aspect-[16/10] overflow-hidden rounded-3xl bg-brown-100', 'lg:w-3/5' => $featured]) tabindex="-1" aria-hidden="true">
        @if ($post->coverImage?->isImage())
            {{-- Blurred copy fills the frame so portrait or square covers are shown whole, never cropped --}}
            <img src="{{ $post->coverImage->thumbnailUrl() }}" alt="" aria-hidden="true" loading="lazy" decoding="async" class="absolute inset-0 size-full scale-110 object-cover opacity-70 blur-2xl">
        @endif
        <x-site.picture :media="$post->coverImage" :label="$post->category?->name" fit="contain" sizes="(min-width: 1024px) 33vw, 100vw"
                class="relative size-full transition duration-700 ease-out-soft group-hover:scale-105" />
    </a>
    <div @class(['flex flex-1 flex-col pt-6', 'lg:w-2/5 lg:pt-0' => $featured])>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-ink/60">
            @if ($post->category)
                <a href="{{ route('blog.category', $post->category->slug) }}" class="font-semibold text-ochre-700 hover:text-ochre-800">{{ $post->category->name }}</a>
                <span aria-hidden="true">•</span>
            @endif
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('j M Y') }}</time>
            <span aria-hidden="true">•</span>
            <span>{{ site_text('reading_time', ['minutes' => $post->reading_time]) }}</span>
        </div>
        <h3 @class(['mt-3 font-semibold transition group-hover:text-ochre-700', 'text-2xl lg:text-3xl' => $featured, 'text-xl' => ! $featured])>
            <a href="{{ $post->url() }}">{{ $post->title }}</a>
        </h3>
        @if ($post->excerpt)
            <p @class(['mt-3 text-ink/70', 'line-clamp-3' => ! $featured])>{{ $post->excerpt }}</p>
        @endif
        @if ($featured && $post->author)
            <p class="mt-6 text-sm font-medium text-brown-800">{{ site_text('post_by', ['author' => $post->author->name]) }}</p>
        @endif
    </div>
</article>
