@props(['eyebrow' => null, 'title', 'subtitle' => null, 'breadcrumbs' => [], 'image' => null])

{{-- Section opener modelled on the portfolio deck: an orange speech bubble over a dark photo or pattern --}}
<section class="bg-bubbles-dark relative overflow-hidden bg-brown-950">
    @if ($image?->isImage())
        <x-site.picture :media="$image" eager sizes="100vw" class="absolute inset-0 -z-10 size-full opacity-45" alt="" />
        <div aria-hidden="true" class="absolute inset-0 -z-10 bg-gradient-to-r from-brown-950/80 via-brown-950/40 to-transparent"></div>
    @else
        <div aria-hidden="true" class="bubble absolute -top-32 right-[-6rem] -z-10 size-[30rem] bg-brown-800/80"></div>
        <div aria-hidden="true" class="absolute right-16 bottom-16 -z-10 hidden gap-3 lg:flex">
            <span class="size-4 rounded-full bg-ochre-600/80"></span><span class="size-4 rounded-full bg-ochre-600/80"></span><span class="size-4 rounded-full bg-ochre-600/80"></span>
        </div>
    @endif

    <div class="container-site relative pt-10 pb-20 lg:pt-14 lg:pb-28">
        @if ($breadcrumbs)
            <nav aria-label="Navegação estrutural" class="mb-8">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm text-white/60">
                    @foreach ($breadcrumbs as $label => $url)
                        <li class="flex items-center gap-1.5">
                            @if (! $loop->last)
                                <a href="{{ $url }}" class="hover:text-white">{{ \App\Support\Text::plain($label) }}</a>
                                <x-lucide-chevron-right class="size-3.5" />
                            @else
                                <span aria-current="page" class="text-white">{{ \App\Support\Text::plain($label) }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        <div class="speech-panel reveal max-w-3xl bg-gradient-to-br from-ochre-700 via-ochre-600 to-ochre-700 px-7 py-12 shadow-2xl shadow-black/30 sm:px-12 lg:max-w-[46rem] lg:px-20 lg:py-24">
            @if ($eyebrow)
                <p class="font-display text-xs font-semibold tracking-[0.25em] text-ochre-100 uppercase">{{ $eyebrow }}</p>
            @endif
            <h1 class="mt-4 text-4xl leading-[1.05] font-semibold text-white sm:text-5xl lg:text-6xl [&_.accent-serif]:text-ochre-100">{!! $title !!}</h1>
            @if ($subtitle)
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-white/85">{{ $subtitle }}</p>
            @endif
            {{ $slot }}
        </div>
    </div>
</section>
