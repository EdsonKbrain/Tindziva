@props(['groups'])

<section id="clientes" class="bg-bubbles scroll-mt-24 bg-cream py-20 lg:py-28">
    <div class="container-site">
        <x-site.section-heading :eyebrow="site_text('clients_section_eyebrow')" :title="\App\Support\Text::accent(site_text('clients_section_title'))" />

        <div class="mt-14 space-y-14">
            @foreach ($groups as $segment => $clients)
                <div class="reveal">
                    <h3 class="flex items-center gap-4 font-display text-sm font-semibold tracking-[0.18em] text-ochre-700 uppercase">
                        {{ \App\Enums\ClientSegment::tryFrom($segment)?->label() ?? site_text('clients_other_segment') }}
                        <span class="h-px flex-1 bg-brown-200"></span>
                    </h3>
                    <ul class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($clients as $client)
                            <li class="flex flex-col rounded-3xl bg-white p-4 shadow-sm shadow-brown-900/5">
                                <x-site.client-logo :client="$client" class="border-0" />
                                <p class="mt-3 px-1 font-display text-[15px] leading-snug font-semibold text-brown-800">{{ $client->name }}</p>
                                @if ($client->services_provided)
                                    <ul class="mt-2 flex flex-wrap gap-1.5 px-1 pb-1" aria-label="Serviços prestados">
                                        @foreach ($client->services_provided as $service)
                                            <li class="rounded-full bg-ochre-50 px-2.5 py-0.5 text-xs text-ochre-800">{{ $service }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>
