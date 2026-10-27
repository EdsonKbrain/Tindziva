@props(['project', 'large' => false])

<article {{ $attributes->class('reveal group') }}>
    <a href="{{ $project->url() }}" class="block">
        <div class="relative overflow-hidden rounded-3xl bg-brown-100">
            <x-site.picture :media="$project->coverImage" :label="$project->title" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                :class="\Illuminate\Support\Arr::toCssClasses(['w-full transition duration-700 ease-out-soft group-hover:scale-105', 'aspect-[4/3]' => ! $large, 'aspect-[4/3] lg:aspect-[16/11]' => $large])" />
            <div class="absolute inset-0 bg-gradient-to-t from-brown-950/60 via-transparent to-transparent opacity-0 transition duration-500 group-hover:opacity-100"></div>
            <span class="absolute right-5 bottom-5 grid size-12 translate-y-3 place-items-center rounded-full bg-white text-brown-800 opacity-0 transition duration-500 group-hover:translate-y-0 group-hover:opacity-100">
                <x-lucide-arrow-up-right class="size-5" />
            </span>
        </div>
        <div class="mt-5">
            @if ($project->categories->isNotEmpty())
                <p class="text-xs font-semibold tracking-[0.15em] text-ochre-600 uppercase">{{ $project->categories->pluck('name')->join(' · ') }}</p>
            @endif
            <h3 class="mt-2 text-xl font-semibold transition group-hover:text-ochre-700">{{ $project->title }}</h3>
            @if ($project->excerpt)
                <p class="mt-2 line-clamp-2 text-ink/70">{{ $project->excerpt }}</p>
            @endif
        </div>
    </a>
</article>
