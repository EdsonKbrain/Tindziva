@props(['service', 'index' => null])

<a href="{{ $service->url() }}" {{ $attributes->class('reveal group relative flex h-full flex-col overflow-hidden rounded-3xl border border-brown-100 bg-white p-8 transition duration-500 ease-out-soft hover:-translate-y-1 hover:border-transparent hover:shadow-2xl hover:shadow-brown-900/10') }}>
    <div aria-hidden="true" class="bubble absolute -top-16 -right-16 size-40 bg-ochre-50 transition duration-500 group-hover:scale-125 group-hover:bg-ochre-100"></div>
    <div class="relative flex items-start justify-between">
        <span class="grid size-14 place-items-center rounded-2xl bg-brown-800 text-white transition duration-500 group-hover:bg-ochre-600">
            @svg('lucide-'.($service->icon ?: 'sparkles'), 'size-6')
        </span>
        @if ($index !== null)
            <span class="font-display text-sm font-semibold text-brown-200">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span>
        @endif
    </div>
    <h3 class="relative mt-8 text-xl font-semibold">{{ $service->name }}</h3>
    @if ($service->excerpt)
        <p class="relative mt-3 flex-1 leading-relaxed text-ink/70">{{ $service->excerpt }}</p>
    @endif
    <span class="relative mt-6 inline-flex items-center gap-2 font-display text-sm font-semibold text-ochre-700">
        {{ site_text('service_card_more') }} <x-lucide-arrow-right class="size-4 transition-transform duration-300 group-hover:translate-x-1" />
    </span>
</a>
